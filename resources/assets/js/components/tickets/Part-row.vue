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
