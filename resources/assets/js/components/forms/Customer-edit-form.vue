<template>
	<base-form
		:title="title"
		:action="action"
		edit-state="true"
		:single-button="singleButton"
		:fields="form"
		@saved="saved"
		@error="failed"
		@close="close"
	>
		<template slot="form-fields">
			<v-container fluid>
				<v-layout row>
					<v-flex xs6>
						<!-- First name -->
						<v-text-field
				      label="First Name"
				 			v-model="form.first.value"
				 			:error-messages="form.first.errors"
				    ></v-text-field>
					</v-flex>
					<v-flex xs6>
						<!-- Last name -->
						<v-text-field
				      label="Last Name"
				      v-model="form.last.value"
				      :error-messages="form.last.errors"
				    ></v-text-field>
					</v-flex>
				</v-layout>
				<v-layout row>
					<v-flex xs6>
						<!-- Phone one -->
						<v-text-field
				      label="Cell Phone"
				      v-model="form.phone_one.value"
				      :error-messages="form.phone_one.errors"
				    ></v-text-field>
					</v-flex>
					<v-flex xs6>
						<!-- Phone two -->
						<v-text-field
				      label="Home Phone"
				      v-model="form.phone_two.value"
				      :error-messages="form.phone_two.errors"
				    ></v-text-field>
					</v-flex>
				</v-layout>
			</v-container>
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';

	export default{
		props: ['title', 'action', 'customer', 'editState', 'singleButton'],

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
				// Clear form errors
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
