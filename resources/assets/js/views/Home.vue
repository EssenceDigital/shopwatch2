<template>
	<layout>
		<h2 slot="title" class="primary--text font-weight-thin">
			<v-icon large color="primary">dashboard</v-icon> Dashboard
		</h2>

		<div slot="tools">
			<v-tooltip left>
		    <v-btn color="teal" dark left slot="activator" @click="addWoDialog = true">
		      <v-icon left>add</v-icon> Work Order
		    </v-btn>
	      <span>Start work order</span>
	    </v-tooltip>
		</div>
		<div slot="content">
			<open-work-orders></open-work-orders>

			<!-- Add WO dialog -->
			<v-dialog v-model="addWoDialog" persistent max-width="500px">
        <work-order-form
					title="Add Work Order"
					action="createWorkOrder"
					@saved="workOrderSaved"
					@close="addWoDialog = false"
				></work-order-form>
      </v-dialog>
		</div><!--/ Content slot -->
	</layout>
</template>

<script>
	import Layout from './_Layout';
	import WorkOrderForm from './../components/forms/Work-order-form';
	import OpenWorkOrders from './../components/iterators/Open-work-orders';

	export default{
		data () {
			return {
				addWoDialog: false,
				componentLoading: false
			}
		},

		components: {
			'layout': Layout,
			'work-order-form': WorkOrderForm,
			'open-work-orders': OpenWorkOrders
		},

		methods: {
			workOrderSaved (id){
				// Toggle digalog
				this.addWoDialog = false;
				// Redirect view
				this.$router.push('/work-orders/'+id);
			}
		}
	}
</script>
