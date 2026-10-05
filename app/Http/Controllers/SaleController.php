<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class SaleController extends Controller
{
    // =========================================================
    // 1. DISPLAY ALL SALES
    // =========================================================
    public function index()
    {
        // No dedicated sales list page: the transaction history
        // (SaleController@transactionHistory) is the canonical list.
        return redirect()->route('transaction.history');
    }


    // =========================================================
    // 2. DISPLAY CREATE SALE FORM (POS Screen)
    // =========================================================
    public function create()
    {
        // Get all customers sorted alphabetically
        $customers = Customer::orderBy('name')->get();

        // Get all categories for the POS dropdown filter
        $categories = Category::orderBy('category_name')->get();

        // Get available products with their category relationship loaded
        $products = Product::with('category')
            ->where('stock_quantity', '>', 0)
            ->orderBy('product_name')
            ->get();

        // Open the POS page and send customers, categories, and products
        return view('pos.pos-index', [
            'customers'  => $customers,
            'categories' => $categories, // <--- PASS CATEGORIES HERE
            'products'   => $products,
        ]);
    }


    // =========================================================
    // 3. SAVE A NEW SALE
    // =========================================================
    public function store(Request $request)
    {
        // Validate the information submitted by the POS form.
        $request->validate([

            // Customer is optional.
            //
            // If a customer ID is provided,
            // that ID must exist in the customers table.
            'customer_id' =>
            'nullable|exists:customers,id',


            // Payment method is required.
            // Example:
            // Cash, GCash, Card
            'payment_method' =>
            'required|string|max:50',


            // Products must be provided.
            // It must be an array.
            // At least one product must be included.
            'products' =>
            'required|array|min:1',


            // Every product in the array must have
            // a valid product ID.
            'products.*.id' =>
            'required|exists:products,id',


            // Every product must have a quantity.
            // Quantity must be at least 1.
            'products.*.quantity' =>
            'required|integer|min:1',
        ]);


        // =====================================================
        // DATABASE TRANSACTION
        // =====================================================
        //
        // The sale, sale items, and stock changes
        // should happen together.
        //
        // If something fails inside this transaction,
        // Laravel can roll back the database changes.
        DB::transaction(function () use ($request) {


            // =================================================
            // CREATE THE SALE
            // =================================================

            // Create a new Sale object.
            $sale = new Sale();


            // Get the selected customer ID.
            $sale->customer_id =
                $request->input('customer_id');


            // Get the ID of the currently logged-in user.
            //
            // This records which staff/user processed the sale.
            $sale->user_id = Auth::id() ?? \App\Models\User::first()?->id ?? 6;


            // Store the current date and time.
            $sale->sale_date =
                now();


            // Start the total at 0.
            //
            // We will calculate the real total
            // after processing the products.
            $sale->total_amount = 0;


            // Store the selected payment method.
            $sale->payment_method =
                $request->input('payment_method');


            // New sales are automatically marked
            // as completed.
            $sale->status = 'completed';


            // Save the sale into the database.
            //
            // After save(), the sale gets its ID.
            $sale->save();


            // =================================================
            // PREPARE TO CALCULATE TOTAL
            // =================================================

            // Start the total amount at zero.
            $totalAmount = 0;


            // Get the products submitted by the POS form.
            //
            // Example:
            //
            // [
            //     ['id' => 1, 'quantity' => 2],
            //     ['id' => 5, 'quantity' => 1]
            // ]
            $productsInput =
                $request->input('products');


            // Loop through every product selected
            // in the sale.
            //
            // $index = position of the product in the array.
            // $item = product ID and quantity.
            foreach ($productsInput as $index => $item) {


                // =================================================
                // GET PRODUCT AND LOCK ITS DATABASE ROW
                // =================================================

                // Find the product by ID.
                //
                // lockForUpdate() temporarily locks the product
                // database row while this transaction is running.
                //
                // This helps prevent two transactions from
                // changing the same stock at the same time.
                $product = Product::lockForUpdate()
                    ->findOrFail($item['id']);


                // =================================================
                // CHECK AVAILABLE STOCK
                // =================================================

                // Check if the available stock is less than
                // the quantity the customer wants to buy.
                if ($product->stock_quantity < $item['quantity']) {


                    // Stop the transaction and create
                    // a validation error.
                    //
                    // Example:
                    // Available: 2
                    // Requested: 5
                    //
                    // The sale cannot continue.
                    throw ValidationException::withMessages([

                        // Point the error to the quantity
                        // field of this product.
                        "products.{$index}.quantity" => [

                            // Show the user how much stock
                            // is available.
                            "Not enough stock available for {$product->product_name}. Available: {$product->stock_quantity}, Requested: {$item['quantity']}."
                        ]
                    ]);
                }


                // =================================================
                // CALCULATE SUBTOTAL
                // =================================================

                // Calculate:
                //
                // product price × quantity
                //
                // Example:
                // ₱100 × 3 = ₱300
                $subtotal =
                    $product->sell_price * $item['quantity'];


                // =================================================
                // CREATE SALE ITEM
                // =================================================

                // Create a new SaleItem object.
                //
                // SaleItem represents one product
                // inside a sale.
                $saleItem = new SaleItem();


                // Connect the sale item to the sale.
                $saleItem->sale_id =
                    $sale->id;


                // Connect the sale item to the product.
                $saleItem->product_id =
                    $product->id;


                // Save how many units were purchased.
                $saleItem->quantity =
                    $item['quantity'];


                // Save the product price at the time of sale.
                //
                // This is important because the product's
                // current price could change later.
                $saleItem->unit_price =
                    $product->sell_price;


                // Save the subtotal.
                $saleItem->subtotal =
                    $subtotal;


                // Save the sale item to the database.
                $saleItem->save();


                // =================================================
                // REDUCE PRODUCT STOCK
                // =================================================

                // Subtract the purchased quantity
                // from the current stock.
                //
                // Example:
                // Stock = 10
                // Customer buys = 3
                // New stock = 7
                $product->stock_quantity =
                    $product->stock_quantity - $item['quantity'];


                // Save the new stock quantity.
                $product->save();


                // =================================================
                // ADD SUBTOTAL TO TOTAL
                // =================================================

                // Add this product's subtotal
                // to the total sale amount.
                //
                // Example:
                // Product 1 = ₱300
                // Product 2 = ₱200
                // Total = ₱500
                $totalAmount =
                    $totalAmount + $subtotal;
            }


            // =================================================
            // SAVE FINAL TOTAL
            // =================================================

            // After all products have been processed,
            // save the calculated total to the sale.
            $sale->total_amount =
                $totalAmount;


            // Save the updated total.
            $sale->save();
        });


        // After the transaction succeeds,
        // return to the sales list.
        return redirect()
            ->route('pos.index')

            // Display a success message.
            ->with(
                'success',
                'Sale added successfully.'
            );
    }


    // =========================================================
    // 4. DISPLAY ONE SALE
    // =========================================================
    public function show($id)
    {
        // Find one sale using its ID.
        //
        // Also load:
        // customer
        // user
        // sale items
        // products inside those sale items
        $sale = Sale::with([
            'customer',
            'user',
            'saleItems.product'
        ])
            ->findOrFail($id);


        // Open the sale details page.
        return view('transactions.transaction-receipt', [
            'sale' => $sale
        ]);
    }


    // =========================================================
    // 5. DISPLAY EDIT SALE FORM
    // =========================================================
    public function edit($id)
    {
        // No dedicated edit page: send back to the sale's receipt.
        return redirect()->route('sales.show', $id);
    }


    // =========================================================
    // 6. UPDATE AN EXISTING SALE
    // =========================================================
    public function update(Request $request, $id)
    {
        // Validate the updated information.
        $request->validate([

            // Customer is optional.
            'customer_id' =>
            'nullable|exists:customers,id',


            // Payment method is required.
            'payment_method' =>
            'required|string|max:50',


            // Only these statuses are allowed (matches the sales.status enum).
            'status' =>
            'required|string|in:completed,voided',
        ]);


        // Use a database transaction because
        // updating the sale can also change product stock.
        DB::transaction(function () use ($request, $id) {


            // Find the sale and lock it during the transaction.
            //
            // This prevents another transaction from
            // modifying the same sale at the same time.
            $sale = Sale::lockForUpdate()
                ->findOrFail($id);


            // Get the new status from the form.
            $newStatus =
                $request->input('status');


            // =================================================
            // RETURN STOCK IF SALE IS VOIDED
            // =================================================

            // Check whether:
            //
            // 1. The old sale status is completed
            // AND
            // 2. The new status is voided
            //
            // If both are true, return the sold products
            // back into inventory.
            if (
                $sale->status === 'completed'
                &&
                $newStatus === 'voided'
            ) {


                // Go through every product in the sale.
                foreach ($sale->saleItems as $item) {


                    // Find the product and lock its row.
                    $product = Product::lockForUpdate()
                        ->findOrFail($item->product_id);


                    // Add the sold quantity back to stock.
                    //
                    // Example:
                    // Current stock = 5
                    // Returned quantity = 2
                    // New stock = 7
                    $product->stock_quantity =
                        $product->stock_quantity + $item->quantity;


                    // Save the restored stock.
                    $product->save();
                }
            }


            // =================================================
            // UPDATE SALE INFORMATION
            // =================================================

            // Update the customer.
            $sale->customer_id =
                $request->input('customer_id');


            // Update the payment method.
            $sale->payment_method =
                $request->input('payment_method');


            // Update the status.
            $sale->status =
                $newStatus;


            // Save the changes.
            $sale->save();
        });


        // Return to the transaction history (the canonical sales list).
        return redirect()
            ->route('transaction.history')

            // Display a success message.
            ->with(
                'success',
                'Sale updated successfully.'
            );
    }


    // =========================================================
    // 7. DELETE A SALE
    // =========================================================
    public function destroy($id)
    {
        // Use a transaction because deleting a sale
        // can also affect product stock.
        DB::transaction(function () use ($id) {


            // Find the sale and lock it.
            $sale = Sale::lockForUpdate()
                ->findOrFail($id);


            // =================================================
            // RETURN STOCK
            // =================================================

            // If the sale was completed,
            // return the sold products to inventory
            // before deleting the sale.
            if ($sale->status === 'completed') {


                // Go through each product in the sale.
                foreach ($sale->saleItems as $item) {


                    // Find and lock the product.
                    $product = Product::lockForUpdate()
                        ->findOrFail($item->product_id);


                    // Add the sold quantity back to stock.
                    $product->stock_quantity =
                        $product->stock_quantity + $item->quantity;


                    // Save the restored stock.
                    $product->save();
                }
            }


            // =================================================
            // DELETE SALE ITEMS
            // =================================================

            // Delete all sale items belonging to this sale.
            //
            // SaleItem records must be removed because
            // they belong to the sale being deleted.
            $sale->saleItems()->delete();


            // =================================================
            // DELETE SALE
            // =================================================

            // Finally delete the sale itself.
            $sale->delete();
        });


        // After deleting the sale,
        // return to the transaction history.
        return redirect()
            ->route('transaction.history')

            // Display a success message.
            ->with(
                'success',
                'Sale deleted successfully.'
            );
    }

    public function transactionHistory(Request $request)
    {
        $query = Sale::with([
            'customer',
            'saleItems.product'
        ]);

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // Transaction ID
                $q->where('id', 'like', "%{$search}%")

                    // Customer
                    ->orWhereHas('customer', function ($customer) use ($search) {

                        $customer->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })

                    // Product Name or SKU
                    ->orWhereHas('saleItems.product', function ($product) use ($search) {

                        $product->where('product_name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        // Payment Method Filter
        if (
            $request->filled('payment_method')
            &&
            $request->payment_method !== 'all'
        ) {

            $query->whereRaw(
                'LOWER(payment_method) = ?',
                [strtolower($request->payment_method)]
            );
        }

        // Status Filter
        if (
            $request->filled('status')
            &&
            $request->status !== 'all'
        ) {

            $query->where(
                'status',
                $request->status
            );
        }

        // Date Filter
        if ($request->date === 'today') {

            $query->whereDate('sale_date', today());
        } elseif ($request->date === 'this_week') {

            $query->whereBetween('sale_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]);
        } elseif ($request->date === 'this_month') {

            $query->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year);
        } elseif ($request->date === 'custom') {

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('sale_date', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            } elseif ($request->filled('start_date')) {
                $query->whereDate('sale_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->whereDate('sale_date', '<=', $request->end_date);
            }
        }

        $sales = $query
            ->latest('sale_date')
            ->paginate(10)
            ->withQueryString();

        return view(
            'transactions.transactions-index',
            compact('sales')
        );
    }
}
