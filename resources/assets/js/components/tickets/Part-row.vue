<template v-if="part">
	<v-layout row>
		<v-flex xs3>
			<p class="pa-2 mb-0">
				{{ part.part_invoice_number }}
			</p>
		</v-flex>
		<v-flex xs7>
			<p class="pa-2 mb-0">
				{{ part.title }}
			</p>
		</v-flex>
		<v-spacer v-if="!invoiceState"></v-spacer>
		<v-flex v-if="!invoiceState" xs1 class="text-xs-right">
			<!-- Job tool menu -->
			<v-menu bottom left>
	      <v-btn icon slot="activator" class="mt-0 mr-0">
	        <v-icon>settings</v-icon>
	      </v-btn>
	      <v-list>
	        <v-list-tile @click="editDialog = true">
	          <v-list-tile-title>Edit</v-list-tile-title>
	        </v-list-tile>	        
	      </v-list>
	    </v-menu>			
		</v-flex>
		<v-flex v-if="invoiceState" xs2 class="text-xs-right">
			<p class="pa-2 mb-0">
				{{ part.billing_price | money }}
			</p>
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