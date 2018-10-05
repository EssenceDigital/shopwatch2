<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\SaveSupplier;
Use App\Supplier;

class SuppliersController extends Controller
{

    /** 
     * Get all suppliers from the db
     *
     * @return Json Collection - The suppliers
    */    
	public function all()
	{
		return Supplier::all();
	}

    /** 
     * Create a new supplier in the db. 
     *
     * @param $request - SaveSupplier custom request 
     * @return Json App\Supplier - The created supplier
    */
    public function create(SaveSupplier $request)
    {
    	return $this->genericSave(new Supplier, $request);
    }

    /** 
     * Update a new supplier in the db. 
     *
     * @param $request - SaveSupplier custom request 
     * @return Json App\Supplier - The updated supplier
    */
    public function update(SaveSupplier $request)
    {
    	return $this->genericSave(Supplier::findOrFail($request->id), $request);
    }
}
