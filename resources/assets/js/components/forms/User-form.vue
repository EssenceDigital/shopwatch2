<template>
	<base-form 
		:action="action" 
		remove-action="removeUser"
		:edit-state="editState"
		:fields="form" 
		@saved="saved"
		@error="failed"
	>
		<template slot="form-fields">
			<!-- Name -->
			<v-text-field
	      label="Name"
	 			v-model="form.name.value"
	 			:error-messages="form.name.errors"
	    ></v-text-field>	
	    <!-- Email -->
			<v-text-field
	      label="Email"
	      v-model="form.email.value"
	      :error-messages="form.email.errors"
	    ></v-text-field>
	    <!-- Role -->
			<v-select
        :items="roles"
        v-model="form.role.value"
        :error-messages="form.role.errors"
        label="Role"
        single-line
        bottom
      ></v-select>
      <!-- Password -->
			<v-text-field
				v-if="!user"
	      label="Password"
	      type="password"
	      v-model="form.password.value"
	      :error-messages="form.password.errors"
	    ></v-text-field>
      <!-- Confirm password -->
			<v-text-field
				v-if="!user"
	      label="Confirm Password"
	      type="password"
	      v-model="form.password_confirmation.value"
	      :error-messages="form.password.errors"
	    ></v-text-field>
      <!-- Hourly wage --> 
      <v-flex xs12 md4>
				<v-text-field
		      label="Hourly Wage"
		      type="number"
		      prefix="$"
		      min="0"
		      step="0.25"
		      v-model="form.hourly_wage.value"
		      :error-messages="form.hourly_wage.errors"
		    ></v-text-field>       	
      </v-flex>  		    		
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';	

	export default{
		props: ['action', 'user', 'editState'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					name: {value: '', errors: []},
					email: {value: '', errors: []},
					role: {value: '', errors: []},
					password: {value: '', errors: []},
					password_confirmation: {value: '', errors: []},
					hourly_wage: {value: '', errors: []}
				},
				roles: [
					{text: 'User', value: 'user'},
					{text: 'Tech', value: 'tech'},
					{text: 'Admin', value: 'admin'}					
				]
			}
		},

		watch: {
			user (user){
				if(user){
					Helpers.populateForm(this.form, user);
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
				// Notify parent component
				this.$emit('saved');
			},

			failed (errors){
				Helpers.populateFormErrors(this.form, errors);
			}
		}
	}
</script>