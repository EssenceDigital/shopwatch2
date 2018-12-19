<template v-if="part">
	<v-layout row>
		<v-flex xs3>
			<p class="pa-2 pl-3 mb-0">
				{{ part.part_number }}
			</p>
		</v-flex>
		<v-flex xs7>
			<p class="pa-2 mb-0">
				{{ part.title }}
			</p>
		</v-flex>
		<v-spacer></v-spacer>
		<v-flex xs1 class="text-xs-right">
			<p class="pa-2 mb-0">
				{{ part.quantity }}
			</p>
		</v-flex>
		<v-flex xs2 class="text-xs-right">
			<p v-if="part.quantity > 1" class="pa-2 mb-0">
				[{{ part.billing_price | money }}]
			</p>
		</v-flex>
		<v-flex xs2 class="text-xs-right">
			<p class="pa-2 mb-0">
				{{ part.billing_price * part.quantity | money }}
			</p>
		</v-flex>
		<v-flex v-if="!invoiceState" xs1 class="text-xs-right">
			<!-- Job tool menu -->
			<v-menu bottom left>
	      <v-btn icon slot="activator" class="mt-0 mr-0">
	        <v-icon>arrow_drop_down</v-icon>
	      </v-btn>
	      <v-list>
	        <v-list-tile @click="editDialog = true">
	          <v-list-tile-title>Edit part</v-list-tile-title>
	        </v-list-tile>
	      </v-list>
	    </v-menu>
		</v-flex>

		<!-- Edit part dialog -->
		<v-dialog v-model="editDialog" persistent max-width="500px">
    	<part-form
				action="updateJobPart"
				:part="part"
				edit-state="true"
				:remove-payload="part.id + '/' + part.job_id"
				@saved="editDialog = false"
			></part-form>
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
