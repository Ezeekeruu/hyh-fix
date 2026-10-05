<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\DevicePhoto;
use App\Models\RepairStatusHistory;
use App\Models\ServiceType;
use App\Models\RepairTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RepairTicketController extends Controller
{
    // =========================================================
    // 1. DISPLAY ALL REPAIR TICKETS
    // =========================================================
    public function index(Request $request)
    {

        $query = RepairTicket::with([
            'device.customer',
            'assignedUser'
        ]);

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('service_type', 'like', "%{$search}%")

                    ->orWhereHas('device', function ($device) use ($search) {
                        $device->where('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('serial_or_imei', 'like', "%{$search}%");
                    })

                    ->orWhereHas('device.customer', function ($customer) use ($search) {
                        $customer->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }
        
        // Status Filter
        if (
            $request->filled('status') &&
            $request->status !== 'all'
        ) {
            $query->where('status', $request->status);
        }

        // Technician Filter
        if (
            $request->filled('technician') &&
            $request->technician !== 'all'
        ) {
            $query->where('assigned_to', $request->technician);
        }

        $repairTickets = $query
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $technicians = User::where('role', 'staff')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('repair.repair-management', [
            'repairs' => $repairTickets,
            'technicians' => $technicians,
        ]);
    }

    // =========================================================
    // 2. DISPLAY CREATE FORM
    // =========================================================
    public function create()
    {
        $users = User::where('role', 'staff')
            ->where('status', 'active')
            ->get();

        $serviceTypes = ServiceType::orderBy('name')->get();

        return view('repair.add-ticket', [
            'users' => $users,
            'serviceTypes' => $serviceTypes
        ]);
    }


    // =========================================================
    // 3. SAVE A NEW REPAIR TICKET
    // =========================================================
    public function store(Request $request)
    {
        // Validate request strictly adhering to business rules
        $request->validate([
            // Customer Info (*)
            'customer_name' => 'required|string|max:100',
            'phone_number'  => 'required|string|max:20',
            'address'       => 'nullable|string|max:255',

            // Device Info (*)
            'brand'          => 'required|string|max:50',
            'model'          => 'required|string|max:100',
            'serial_or_imei' => 'nullable|string|max:100',

            // Repair Info (*)
            'service_type'        => 'required|string|max:100',
            'problem_description' => 'nullable|string',
            'assigned_to'          => 'required|exists:users,id',
            'quotation_price'     => 'nullable|numeric|min:0',

            // Photos
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        DB::transaction(function () use ($request) {

            // 1. Find or create Customer
            // address is optional on the form but NOT NULL in the schema.
            $customer = Customer::firstOrCreate(
                ['phone' => $request->input('phone_number')],
                [
                    'name'    => $request->input('customer_name'),
                    'address' => $request->input('address') ?? '',
                ]
            );

            // 2. Register Device connected to Customer
            // serial_or_imei is optional on the form but NOT NULL in the schema.
            $device = new Device();
            $device->customer_id    = $customer->id;
            $device->brand          = $request->input('brand');
            $device->model          = $request->input('model');
            $device->serial_or_imei = $request->input('serial_or_imei') ?? '';
            $device->save();

            // 3. Create Repair Ticket
            $repairTicket = new RepairTicket();
            $repairTicket->device_id           = $device->id;
            $repairTicket->assigned_to         = $request->input('assigned_to');
            $repairTicket->service_type        = $request->input('service_type');
            $repairTicket->problem_description = $request->input('problem_description') ?? '';
            $repairTicket->quotation_price     = $request->input('quotation_price') ?? 0;
            $repairTicket->final_price         = $request->input('quotation_price') ?? 0;

            // Automatic Business Rules:
            $repairTicket->status        = 'pending';
            $repairTicket->date_received = now();
            $repairTicket->save();

            // 4. Initial Repair Status History
            $history = new RepairStatusHistory();
            $history->repair_ticket_id = $repairTicket->id;
            $history->status = 'pending';
            $history->changed_by = \Illuminate\Support\Facades\Auth::id() ?? \App\Models\User::first()?->id ?? 6;
            $history->changed_at = now();
            $history->save();

            // 5. Save Intake Photos (if uploaded)
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photoFile) {
                    $path = $photoFile->store('repair-photos', 'public');

                    $photo = new DevicePhoto();
                    $photo->repair_ticket_id = $repairTicket->id;
                    $photo->photo_path = $path;
                    $photo->photo_type = 'intake';
                    $photo->uploaded_by = \Illuminate\Support\Facades\Auth::id() ?? \App\Models\User::first()?->id ?? 6;
                    $photo->uploaded_at = now();
                    $photo->save();
                }
            }
        });

        return redirect()
            ->route('repair-tickets.index')
            ->with('success', 'Repair ticket registered successfully.');
    }


    // =========================================================
    // 4. DISPLAY ONE REPAIR TICKET
    // =========================================================
    public function show($id)
    {
        // Find one repair ticket using its ID.
        //
        // If the ID does not exist,
        // Laravel returns a 404 error.
        $repairTicket = RepairTicket::findOrFail($id);


        // Load related information.
        //
        // device.customer
        // = Get the device and its customer.
        //
        // assignedUser
        // = Get the staff member assigned to the repair.
        //
        // statusHistory.changedBy
        // = Get the status history and the user
        //   who changed each status.
        //
        // photos.uploadedBy
        // = Get the repair photos and the user
        //   who uploaded each photo.
        $repairTicket->load([
            'device.customer',
            'assignedUser',
            'statusHistory.changedBy',
            'photos.uploadedBy',
        ]);


        // Send the repair ticket and its related information
        // to the show Blade view.
        return view('repair.show-ticket', [
            'repairTicket' => $repairTicket
        ]);
    }


    // =========================================================
    // 5. DISPLAY EDIT FORM
    // =========================================================
    public function edit($id)
    {
        // Find the repair ticket that we want to edit.
        $repairTicket = RepairTicket::findOrFail($id);


        // Get all devices and their customers.
        //
        // These will be available in the edit form
        // for selecting a device.
        $devices = Device::with('customer')->get();


        // Get only active staff users.
        //
        // These users can be selected as
        // the assigned staff member.
        $users = User::where('role', 'staff')
            ->where('status', 'active')
            ->get();


        // Open the edit repair ticket form.
        //
        // Send the repair ticket, devices,
        // and staff users to the view.
        return view('repair.edit-ticket', [
            'repairTicket' => $repairTicket,
            'devices'      => $devices,
            'users'        => $users
        ]);
    }


    // =========================================================
    // 6. UPDATE A REPAIR TICKET
    // =========================================================
    public function update(Request $request, $id)
    {
        // Validate the updated information.
        $request->validate([

            // Device is required and must exist.
            'device_id' =>
            'required|exists:devices,id',


            // Assigned staff is optional.
            'assigned_to' =>
            'nullable|exists:users,id',


            // Problem description is required.
            'problem_description' =>
            'required|string',


            // Quotation price is optional
            // but cannot be negative.
            'quotation_price' =>
            'nullable|numeric|min:0',


            // Final price is optional
            // but cannot be negative.
            'final_price' =>
            'nullable|numeric|min:0',


            // Only these statuses are allowed.
            'status' =>
            'required|in:pending,in_progress,completed,cancelled',


            // Date received must be a valid date.
            'date_received' =>
            'required|date',


            // Completion date cannot be earlier
            // than the received date.
            'date_completed' =>
            'nullable|date|after_or_equal:date_received',
        ]);


        // Find the repair ticket that we want to update.
        $repairTicket = RepairTicket::findOrFail($id);


        // Save the OLD status before changing anything.
        //
        // Example:
        // oldStatus = "pending"
        //
        // We need this later to check whether
        // the status actually changed.
        $oldStatus = $repairTicket->status;


        // =====================================================
        // DATABASE TRANSACTION
        // =====================================================
        //
        // The ticket update and possible status-history
        // creation are treated as one database operation.
        DB::transaction(function () use (
            $request,
            $repairTicket,
            $oldStatus
        ) {


            // =================================================
            // UPDATE THE REPAIR TICKET
            // =================================================

            // Update the device.
            $repairTicket->device_id =
                $request->input('device_id');


            // Update the assigned staff member.
            $repairTicket->assigned_to =
                $request->input('assigned_to');


            // Update the problem description.
            $repairTicket->problem_description =
                $request->input('problem_description');


            // Update the quotation price.
            $repairTicket->quotation_price =
                $request->input('quotation_price');


            // Update the final price.
            $repairTicket->final_price =
                $request->input('final_price');


            // Update the status.
            $repairTicket->status =
                $request->input('status');


            // Update the date received.
            $repairTicket->date_received =
                $request->input('date_received');


            // Update the completion date.
            $repairTicket->date_completed =
                $request->input('date_completed');


            // Save all changes to the database.
            $repairTicket->save();


            // =================================================
            // CHECK IF STATUS CHANGED
            // =================================================

            // Compare the old status with the new status.
            //
            // Example:
            //
            // Old status = pending
            // New status = in_progress
            //
            // They are different, so a history record
            // should be created.
            if ($oldStatus !== $repairTicket->status) {


                // Create a new status history record.
                $history = new RepairStatusHistory();


                // Connect the history to the repair ticket.
                $history->repair_ticket_id =
                    $repairTicket->id;


                // Store the NEW status.
                $history->status =
                    $repairTicket->status;


                // Store who changed the status.
                $history->changed_by =
                    \Illuminate\Support\Facades\Auth::id() ?? \App\Models\User::first()?->id ?? 6;


                // Store when the status was changed.
                $history->changed_at =
                    now();


                // Save the status history.
                $history->save();
            }
        });


        // After successfully updating,
        // return to the repair ticket list.
        return redirect()
            ->route('repair-tickets.index')

            // Display a success message.
            ->with(
                'success',
                'Repair ticket updated successfully.'
            );
    }


    // =========================================================
    // 7. DELETE A REPAIR TICKET
    // =========================================================
    public function destroy($id)
    {
        // Find the repair ticket we want to delete.
        //
        // If it does not exist,
        // Laravel returns a 404 error.
        $repairTicket = RepairTicket::findOrFail($id);


        // Delete the repair ticket from the database.
        $repairTicket->delete();


        // After deleting,
        // return to the repair ticket list.
        return redirect()
            ->route('repair-tickets.index')

            // Display a success message.
            ->with(
                'success',
                'Repair ticket deleted successfully.'
            );
    }

}
