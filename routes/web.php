<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/*
 * General app routes
*/
Route::get('/app', function () {
    return view('dashboard');
});

Route::get('/bus-config', 'BusConfigController@get');
Route::post('/bus-config/update', 'BusConfigController@update');

/*
 * User routes
*/
Route::get('/users', 'UsersController@all');
Route::get('/users/get-auth', 'UsersController@getAuth');
Route::post('/users/create', 'UsersController@create');
Route::post('/users/update', 'UsersController@update');
Route::post('/users/change-password', 'UsersController@changePassword');
Route::post('/users/change-auth-password', 'UsersController@changeAuthPassword');
Route::delete('/users/{id}/remove', 'UsersController@remove');
Route::get('/users/{id}', 'UsersController@get');

/*
 * Supplier routes
*/
Route::get('/suppliers', 'SuppliersController@all');
Route::post('/suppliers/create', 'SuppliersController@create');
Route::post('/suppliers/update', 'SuppliersController@update');

/*
 * Customer routes
*/
Route::get('/customers/{id}/fetch', 'CustomersController@get');
Route::get('/customers/all', 'CustomersController@all');
Route::get('/customers/{id}/work-orders', 'WorkOrdersController@getCustomers');
Route::get('/customers/{id}/invoices', 'InvoicesController@getCustomers');
Route::get('/customers/{first?}/{last?}', 'CustomersController@filter');
Route::post('/customers/create', 'CustomersController@create');
Route::post('/customers/update', 'CustomersController@update');
Route::delete('/customers/{id}/remove', 'CustomersController@remove');

/*
 * Vehicle Routes
*/
Route::get('/vehicles/{vin}/search', 'VehiclesController@search');
Route::get('/vehicles/{id}/work-orders', 'WorkOrdersController@getVehicles');
Route::get('/vehicles/{id}/invoices', 'InvoicesController@getVehicles');
Route::get('/vehicles/{year}/{make}/{model}/{plate_number}', 'VehiclesController@filter');
Route::post('/vehicles/create', 'VehiclesController@create');
Route::post('/vehicles/update', 'VehiclesController@update');
Route::delete('/vehicles/{id}/remove', 'VehiclesController@remove');
Route::get('/vehicles/{id}', 'VehiclesController@get');

/*
 * Work order routes
*/
Route::get('/work-orders/filter/{from_date?}/{to_date?}/{is_invoiced?}/{customer_id?}', 'WorkOrdersController@filter');
Route::post('/work-orders/create', 'WorkOrdersController@create');
Route::delete('/work-orders/{id}/remove', 'WorkOrdersController@remove');
Route::get('/work-orders/{id}', 'WorkOrdersController@get');

/*
 * Job routes
*/
Route::post('/jobs/create', 'JobsController@create');
Route::post('/jobs/update', 'JobsController@update');
Route::post('/jobs/mark-complete', 'JobsController@markComplete');
Route::delete('/jobs/{id}/remove', 'JobsController@remove');
Route::get('/jobs/{id}', 'JobsController@get');

/*
 * Job part routes
*/
Route::post('/job-parts/create', 'JobPartsController@create');
Route::post('/job-parts/update', 'JobPartsController@update');
Route::delete('/job-parts/{id}/remove', 'JobPartsController@remove');

/*
 * Invoice routes
*/
Route::get('/invoices/filter/{from_date?}/{to_date?}/{is_paid?}/{customer_id?}', 'InvoicesController@filter');
Route::post('/invoices/create', 'InvoicesController@create');
Route::post('/invoices/mark-paid', 'InvoicesController@markPaid');
Route::delete('/invoices/{id}/remove', 'InvoicesController@remove');
Route::get('/invoices/{id}', 'InvoicesController@get');
