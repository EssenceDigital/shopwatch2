<template v-if="id">
	<layout>
		<h2 slot="title" class="primary--text font-weight-thin">
			<v-icon large color="primary">person</v-icon> Customer Account
		</h2>


		<div slot="tools">

		</div>

		<div slot="content">
			<customer-edit-form
				action="updateCustomer"
				title="Information"
				single-button="true"
				:customer="customer"
			></customer-edit-form>

			<open-work-orders
				:filter="workOrdersFilter"
				class="mt-5"
			></open-work-orders>

		</div><!--/ Content slot -->
	</layout>
</template>

<script>
	import Layout from './_Layout';
	import CustomerEditForm from './../components/forms/Customer-edit-form';
	import OpenWorkOrders from './../components/iterators/Open-work-orders';

	export default{
		props: ['id'],

		data () {
			return {
				componentLoading: true,
				workOrdersFilter: {
					created_at: '',
  				is_invoiced: 'false-bool',
					customer_id: this.id
				}
			}
		},

		computed: {
			customer (){
				return this.$store.getters.selectedCustomer;
			}
		},

		components: {
			'layout': Layout,
			'customer-edit-form': CustomerEditForm,
			'open-work-orders': OpenWorkOrders
		},

		created (){
			console.log(this.id);
			// If ID prop does not match the selected work order then update selected customer
			// ID prop is final source of truth
			if(! this.customer.id || this.customer.id != this.id) {
				this.$store.dispatch('getCustomer', this.id)
					.then(() => {
						// Toggle component loading
						this.componentLoading = false;
					});
			} else {
				// Toggle component loading
				this.componentLoading = false;
			}
		}
	}
</script>
