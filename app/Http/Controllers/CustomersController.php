<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

Use App\Http\Requests\SaveCustomer;

Use App\Customer;

class CustomersController extends Controller
{
	public function all()
	{
			return Customer::select('id', 'first', 'last', 'phone_one', 'phone_two')->get();
	}

	/**
	 * Filters the cusomter table based on passed params.
	 *
	 * @param $first - The customer first name
	 * @param $last - The customer last name
	 * @return Json Collection
	*/
	public function filter($first = false, $last = false)
	{
		// Possible where fields for the filter
		$whereFields = [
			['filter' => $first, 'field' => 'first', 'value' => "%{$first}%", 'conditional' => 'like'],
			['filter' => $last, 'field' => 'last', 'value' => "%{$last}%", 'conditional' => 'like']
		];

		return $this->genericFilter(Customer::with(['vehicles'])->orderBy('last', 'asc'), $whereFields);
	}

	/**
	 * Get a customer based on ID.
	 *
	 * @param $id - The ID of the customer
	 * @return Json App\Customer - The requested customer
	*/
	public function get($id)
	{
		return Customer::with(['vehicles', 'vehicles.work_orders'])->findOrFail($id);
	}

	/**
	 * Create a new customer in the db.
	 *
	 * @param $request - SaveCustomer custom request
	 * @return Json App\Customer - The created customer
	*/
    public function create(SaveCustomer $request)
    {
    	return $this->genericSave(new Customer, $request);
    }

	/**
	 * Updates a customer in the db.
	 *
	 * @param $request - SaveCustomer custom request
	 * @return Json App\Customer - The updated customer
	*/
    public function update(SaveCustomer $request)
    {
    	return $this->genericSave(Customer::with(['vehicles'])->findOrFail($request->id), $request);
    }

	/**
	 * Removes a customer from the db.
	 *
	 * @param $id The id of the customer to remove
	 * @return Int - The id of the removed customer
	*/
    public function remove($id)
    {
    	// Get the customer
    	$customer = Customer::with(['vehicles'])->findOrFail($id);

    	// If the customer has related vehicles it may not be removed
    	if(count($customer->vehicles) > 0){
    		// Failed response
	        return response()->json([
	            'result' => 'error',
	            'message' => 'Customer with vehicles cannot be removed.'
	        ], 422);
    	}

    	return $this->genericRemove($customer);
    }
}
