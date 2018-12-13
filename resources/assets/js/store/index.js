import Vue from 'vue';
import Vuex from 'vuex';
import ApiHelper from './../app/api';
import Helpers from './../app/helpers';

Vue.use(Vuex);

/**
 * Holds state that can be used accross all components
*/
export const store = new Vuex.Store({
	/**
	 * The cache values to be centralized
	*/
	state: {
		users: [],
		selectedUser: false,

		customers: [],
		selectedCustomer: false,
		customersFilter: {

		},
		selectedVehicle: false,

		workOrders: [],
		workOrdersFilter: {
			from_date: '',
			to_date: '',
			is_invoiced: '',
			customer_id:  ''
		},
		selectedWorkOrder: false,

		invoices: [],
		selectedInvoice: false,
		invoicesFilter: {
			from_date: '',
			to_date: '',
			is_paid: '',
			customer_id: ''
		},

		suppliers: [],
		busConfig: {},
		authUser: {}
	},

	/**
	 * Methods that directly change the state cache.
	 *
	 * Method names are self descriptive so comments are only added where clarity is needed.
	*/
	mutations: {
		updateBusConfig (state, payload){
			return state.busConfig = payload;
		},

		updateUsers (state, payload){
			return state.users = payload;
		},
		addUser (state, payload){
			return state.users.push(payload);
		},
		updateSelectedUser (state, payload){
			return state.selectedUser = payload;
		},
		updateUser (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.users, 'id', payload.id);
			// Have to remove to trigger computed prop in components
			state.users.splice(index, 1);
			// Re-add updated payload at same index
			return state.users.splice(index, 0, payload);
		},
		removeUser (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.users, 'id', payload);
			// Have to remove to trigger computed prop in components
			return state.users.splice(index, 1);
		},

		updateCustomers (state, payload){
			return state.customers = payload;
		},
		addCustomer (state, payload){
			return state.customers.unshift(payload);
		},
		updateSelectedCustomer (state, payload){
			return state.selectedCustomer = payload;
		},
		/*updateCustomer (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.customers, 'id', payload.id);
			// Have to remove to trigger computed prop in components
			state.customers.splice(index, 1);
			//Update selectec customer
			state.selectedCustomer = payload;
			// Re-add updated payload at same index
			return state.customers.splice(index, 0, payload);
		},*/
		removeCustomer (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.customers, 'id', payload);
			// Have to remove to trigger computed prop in components
			return state.customers.splice(index, 1);
		},

		updateVehicles (state, payload){
			return state.vehicles = payload;
		},
		updateSelectedVehicle (state, payload){
			return state.selectedVehicle = payload;
		},
		/*addVehicle (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.customers, 'id', payload.customer_id);

			return state.customers[index].vehicles.unshift(payload);
		},

		updateVehicle (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.customers, 'id', payload.customer_id);
			// Have to remove to trigger computed prop in components
			state.vehicles.splice(index, 1);
			// Re-add updated payload at same index
			return state.vehicles.splice(index, 0, payload);
		},
		removeVehicle (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.vehicles, 'id', payload);
			// Have to remove to trigger computed prop in components
			return state.vehicles.splice(index, 1);
		},*/

		updateWorkOrders (state, payload){
			return state.workOrders = payload;
		},
		addWorkOrder (state, payload){
			return state.workOrders.unshift(payload);
		},
		updateSelectedWorkOrder (state, payload){
			return state.selectedWorkOrder = payload;
		},
		updateWorkOrder (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.workOrders, 'id', payload.id);
			// Have to remove to trigger computed prop in components
			state.workOrders.splice(index, 1);
			// Re-add updated payload at same index
			return state.workOrders.splice(index, 0, payload);
		},
		updateWorkOrderFromDateFilter (state, payload){
			return state.workOrdersFilter.from_date = payload;
		},
		updateWorkOrderToDateFilter (state, payload){
			return state.workOrdersFilter.to_date = payload;
		},
		updateWorkOrderIsInvoicedFilter (state, payload){
			return state.workOrdersFilter.is_invoiced = payload;
		},
		updateWorkOrderCustomerIdFilter (state, payload){
			return state.workOrdersFilter.customer_id = payload;
		},
		removeWorkOrder (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.workOrders, 'id', payload);
			// Have to remove to trigger computed prop in components
			return state.workOrders.splice(index, 1);
		},


		updateSelectedJob (state, payload){
			return state.updateSelectedJob = payload;
		},

		updateInvoices (state, payload){
			return state.invoices = payload;
		},
		updateSelectedInvoice (state, payload){
			return state.selectedInvoice = payload;
		},
		addInvoice (state, payload){
			return state.invoices.unshift(payload);
		},
		updateInvoice(state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.invoices, 'id', payload.id);
			// Have to remove to trigger computed prop in components
			state.invoices.splice(index, 1);
			// Re-add updated payload at same index
			return state.invoices.splice(index, 0, payload);
		},
		updateInvoiceFromDateFilter (state, payload){
			return state.invoicesFilter.from_date = payload;
		},
		updateInvoiceToDateFilter (state, payload){
			return state.invoicesFilter.to_date = payload;
		},
		updateInvoiceIsPaidFilter (state, payload){
			return state.invoicesFilter.is_paid = payload;
		},
		updateInvoiceCustomerIdFilter (state, payload){
			return state.invoicesFilter.customer_id = payload;
		},
		removeInvoice (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.invoices, 'id', payload);
			// Have to remove to trigger computed prop in components
			return state.invoices.splice(index, 1);
		},

		updateSuppliers (state, payload){
			return state.suppliers = payload;
		},
		addSupplier (state, payload){
			return state.suppliers.unshift(payload);
		},
		updateSupplier (state, payload){
			// Get index of updated payload
			let index = Helpers.pluckObjectById(state.suppliers, 'id', payload.id);
			// Have to remove to trigger computed prop in components
			state.suppliers.splice(index, 1);
			// Re-add updated payload at same index
			return state.suppliers.splice(index, 0, payload);
		}
	},

	/**
	 * Actions that send API server calls and manipulate the database.
	 *
	 * Method names are self descriptive so comments are only added where clarity is needed.
	*/
	actions: {
		getBusConfig (context, payload){
			return ApiHelper.getAction(context, '/bus-config', 'updateBusConfig');
		},
		updateBusConfig (context, payload){
			return ApiHelper.postAction(context, payload, '/bus-config/update', 'updateBusConfig');
		},

		getUsers(context, payload){
			return ApiHelper.getAction(context, '/users', 'updateUsers');
		},
		getUser (context, payload){
			return ApiHelper.getAction(context, '/users/'+payload, 'updateSelectedUser');
		},
		createUser (context, payload){
			return ApiHelper.postAction(context, payload, '/users/create', 'addUser');
		},
		updateUser (context, payload){
			return ApiHelper.postAction(context, payload, '/users/update', 'updateUser');
		},
		removeUser (context, payload){
			return ApiHelper.removeAction(context, '/users/'+payload+'/remove', 'removeUser');
		},
		changeUserPassword (context, payload){
			return ApiHelper.postAction(context, payload, '/users/change-password');
		},
		changeAuthPassword (context, payload){
			return ApiHelper.postAction(context, payload, '/users/change-auth-password');
		},
		allCustomers (context, payload){
			return ApiHelper.getAction(context, '/customers/all', 'updateCustomers');
		},
		filterCustomers (context, payload){
			var url = '/customers';
			// Create payload
			if(payload){
				if(payload.first != '') url += '/' + payload.first;
					else url += '/' + 0;

				if(payload.last != '') url += '/' + payload.last;
					else url += '/' + 0;
			}
			// Use api to send the request
			return ApiHelper.getAction(context, url, 'updateCustomers');
		},
		getCustomer (context, payload){
			return ApiHelper.getAction(context, '/customers/'+payload+'/fetch', 'updateSelectedCustomer');
		},
		getCustomerWorkOrders (context, payload){
			return ApiHelper.getAction(context, '/customers/'+payload+'/work-orders', 'updateWorkOrders');
		},
		getCustomerInvoices (context, payload){
			return ApiHelper.getAction(context, '/customers/'+payload+'/invoices', 'updateInvoices');
		},
		createCustomer (context, payload){
			return ApiHelper.postAction(context, payload, '/customers/create', 'addCustomer');
		},
		updateCustomer (context, payload){
			return ApiHelper.postAction(context, payload, '/customers/update', 'updateSelectedCustomer');
		},
		removeCustomer (context, payload){
			return ApiHelper.removeAction(context, '/customers/'+payload+'/remove', 'removeCustomer');
		},

		filterVehicles (context, payload){
			var url = '/vehicles';
			// Create payload
			if(payload){
				if(payload.year != '') url += '/' + payload.year;
					else url += '/' + 0;

				if(payload.make != '') url += '/' + payload.make;
					else url += '/' + 0;

				if(payload.model != '') url += '/' + payload.model;
					else url += '/' + 0;

				if(payload.plate_number != '') url += '/' + payload.plate_number;
					else url += '/' + 0;
			}
			// Use api to send the request
			return ApiHelper.getAction(context, url, 'updateVehicles');
		},
		getVehicle (context, payload){
			return ApiHelper.getAction(context, '/vehicles/'+payload, 'updateSelectedVehicle');
		},
		getVehicleWorkOrders (context, payload){
			return ApiHelper.getAction(context, '/vehicles/'+payload+'/work-orders', 'updateVehicles');
		},
		getVehicleInvoices (context, payload){
			return ApiHelper.getAction(context, '/vehicles/'+payload+'/invoices', 'updateInvoices');
		},
		createVehicle (context, payload){
			return ApiHelper.postAction(context, payload, '/vehicles/create', 'updateSelectedVehicle');
		},
		updateVehicle (context, payload){
			return ApiHelper.postAction(context, payload, '/vehicles/update', 'updateCustomer');
		},
		removeVehicle (context, payload){
			return ApiHelper.removeAction(context, '/vehicles/'+payload+'/remove', 'updateCustomer');
		},

		filterWorkOrders (context, payload){
			var url = '/work-orders/filter';

			if(payload){
				if(payload.from_date && payload.from_date != '') url += '/' + payload.from_date;
					else url += '/' + 0;

				if(payload.to_date && payload.to_date != '') url += '/' + payload.to_date;
					else url += '/' + 0;

				if(payload.is_invoiced && payload.is_invoiced != '') url += '/' + payload.is_invoiced;
					else url += '/' + 0;

				if(payload.customer_id && payload.customer_id != '') url += '/' + payload.customer_id;
					else url += '/' + 0;
			}

			// Use api to send the request
			return ApiHelper.getAction(context, url, 'updateWorkOrders');
		},
		getWorkOrder (context, payload){
			return ApiHelper.getAction(context, '/work-orders/'+payload, 'updateSelectedWorkOrder');
		},
		createWorkOrder (context, payload){
			return ApiHelper.postAction(context, payload, '/work-orders/create', 'updateSelectedWorkOrder');
		},
		removeWorkOrder (context, payload){
			return ApiHelper.removeAction(context, '/work-orders/'+payload+'/remove', 'removeWorkOrder');
		},

		getJob (context, payload){
			return ApiHelper.getAction(context, '/jobs/'+payload, 'updateSelectedJob');
		},
		createJob (context, payload){
			return ApiHelper.postAction(context, payload, '/jobs/create', 'updateSelectedWorkOrder');
		},
		updateJob (context, payload){
			return ApiHelper.postAction(context, payload, '/jobs/update', 'updateSelectedWorkOrder');
		},
		markJobComplete (context, payload){
			return ApiHelper.postAction(context, payload, '/jobs/mark-complete', 'updateSelectedWorkOrder');
		},
		removeJob (context, payload){
			return ApiHelper.removeAction(context, '/jobs/'+payload+'/remove', 'updateSelectedWorkOrder');
		},

		createJobPart (context, payload){
			return ApiHelper.postAction(context, payload, '/job-parts/create', 'updateSelectedWorkOrder');
		},
		updateJobPart (context, payload){
			return ApiHelper.postAction(context, payload, '/job-parts/update', 'updateSelectedWorkOrder');
		},
		removeJobPart (context, payload){
			return ApiHelper.removeAction(context, '/job-parts/'+payload+'/remove', 'updateSelectedWorkOrder');
		},

		filterInvoices (context, payload){
			var url = '/invoices/filter';
			// Create payload
			if(payload){
				if(payload.from_date && payload.from_date != '') url += '/' + payload.from_date;
					else url += '/' + 0;

				if(payload.to_date && payload.to_date != '') url += '/' + payload.to_date;
					else url += '/' + 0;

				if(payload.is_paid && payload.is_paid != '') url += '/' + payload.is_paid;
					else url += '/' + 0;

				if(payload.customer_id && payload.customer_id != '') url += '/' + payload.customer_id;
					else url += '/' + 0;
			}
			// Use api to send the request
			return ApiHelper.getAction(context, url, 'updateInvoices');
		},
		getInvoice (context, payload){
			return ApiHelper.getAction(context, '/invoices/'+payload, 'updateSelectedInvoice');
		},
		createInvoice (context, payload){
			return ApiHelper.postAction(context, payload, '/invoices/create', 'updateSelectedInvoice');
		},
		markInvoicePaid (context, payload){
			return ApiHelper.postAction(context, payload, '/invoices/mark-paid', 'updateSelectedInvoice');
		},
		removeInvoice (context, payload){
			return ApiHelper.removeAction(context, '/invoices/'+payload+'/remove', 'updateSelectedWorkOrder');
		},

		getSuppliers (context, payload){
			return ApiHelper.getAction(context, '/suppliers', 'updateSuppliers');
		},
		createSupplier (context, payload){
			return ApiHelper.postAction(context, payload, '/suppliers/create', 'addSupplier');
		},
		updateSupplier(context, payload){
			return ApiHelper.postAction(context, payload, '/suppliers/update', 'updateSupplier');
		}

	},

	/**
	 * Getters that access the state directly (For components)
	 *
	 * Method names are self descriptive so comments are only added where clarity is needed.
	*/
	getters: {
		users (state){
			return state.users;
		},
		selectedUser (state){
			return state.selectedUser
		},

		customers (state){
			return state.customers;
		},
		customersSelect (state){
			var customers = state.customers,
          select = [];
      // Create select array
      customers.forEach(function(customer){
        select.push({ text: customer.first+' '+customer.last, value: customer.id });
      });
      return select;
		},
		selectedCustomer (state){
			return state.selectedCustomer;
		},
		selectedCustomerVehiclesSelect (state){
			var customer = state.selectedCustomer,
          select = [];

      if(customer){
	      if(customer.vehicles){
		      // Create select array
		      customer.vehicles.forEach(function(vehicle){
		        select.push({ text: vehicle.year+' '+vehicle.make+' '+vehicle.model, value: vehicle.id });
		      });
	      }
      }
      return select;
		},
		selectedVehicle (state){
			return state.selectedVehicle;
		},

		workOrders (state){
			return state.workOrders
		},
		selectedWorkOrder (state){
			return state.selectedWorkOrder;
		},
		workOrdersFilter (state){
			return state.workOrdersFilter;
		},

		invoices (state){
			return state.invoices;
		},
		selectedInvoice (state){
			return state.selectedInvoice;
		},
		invoicesFilter (state){
			return state.invoicesFilter;
		},

		suppliersSelect (state){
			var suppliers = state.suppliers,
          select = [{ text: "Supplier...", value: "" }];
      // Create select array
      suppliers.forEach(function(supplier){
        select.push({ text: supplier.name, value: supplier.name });
      });
      return select;
		},
		busConfig (state){
			return state.busConfig;
		},
		authUser (state){
			return state.authUser;
		},
		techSelect (state){
			var users = state.users,
          select = [{ text: "Tech...", value: "" }];
      // Create select array
      users.forEach(function(user){
      	if(user.role == 'tech'){
      		select.push({ text: user.name, value: user.name });
      	}
      });
      return select;

		}
	}

});
