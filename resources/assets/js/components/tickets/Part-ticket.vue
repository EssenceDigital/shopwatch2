<template v-if="part">
	<v-layout row class="mt-3">
		<!-- Edit button container  -->
		<v-flex xs1 v-if="invoiceState" class="text-xs-left">
			<v-tooltip top>
	  		<v-btn icon slot="activator" class="mt-0 ml-0" @click="editDialog = true">
	  			<v-icon class="body-1">edit</v-icon>
	  		</v-btn>				    			
	      <span>Edit part</span>
	    </v-tooltip>			
		</v-flex>
		
		<!-- Part info  -->		
		<v-flex xs6>
			<span class="primary--text">
				{{ part.supplier.name }} <em>({{ part.part_invoice_number }})</em>
			</span>
			<br>
			<span>{{ part.title }}</span> 
		</v-flex>
		<v-flex xs1 v-if="!invoiceState"></v-flex>
		<!-- Part costs (Invoice state only) -->
		<v-flex xs2 class="text-xs-right">
			<span>
				{{ part.billing_price | money }}
			</span>
			<br>
			<span v-if="invoiceState" class="error--text">
				<em>({{ part.total_cost | money }}</em>)
			</span>
		</v-flex>

		<!-- Edit part dialog -->
		<v-dialog v-model="editDialog" persistent max-width="500px">
      <v-card>
			 	<v-system-bar window class="blue darken-4">
		      <v-spacer></v-spacer>
					<v-tooltip top>
			      <v-btn icon class="mr-0" slot="activator" @click="editDialog = false">
			      	<v-icon class="white--text mr-0">close</v-icon>
			      </v-btn>											
			      <span>Close dialog</span>
			    </v-tooltip>			      
		    </v-system-bar>
        <v-card-text>
        	<part-form action="updateJobPart" :part="part" edit-state="true" @saved="editDialog = false"></part-form>
        </v-card-text>
      </v-card>
    </v-dialog>
	</v-layout>
</template>

<script>
	import PartForm from './../forms/Job-parts-form';

	export default{
		props: {
			part: {
				required: true
			},
			invoiceState: {
				default: false
			}
		},

		data (){
			return {
				editDialog: false
			}
		},

		components: {
			'part-form': PartForm
		}

	}
</script>