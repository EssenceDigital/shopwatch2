<template>
	<base-form 
		:action="action" 
		:fields="form" 
		@saved="saved"
		@error="failed"
	>
		<template slot="form-fields">
      <!-- Password -->
			<v-text-field
	      label="Password"
	      type="password"
	      v-model="form.password.value"
	      :error-messages="form.password.errors"
	    ></v-text-field>
      <!-- Confirm password -->
			<v-text-field
	      label="Confirm Password"
	      type="password"
	      v-model="form.password_confirmation.value"
	      :error-messages="form.password.errors"
	    ></v-text-field>  		    		
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';	

	export default{
		props: ['action', 'user'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					password: {value: '', errors: []},
					password_confirmation: {value: '', errors: []}
				}
			}
		},

		watch: {
			user (user){
				if(user){
					this.form.id.value = user.id;
				}
			}
		},

		components: {
			'base-form': BaseForm
		},

		methods: {
			saved (){
				// Clear form 
				Helpers.clearForm(this.form);
				Helpers.clearFormErrors(this.form);
				// Emit saved event
				this.$emit('saved');
			},

			failed (errors){
				Helpers.populateFormErrors(this.form, errors);
			}
		}
	}
</script>