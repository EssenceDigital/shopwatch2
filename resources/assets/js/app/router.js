// Load Vue based dependencies
import Vue from 'vue';
import VueRouter from 'vue-router';

import HomeView from './../views/Home';
import UsersView from './../views/Users';
import CustomersView from './../views/Customers';
import CustomerAccountView from './../views/Customer-account';
import WorkOrdersView from './../views/Work-orders';
import WorkOrderView from './../views/Work-order';
import InvoicesView from './../views/Invoices';
import InvoiceView from './../views/Invoice';
import BusConfigView from './../views/Bus-config';
import UserSettingsView from './../views/User-settings';

// Register router with Vue
Vue.use(VueRouter);

export default new VueRouter({
	routes: [
		{
			path: '/',
			name: 'Dashboard',
			component: HomeView
		},
		{
			path: '/users',
			name: 'Users',
			component: UsersView
		},
		{
			path: '/customers',
			name: 'Customers',
			component: CustomersView
		},
		{
			path: '/customers/:id/account',
			name: 'Customer Account',
			component: CustomerAccountView,
			props: true
		},
		{
			path: '/work-orders',
			name: 'Work Orders',
			component: WorkOrdersView
		},
		{
			path: '/work-orders/:id',
			name: 'Work Order',
			component: WorkOrderView,
			props: true
		},
		{
			path: '/invoices',
			name: 'Invoices',
			component: InvoicesView
		},
		{
			path: '/invoices/:id',
			name: 'Invoice',
			component: InvoiceView,
			component: InvoiceView,
			props: true
		},
		{
			path: '/config',
			name: 'Business Config',
			component: BusConfigView
		},
		{
			path: '/user-settings',
			name: 'User Settings',
			component: UserSettingsView
		}
	]
});
