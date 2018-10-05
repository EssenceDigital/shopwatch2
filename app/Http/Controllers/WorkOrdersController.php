<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Requests\SaveWorkOrder;
use App\WorkOrder;
use App\Vehicle;
Use Auth;

class WorkOrdersController extends Controller
{

	/** 
	 * Filters the work order table based on passed params.
	 * 
	 * @return Json Collection
	*/
	public function filter($from_date = false, $to_date = false, $is_invoiced = false, $customer_id = false)
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
			['filter' => $is_invoiced, 'field' => 'is_invoiced', 'value' => $is_invoiced, 'conditional' => '='],
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
			WorkOrder::with([
				'customer', 
				'vehicle', 
				'jobs', 
				'jobs.parts', 
				'jobs.parts.supplier'
			])->orderBy('created_at', 'asc'), 
			$whereFields, 
			$whereBetweenFields
		);			
	}

	/** 
	 * Get a work order based on ID.
	 *
	 * @param $id - The ID of the work order
	 * @return Json App\WorkOrder - The requested work order
	*/
	public function get($id)
	{
		return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($id);
	}

	/** 
	 * Get all work orders associated with the supplied customer ID
	 *
	 * @param $id - The ID of the customer
	 * @return Json Collection - The work orders associated with the customer
	*/
	public function getCustomers($id)
	{
		// Get all work orders first
		return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->where('customer_id', $id)->get();		
	}

	/** 
	 * Get all work orders associated with the supplied vehicle ID
	 *
	 * @param $id - The ID of the vehicle
	 * @return Json Collection - The work orders associated with the vehicle
	*/
	public function getVehicles($id)
	{
		return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->where('vehicle_id', $id)->get();
	}

	/** 
	 * Create a new work order in the db associated with a vehicle.
	 *
	 * @param $request - SaveWorkOrder custom request 
	 * @return Json App\WorkOrder - The created work order
	*/
    public function create(SaveWorkOrder $request)
    {
    	// Get the vehicle so we have the customer ID
    	$vehicle = Vehicle::findOrFail($request->vehicle_id);
    	// Merge the customer ID into the request
    	$request->merge(['customer_id' => $vehicle->customer_id]);
    	// Merge authenticated user with request
    	$request->merge(['created_by' => Auth::user()->name]);
    	// Merge date
    	$request->merge(['date' => date('Y-m-d')]);
    	// Save WO
    	$wo = $this->genericSave(New WorkOrder, $request);
    	// Load relationships
    	$wo->load('customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier');

    	return $wo;
    }

	/** 
	 * Removes a work order from the db.
	 *
	 * @param $id The id of the work order to remove 
	 * @return Int - The id of the removed work order
	*/
    public function remove($id)
    {
    	// Find the work order
    	$wo = WorkOrder::with(['jobs'])->findOrFail($id);

    	// If the work order has jobs it cannot be removed
    	if(count($wo->jobs) > 0 || $wo->invoice_id != null){
    		// Failed response
	        return response()->json([
	            'result' => 'error',
	            'message' => 'This work order has been closed (invoiced) or there are jobs associated with it and cannot be removed.'
	        ], 422);      		
    	}

    	return $this->genericRemove($wo);    	
    }
}
