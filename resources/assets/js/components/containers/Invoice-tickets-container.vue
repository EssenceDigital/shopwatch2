<template v-if="invoices">
	<v-container fluid class="pa-0">
		<!-- Row for headings -->
		<v-layout row>
			<v-flex xs12 class="pa-1">
				<span class="subheading"><strong>Open Invoices</strong></span>
			</v-flex>
		</v-layout>
		<!-- Row for invoice tickets -->
		<v-layout v-if="invoices.length > 0" row wrap>
			<v-flex xs12 v-for="invoice in invoices" :key="invoice.id" class="pl-2 pr-2 mt-4">
				<invoice-ticket :invoice="invoice"></invoice-ticket>
			</v-flex>
		</v-layout>	
		<!-- No invoices alert -->
		<v-layout v-else row class="mt-3">
			<v-flex xs10 offset-xs1>
				<v-alert outline color="success" icon="check" value="true">
					No open invoices
				</v-alert>				
			</v-flex>
		</v-layout>			
	</v-container>		
</template>

<script>
	import InvoiceTicket from './../tickets/Invoice-ticket';

	export default{

		computed: {
			invoices (){
				return this.$store.getters.invoices;
			}
		},

		components: {
			'invoice-ticket': InvoiceTicket
		}
	}
</script>