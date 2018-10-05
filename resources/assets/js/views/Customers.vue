<template>
	<layout>
		<div slot="title">Customers</div>

		<div slot="tools">
			<v-tooltip top>
		    <v-btn color="info" left slot="activator" @click="addDialog = true">
		      <v-icon left>add_circle_outline</v-icon> Customer
		    </v-btn>				
	      <span>Add Customer</span>
	    </v-tooltip>						
		</div>

		<div slot="content">
			<!-- Customers table -->
			<customers-table></customers-table>
	
			<!-- Form dialog -->
			<v-dialog v-model="addDialog" persistent max-width="500px">
        <v-card>
				 	<v-system-bar window class="blue darken-4">
			      <v-spacer></v-spacer>
						<v-tooltip top>
				      <v-btn icon class="mr-0" slot="activator" @click="addDialog = false">
				      	<v-icon class="white--text mr-0">close</v-icon>
				      </v-btn>											
				      <span>Close dialog</span>
				    </v-tooltip>			      
			    </v-system-bar>
          <v-card-text>
          	<customer-form action="createCustomer" @saved="addDialog = false"></customer-form>
          </v-card-text>
        </v-card>
      </v-dialog>			

		</div><!--/ Content slot -->

	</layout>
</template>

<script>
	import Layout from './_Layout';
	import CustomerForm from './../components/forms/Customer-form';
	import CustomersTable from './../components/tables/Customers-table';

	export default{
		data () {
			return {
				addDialog: false
			}
		},

		components: {
			'layout': Layout,
			'customers-table': CustomersTable,
			'customer-form': CustomerForm,
		}	
	}
</script>