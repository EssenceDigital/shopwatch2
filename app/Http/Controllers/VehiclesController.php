<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

Use App\Http\Requests\SaveVehicle;

Use App\Vehicle;
Use App\Customer;

class VehiclesController extends Controller
{
	public function search($vin)
	{

		// Possible where fields for the filter
		$whereFields = [
			['filter' => $vin, 'field' => 'vin', 'value' => "%{$vin}%", 'conditional' => 'like']
		];

		return $this->genericFilter(Vehicle::with(['customer'])->orderBy('year', 'asc'), $whereFields);
	}

	/**
	 * Filters the vehicle table based on passed params.
	 *
	 * @return Json Collection
	*/
	public function filter($year = false, $make = false, $model = false, $plate_number = false)
	{
		// Possible where fields for the filter
		$whereFields = [
			['filter' => $year, 'field' => 'year', 'value' => $year, 'conditional' => '='],
			['filter' => $make, 'field' => 'make', 'value' => "%{$make}%", 'conditional' => 'like'],
			['filter' => $model, 'field' => 'model', 'value' => "%{$model}%", 'conditional' => 'like'],
			['filter' => $plate_number, 'field' => 'plate_number', 'value' => "%{$plate_number}%", 'conditional' => 'like']
		];

		return $this->genericFilter(Vehicle::with(['work_orders'])->orderBy('year', 'asc'), $whereFields);
	}

	/**
	 * Get a vehicle based on ID.
	 *
	 * @param $id - The ID of the vehicle
	 * @return Json App\Vehicle - The requested vehicle
	*/
	public function get($id)
	{
		return Vehicle::with(['customer', 'work_orders'])->findOrFail($id);
	}

	/**
	 * Create a new vehicle in the db associated with a customer.
	 *
	 * @param $request - SaveVehicle custom request
	 * @return Json App\Vehicle - The created vehicle
	*/
    public function create(SaveVehicle $request)
    {
    	$vehicle = $this->genericSave(new Vehicle, $request);
			// Load customer
			$vehicle->load('customer');

    	return $vehicle;
    }

	/**
	 * Updates a vehicle in the db.
	 *
	 * @param $request - SaveVehicle custom request
	 * @return Json App\Vehicle - The updated vehicle
	*/
    public function update(SaveVehicle $request)
    {
    	$vehicle = $this->genericSave(Vehicle::findOrFail($request->id), $request);

    	return Customer::with(['vehicles'])->findOrFail($vehicle->customer_id);
    }

	/**
	 * Removes a vehicle from the db.
	 *
	 * @param $id The id of the vehicle to remove
	 * @return Int - The id of the removed vehicle
	*/
    public function remove($id)
    {
    	// Find the vehicle
    	$vehicle = Vehicle::with(['work_orders'])->findOrFail($id);

    	// Cache customer ID
    	$customer_id = $vehicle->customer_id;

    	// If the vehicle has work orders it cannot be removed
    	if(count($vehicle->work_orders) > 0){
    		// Failed response
	        return response()->json([
	            'result' => 'error',
	            'message' => 'Vehicle with work orders cannot be removed.'
	        ], 422);
    	}

    	$id = $this->genericRemove($vehicle);

			return Customer::with(['vehicles'])->findOrFail($customer_id);
    }
}
