<template>
	<base-form 
		:action="action" 
		:fields="form" 
		@saved="saved"
		@error="failed"
	>
		<template slot="form-fields">
			<!-- Tech -->
			<v-select
        :items="paymentOptions"
        v-model="form.payment_method.value"
        :error-messages="form.payment_method.errors"
        label="Payment method"
        single-line
        bottom
      ></v-select>	         	    	    	    		    		
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';	

	export default{
		props: ['action', 'invoice'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					payment_method: {value: '', errors: []}

				},
				paymentOptions: [
					{ text: 'Debit', value: 'Debit' },
					{ text: 'Credit', value: 'Credit' },
					{ text: 'Cash', value: 'Cash' },
					{ text: 'Cheque', value: 'Cheque' }
				]
			}
		},

		computed: {
			techSelect (){
				return this.$store.getters.techSelect;
			}
		},

		watch: {
			invoice (id){
				// Set Invoice ID
				if(id){
					this.form.id.value = id;
				}
			}
		},

		components: {
			'base-form': BaseForm
		},

		methods: {
			saved (){
				if(! this.editState){
					// Clear form 
					Helpers.clearForm(this.form);
				}
				// Clear form errors
				Helpers.clearFormErrors(this.form);
				// Notify parent component
				this.$emit('saved');
			},

			failed (errors){
				Helpers.populateFormErrors(this.form, errors);
			}
		},

		created (){
			// Set Invoice ID
			if(this.invoice){
				this.form.id.value = this.invoice;
			}					
		}
	}
</script>