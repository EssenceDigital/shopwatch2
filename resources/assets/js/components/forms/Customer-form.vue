<template>
	<base-form
		:title="title"
		:action="action"
		:fields="form"
		@saved="saved"
		@error="failed"
		@close="close"
	>
		<template slot="form-fields">
			<!-- First name -->
			<v-text-field
	      label="First Name"
	 			v-model="form.first.value"
	 			:error-messages="form.first.errors"
	    ></v-text-field>
	    <!-- Last name -->
			<v-text-field
	      label="Last Name"
	      v-model="form.last.value"
	      :error-messages="form.last.errors"
	    ></v-text-field>
	    <!-- Phone one -->
			<v-text-field
	      label="Cell Phone"
	      v-model="form.phone_one.value"
	      :error-messages="form.phone_one.errors"
	    ></v-text-field>
	    <!-- Phone two -->
			<v-text-field
	      label="Home Phone"
	      v-model="form.phone_two.value"
	      :error-messages="form.phone_two.errors"
	    ></v-text-field>
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';

	export default{
		props: ['title', 'action', 'customer', 'editState'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					first: {value: '', errors: []},
					last: {value: '', errors: []},
					phone_one: {value: '', errors: []},
					phone_two: {value: '', errors: []}
				}
			}
		},

		watch: {
			customer (customer){
				if(customer){
					Helpers.populateForm(this.form, customer);
				}
			}
		},

		components: {
			'base-form': BaseForm
		},

		methods: {
			close (){
				// Clear form
				Helpers.clearForm(this.form);
				Helpers.clearFormErrors(this.form);
				// Notify parent component
				this.$emit('close');
			},
			saved (id){
				// Clear form
				Helpers.clearForm(this.form);
				Helpers.clearFormErrors(this.form);
				// Alert user to success
				this.$router.app.$emit('snackbar', {
					text: 'Customer saved!'
				});
				// Notify parent component
				this.$emit('saved', id);
			},
			failed (errors){
				Helpers.populateFormErrors(this.form, errors);
			}
		}
	}
</script>
