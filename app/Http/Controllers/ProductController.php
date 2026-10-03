<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    // =========================================================
    // 1. DISPLAY ALL PRODUCTS
    // =========================================================
    // =========================================================
    // 1. DISPLAY ALL PRODUCTS WITH PAGINATION
    // =========================================================
    public function index(Request $request)
    {
        // Fetch all categories for the filter dropdown
        $categories = Category::orderBy('category_name')->get();

        // Global counts for stat cards across all pages
        $totalProducts = Product::count();
        $lowStockCount = Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();

        // Build query with relationships
        $query = Product::with(['category', 'supplier']);

        // 1. Filter by Search Query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($catQuery) use ($search) {
                        $catQuery->where('category_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supplier', function ($supQuery) use ($search) {
                        $supQuery->where('supplier_name', 'like', "%{$search}%");
                    });
            });
        }

        // 2. Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 3. Filter by Status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'in_stock') {
                $query->where('stock_quantity', '>', 10);
            } elseif ($status === 'low_stock') {
                $query->where('stock_quantity', '>', 0)
                    ->where('stock_quantity', '<=', 10);
            } elseif ($status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Paginate results by 10 items per page and append current URL filters
        $products = $query->latest()->paginate(10)->withQueryString();

        return view('inventory.inventory-index', [
            'products'        => $products,
            'categories'      => $categories,
            'totalProducts'   => $totalProducts,
            'lowStockCount'   => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
        ]);
    }


    // =========================================================
    // 2. DISPLAY CREATE PRODUCT FORM
    // =========================================================
    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();

        // Point to the correct folder and file name: resources/views/inventory/add_product.blade.php
        return view('inventory.add_product', [
            'categories' => $categories,
            'suppliers'  => $suppliers,
        ]);
    }


    // =========================================================
    // 3. SAVE A NEW PRODUCT
    // =========================================================
    public function store(Request $request)
    {
        // Validate the information submitted by the user.
        //
        // Laravel checks these rules BEFORE saving the product.
        $request->validate([

            // Product name is required.
            // It must be text and cannot exceed 150 characters.
            'product_name' => 'required|string|max:150',

            // SKU is required.
            // It must be text.
            // It cannot exceed 50 characters.
            // It must be unique in the products table.
            'sku' => 'required|string|max:50|unique:products,sku',

            // category_id is required.
            // The category ID must exist in the categories table.
            'category_id' => 'required|exists:categories,id',

            // supplier_id is required.
            // The supplier ID must exist in the suppliers table.
            'supplier_id' => 'required|exists:suppliers,id',

            // Cost price is required.
            // It must be a number and cannot be negative.
            'cost_price' => 'required|numeric|min:0',

            // Selling price is required.
            // It must be a number and cannot be negative.
            'sell_price' => 'required|numeric|min:0',

            // Stock quantity is required.
            // It must be a whole number and cannot be negative.
            'stock_quantity' => 'required|integer|min:0',

            // Image is optional.
            // If provided, it must be an actual image file
            // (jpg, jpeg, png, or webp) and no larger than 2MB.
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);


        // Create a new Product object.
        //
        // At this point, we are preparing a new
        // product database record.
        $product = new Product();


        // Get the product name from the submitted form
        // and place it into the Product object.
        $product->product_name = $request->input('product_name');


        // Get the SKU from the form.
        $product->sku = $request->input('sku');


        // Get the selected category ID.
        //
        // This connects the product to a category.
        $product->category_id = $request->input('category_id');


        // Get the selected supplier ID.
        //
        // This connects the product to a supplier.
        $product->supplier_id = $request->input('supplier_id');


        // Get the product's cost price.
        $product->cost_price = $request->input('cost_price');


        // Get the product's selling price.
        $product->sell_price = $request->input('sell_price');


        // Get the starting/current stock quantity.
        $product->stock_quantity = $request->input('stock_quantity');


        // If the user uploaded a product image, store it in
        // storage/app/public/products and remember its path.
        if ($request->hasFile('image')) {
            $product->image_path = $request->file('image')->store('products', 'public');
        }


        // Save the Product object into the products table.
        $product->save();


        // After successfully saving:
        // redirect the user to the product list page.
        return redirect()
            ->route('products.create')

            // Display a success message.
            ->with('success', 'Product added successfully.');
    }


    // =========================================================
    // 4. DISPLAY ONE PRODUCT
    // =========================================================
    public function show($id)
    {
        // Find one product using its ID.
        //
        // Example:
        // /products/5
        //
        // Laravel searches for product ID 5.
        //
        // If the product does not exist, Laravel returns 404.
        $product = Product::findOrFail($id);


        // Load the category and supplier related to this product.
        //
        // This allows us to access things such as:
        // $product->category->category_name
        // $product->supplier->supplier_name
        $product->load('category', 'supplier');


        // Send the product to the show Blade view.
        return view('products.show', [
            'product' => $product
        ]);
    }


    // =========================================================
    // 5. DISPLAY EDIT PRODUCT FORM
    // =========================================================
    public function edit($id)
    {
        // Find the product and load its category and supplier relations
        $product = Product::with(['category', 'supplier'])->findOrFail($id);

        // Get all categories and suppliers for selection dropdowns
        $categories = Category::orderBy('category_name')->get();
        $suppliers  = Supplier::orderBy('supplier_name')->get();

        return view('inventory.edit', [
            'product'    => $product,
            'categories' => $categories,
            'suppliers'  => $suppliers,
        ]);
    }


    // =========================================================
    // 6. UPDATE AN EXISTING PRODUCT
    // =========================================================
    public function update(Request $request, $id)
    {
        // Find the existing product using its ID.
        $product = Product::findOrFail($id);


        // Validate the new information.
        $request->validate([

            // Product name is required.
            // It must be text and maximum 150 characters.
            'product_name' => 'required|string|max:150',


            // SKU must be required, text, and maximum 50 characters.
            //
            // Rule::unique() checks that the SKU is not already
            // being used by another product.
            //
            // ignore($product->id) means:
            // "Ignore this product's current SKU."
            //
            // This is important because when editing a product,
            // it is okay for it to keep its existing SKU.
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],


            // The selected category must exist.
            'category_id' => 'required|exists:categories,id',


            // The selected supplier must exist.
            'supplier_id' => 'required|exists:suppliers,id',


            // Cost price must be a number and cannot be negative.
            'cost_price' => 'required|numeric|min:0',


            // Selling price must be a number and cannot be negative.
            'sell_price' => 'required|numeric|min:0',


            // Stock quantity must be a whole number and cannot be negative.
            'stock_quantity' => 'required|integer|min:0',

            // Image is optional on update — the product keeps its
            // current image if no new file is uploaded.
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);


        // Update the product name.
        $product->product_name = $request->input('product_name');


        // Update the SKU.
        $product->sku = $request->input('sku');


        // Update the category.
        $product->category_id = $request->input('category_id');


        // Update the supplier.
        $product->supplier_id = $request->input('supplier_id');


        // Update the cost price.
        $product->cost_price = $request->input('cost_price');


        // Update the selling price.
        $product->sell_price = $request->input('sell_price');


        // Update the stock quantity.
        $product->stock_quantity = $request->input('stock_quantity');


        // If the user uploaded a new image, delete the old one
        // (if it exists) and store the new one instead.
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->image_path = $request->file('image')->store('products', 'public');
        }


        // Save all the changes to the database.
        $product->save();


        // After updating, return to the product list.
        return redirect()
            ->route('products.index')

            // Display a success message.
            ->with('success', 'Product updated successfully.');
    }


    // =========================================================
    // 7. DELETE A PRODUCT
    // =========================================================
    public function destroy($id)
    {
        // Find the product that we want to delete.
        //
        // If the product does not exist, Laravel returns 404.
        $product = Product::findOrFail($id);


        // Remove the product's image file from storage, if it has one.
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }


        // Delete the product from the products table.
        $product->delete();


        // After deleting the product,
        // return to the product list.
        return redirect()
            ->route('products.index')

            // Display a success message.
            ->with('success', 'Product deleted successfully.');
    }
}
