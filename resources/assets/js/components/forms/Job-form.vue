<template>
	<base-form
		:action="action"
		remove-action="removeJob"
		:edit-state="editState"
		:fields="form"
		@saved="saved"
		@error="failed"
		@close="saved"
	>
		<template slot="form-fields">
			<!-- Title -->
			<v-text-field
	      label="Title"
	 			v-model="form.title.value"
	 			:error-messages="form.title.errors"
	    ></v-text-field>
			<!-- Description -->
			<v-textarea
	      label="Description"
	 			v-model="form.description.value"
	 			:error-messages="form.description.errors"
	    ></v-textarea>

			<!-- Flat rate our hourly job -->
			<v-checkbox
				v-if="!editState"
				label="Flat Rate Job"
				v-model="isFlatRate"
			></v-checkbox>
			<!-- Hourly job -->
			<v-layout row v-if="!form.is_flat_rate.value">
		    <v-flex xs4 class="pr-2">
					<v-text-field
						type="number"
						min="0"
						step="0.1"
			      label="Hours"
			 			v-model="form.hours.value"
			 			:error-messages="form.hours.errors"
			    ></v-text-field>
		    </v-flex>
				<v-flex xs4 class="pl-2">
					<v-text-field
						type="number"
						min="0"
						step="1"
			      label="Shop Rate"
			 			v-model="form.shop_rate.value"
			 			:error-messages="form.hours.errors"
			    ></v-text-field>
		    </v-flex>
			</v-layout>
			<!-- Flat rate job -->
			<v-layout row v-if="form.is_flat_rate.value">
				<v-flex xs4 class="pr-2">
					<v-text-field
						type="number"
						min="0"
						step="1"
			      label="Job Rate"
			 			v-model="form.flat_rate.value"
			 			:error-messages="form.flat_rate.errors"
			    ></v-text-field>
		    </v-flex>
				<v-flex xs4 class="pl-2">
					<v-text-field
						type="number"
						min="0"
						step="1"
						label="Job Cost"
						v-model="form.flat_rate_cost.value"
						:error-messages="form.flat_rate_cost.errors"
					></v-text-field>
				</v-flex>
			</v-layout>

			<!-- Tech -->
			<v-select
        :items="techSelect"
        v-model="form.tech_id.value"
        :error-messages="form.tech_id.errors"
        label="Technician..."
        single-line
        menu-props="bottom"
				:disabled="form.is_flat_rate.value"
      ></v-select>
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';

	export default{
		props: ['action', 'job', 'workOrder', 'editState', 'shopRate'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					work_order_id: {value: '', errors: []},
					title: {value: '', errors: []},
					description: {value: '', errors: []},
					is_flat_rate: {value: false, errors: []},
					flat_rate: {value: 0, errors: []},
					flat_rate_cost: {value: 0, errors: []},
					hours: {value: 0, errors: []},
					shop_rate: {value: '', errors: []},
					tech_id: {value: '', errors: []}
				},
				completeSelect: [
					{ text: 'No', value: 0 },
					{ text: 'Yes', value: 1 }
				],
				isFlatRate: false
			}
		},

		computed: {
			techSelect (){
				if(!this.form.is_flat_rate.value){
					return this.$store.getters.techSelect;
				} else {
					return this.$store.getters.chargeSelect;
				}
			}
		},

		watch: {
			job (job){
				// Populate the form for editing
				if(job){
					Helpers.populateForm(this.form, job);
				}
			},

			workOrder (id){
				// Set WO ID
				if(id){
					this.form.work_order_id.value = id;
				}
			},

			shopRate (rate){
				if(rate){
					this.form.shop_rate.value = rate;
				}
			},

			isFlatRate (bool){
				if(!bool){
					this.form.is_flat_rate.value = bool;
				} else {
					this.form.is_flat_rate.value = bool;
					this.form.tech_id.value = 1;
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
					Helpers.clearForm(this.form, 'work_order_id', {
						// Default values
						hours: 0,
						is_flat_rate: 0,
						flat_rate: 0,
						flat_rate_cost: 0,
						shop_rate: this.shopRate
					});
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
			// Populate form with supplied job, if needed
			if(this.job){
				Helpers.populateForm(this.form, this.job);
			} else {
				// If not an edit job, apply the default shop rate
				this.form.shop_rate.value = this.shopRate;
			}
			// Set WO ID
			if(this.workOrder){
				this.form.work_order_id.value = this.workOrder;
			}
		}
	}
</script>
