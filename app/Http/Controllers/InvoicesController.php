<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\SaveInvoice;
use App\Http\Requests\MarkInvoicePaid;
use App\Invoice;
use App\WorkOrder;
use App\Job;
use App\JobPart;
use App\BusSetting;

class InvoicesController extends Controller
{
	/** 
	 * Filters the invoice table based on passed params.
	 * 
	 * @return Json Collection
	*/	
    public function filter($from_date = false, $to_date = false, $is_paid = false, $customer_id = false)
    {
    	// Default state for date (single date)
    	$date = false;
    	// If only one of from_date or to_date is set then the filter wants a specific single date
    	if($from_date && !$to_date){
    		// Cache date
    		$date = $from_date;
    	} elseif($to_date && !$from_date){
    		// Cache date
    		$date = $to_date;
    	}

		// Possible where fields for the filter
		$whereFields = [
			['filter' => $date, 'field' => 'date', 'value' => $date, 'conditional' => '='],
			['filter' => $is_paid, 'field' => 'is_paid', 'value' => $is_paid, 'conditional' => '='],
			['filter' => $customer_id, 'field' => 'customer_id', 'value' => $customer_id, 'conditional' => '=']
		];

		// Default value for whereBetweenFields
		$whereBetweenFields = false;
		// Set where between values if dates present
		if($from_date && $to_date){
			// Possible where between fields for the filter
			$whereBetweenFields = [
				'first' => ['field' => 'date', 'value' => $from_date],
				'second' => ['field' => 'date', 'value' => $to_date]
			];			
		}

		return $this->genericFilter(
			Invoice::with([
	    		'customer', 
	    		'vehicle'
	    	])->orderBy('created_at', 'asc'), 
	    	$whereFields, 
	    	$whereBetweenFields
		);    	
    }

	/** 
	 * Get an invoice based on ID.
	 *
	 * @param $id - The ID of the invoice
	 * @return Json App\Invoice - The requested invoice
	*/    
    public function get($id)
    {
    	return Invoice::with(
    		'customer', 
    		'vehicle',
    		'work_order', 
    		'work_order.jobs', 
    		'work_order.jobs.parts', 
    		'work_order.jobs.parts.supplier'
    	)->findOrFail($id);
    }

	/** 
	 * Create a new invoice in the db associated with a work order. Updates the work order status.
	 *
	 * @param $request - SaveInvoice custom request 
	 * @return Json App\Invoice - The created Invoice
	*/
    public function create(SaveInvoice $request)
    {
    	// Get the work order first because we need the customer ID
    	$wo = WorkOrder::with(['vehicle', 'customer', 'jobs', 'jobs.parts'])->findOrFail($request->work_order_id);

    	// Ensure the work order has jobs (cannot create an invoice from a work order with no jobs)
    	if($wo->jobs->isNotEmpty()){
	    	// Get the current business settings
	    	$bus_settings = BusSetting::find(1);
	    	// Start the invoice
	    	$invoice = new Invoice;
	    	// Assign basic invoice values
	    	$invoice->created_by = Auth::user()->name;
	    	$invoice->date = date('Y-m-d');
	    	$invoice->work_order_id = $wo->id;
	    	$invoice->vehicle_id = $wo->vehicle_id;
	    	$invoice->customer_id = $wo->customer_id;
	    	$invoice->gst_rate = $bus_settings->gst_rate;
	    	$invoice->shop_supply_rate = $bus_settings->shop_supply_rate;

	    	// Start totals
	    	$total_labour = 0;
	    	$total_labour_cost = 0;
	    	$total_parts = 0;
	    	$total_parts_cost = 0;

	    	// Calculate labour / parts totals
	    	forEach($wo->jobs as $job){
	    		$total_labour += floatval($job->job_labour_total);
	    		$total_labour_cost += floatval($job->tech_pay_total);
	    		$total_parts += floatval($job->parts_total_billed);
	    		$total_parts_cost += floatval($job->parts_total_cost);
	    	}

	    	// Calculate final totals
	    	$sub_total = floatval($total_labour) + floatval($total_parts);
	    	$gst_total = floatval($sub_total) * floatval($invoice->gst_rate);
	    	$grand_total = floatval($sub_total) + floatval($gst_total) + floatval($invoice->shop_supply_rate);

	    	// Cache totals in invoice
	    	$invoice->total_labour = round($total_labour, 3);
	    	$invoice->total_labour_cost = round($total_labour_cost, 3);
	    	$invoice->total_parts = round($total_parts, 3);
	    	$invoice->total_parts_cost = round($total_parts_cost, 3);
	    	$invoice->sub_total = round($sub_total, 3);
	    	$invoice->gst_total = round($gst_total, 3);
	    	$invoice->grand_total = round($grand_total, 3);

	    	// Save invoice
	    	$invoice = $this->genericSave($invoice);
	    	// Load invoice relationships
	    	$invoice->load(    		
		    	'customer', 
	    		'vehicle',
	    		'work_order', 
	    		'work_order.jobs', 
	    		'work_order.jobs.parts', 
	    		'work_order.jobs.parts.supplier'
	    	);

	    	// Mark all jobs on work order as complete
	    	forEach($wo->jobs as $job){
	    		// Only mark job as complete if it's not already marked as such
	    		if($job->is_complete != 1){
	    			// Mark complete
		    		$job->is_complete = 1;
		    		// Save job
		    		$this->genericSave($job);	    			
	    		}
	    	}

	    	// Update invoice info on work order 
	    	$wo->is_invoiced = 1;
	    	$wo->invoice_id = $invoice->id;
	    	// Save updated work order
	    	$this->genericSave($wo);

	    	return $invoice;    		
    	} else {
    		// Work order has no jobs. Failed response
    		return response()->json([
    			'result' => 'error',
    			'message' => 'This work order has no jobs so an invoiced cannot be created'
    		], 422);
    	}

    }

	/** 
	 * Marks an invoice as paid and sets the payment method and date.
	 * Once the invoice is marked paid the related work order and the invoice become permanent and cannot be removed or modified.
	 *
	 * @param $request - MakrInvoicePaid custom request 
	 * @return Json App\Invoice - The updated invoice
	*/
    public function markPaid(MarkInvoicePaid $request)
    {
    	// Find invoice
    	$invoice = Invoice::with(
    		'customer', 
    		'vehicle',
    		'work_order', 
    		'work_order.jobs', 
    		'work_order.jobs.parts', 
    		'work_order.jobs.parts.supplier'
    	)->findOrFail($request->id);
    	// Setup payment info 
    	$invoice->is_paid = 1;
    	$invoice->payment_method = $request->payment_method;
    	// For timestamp
		$date = new \DateTime();
		// Set timestamp
    	$invoice->paid_on = $date->format('Y-m-d H:i:s');

    	return $this->genericSave($invoice);
    }

	/** 
	 * Get all invoices associated with the supplied customer ID
	 *
	 * @param $id - The ID of the customer
	 * @return Json Collection - The invoices associated with the customer
	*/
    public function getCustomers($id)
    {
    	return Invoice::where('customer_id', '=', $id)->get();
    }

	/** 
	 * Get all invoices associated with the supplied vehicle ID
	 *
	 * @param $id - The ID of the vehicle
	 * @return Json Collection - The invoices associated with the vehicle
	*/
    public function getVehicles($id)
    {
    	return Invoice::where('vehicle_id', '=', $id)->get();
    }

	/** 
	 * Removes an invoice from the db. Can only remove an invoice not marked as paid.
	 *
	 * @param $id The id of the invoice to remove 
	 * @return Int - The id of the removed invoice
	*/
    public function remove($id)
    {
    	// Find invoice
    	$invoice = Invoice::findOrFail($id);

    	// Can only remove an invoice that has not been paid
    	if(! $invoice->is_paid){
    		// Now find the related work order so we can reset the is_invoiced values
    		$wo = WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($invoice->work_order_id);

    		// Update work order
    		$wo->is_invoiced = 0;
    		$wo->invoice_id = null;
    		// Save the work order
    		$wo = $this->genericSave($wo);
    		// Delete the invoice
    		$id = $this->genericRemove($invoice);
    		
    		return $wo;

    	} else {
    		// Invoice IS PAID. Cannot remove
    		return response()->json([
    			'result' => 'error',
    			'message' => 'This invoice has been paid and cannot be removed.'
    		], 422);    		
    	}
    }
}
