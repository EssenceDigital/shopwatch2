<template v-if="invoice">
	<v-card flat>
		<v-container fluid class="pa-0">
			<v-system-bar window color="primary" dark class="mb-2">
				Invoice #{{ invoice.id }}
	      <v-spacer></v-spacer>
	    </v-system-bar>
			<v-card-text class="pa-0">
				<v-container fluid class="pa-0">
					<v-layout row class="mt-1">
						<v-icon>event</v-icon>
						<span class="ml-2">{{ invoice.date }}</span>						
					</v-layout>
					<v-layout row class="mt-1">
						<v-icon class="mr-2">drive_eta</v-icon>
						<span><strong>{{ invoice.vehicle.year + ' ' + invoice.vehicle.make + ' ' + invoice.vehicle.model }}</strong></span>						
					</v-layout>					
					<v-layout row class="mt-1">
						<v-icon class="mr-2">account_box</v-icon>
						<span>{{ invoice.customer.first + ' ' + invoice.customer.last }}</span>							
					</v-layout>	
					<v-layout row class="mt-1">
						<v-icon class="mr-2" color="success">account_balance</v-icon>
						<span class="success--text">{{ invoice.grand_total | money }}</span>							
					</v-layout>						
				</v-container>
			</v-card-text>

    	<v-card-actions class="pa-0">
        <v-spacer></v-spacer>
				<v-tooltip left>
	        <v-btn icon slot="activator" @click="viewInvoice(invoice.id)">
	          <v-icon>more</v-icon>
	        </v-btn>				
		      <span>View Invoice</span>
		    </v-tooltip>        

      </v-card-actions>
      <v-divider></v-divider>		
		</v-container>

	</v-card>
</template>

<script>
	export default{
		props: ['invoice'],

		methods: {
			viewInvoice (id){
				this.$router.push('/invoices/' + id);
			}
		}
	}
</script>