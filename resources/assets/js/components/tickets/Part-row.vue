<template v-if="part">
	<v-layout row class="caption">
		<v-flex xs3>
			<p class="pa-2 pl-3 mb-0">
				{{ part.part_number }}
			</p>
		</v-flex>
		<v-flex xs7>
			<p class="pt-2 pb-2 mb-0">
				{{ part.title }}
			</p>
		</v-flex>
		<v-spacer></v-spacer>
		<v-flex xs1 class="text-xs-center">
			<p class="pa-2 mb-0">
				{{ part.quantity }}
			</p>
		</v-flex>
		<v-flex xs2 class="text-xs-center">
			<p class="pa-2 mb-0">
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
		<v-flex v-if="invoiceState" xs1>

		</v-flex>

	</v-layout>
</template>

<script>

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
			
			}
		},

		components: {

		}

	}
</script>
