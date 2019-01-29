<template>
	<v-layout row>
		<v-flex xs6>
			<v-card>
				<v-card-title>

					<h3 class="headline">
						<v-icon left>description</v-icon>
						Details
					</h3>
				</v-card-title>
				<v-divider></v-divider>
				<parent-form
					:save-form="saveForm"
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
						<v-layout row>
							<v-flex xs8>
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
							</v-flex>
						</v-layout>

					</template>
				</parent-form>
			</v-card>
		</v-flex>
		<v-flex xs6 class="pl-3">
			<v-card v-if="">
				<v-card-title>
					<h3 class="headline">
						<v-icon left>build</v-icon>
						Parts
					</h3>
					<v-spacer></v-spacer>
					<!-- Show add part form -->
					<v-tooltip left v-if="!showPartsForm">
						<v-btn
							slot="activator"
							@click="showPartsForm = true"
							icon
						>
							<v-icon color="teal">add_circle_outline</v-icon>
						</v-btn>
						<span>Add part</span>
					</v-tooltip>
					<!-- Hide add part form-->
					<v-tooltip left v-if="showPartsForm">
						<v-btn
							slot="activator"
							@click="hidePartsForm"
							icon
							class="mt-0"
						>
							<v-icon>clear</v-icon>
						</v-btn>
						<span>Hide form</span>
					</v-tooltip>

				</v-card-title>
				<v-divider></v-divider>

				<v-card-text v-if="!showPartsForm" class="ml-1 mr-1">

					<v-container
						v-for="part in form.parts.value"
						:key="id"
						fluid
						class="pl-0 pr-0"
					>
						<v-layout row>
							<v-flex xs3>
								<p class="pa-2 pt-3 mb-0">
									{{ part.part_number }}
								</p>
							</v-flex>
							<v-flex xs6>
								<p class="pt-2 pt-3 pb-2 mb-0">
									{{ part.title }}
								</p>
							</v-flex>
							<v-spacer></v-spacer>
							<v-flex xs1 class="text-xs-center">
								<p class="pa-2 pt-3 mb-0">
									{{ part.quantity }}
								</p>
							</v-flex>
							<v-flex xs2 class="text-xs-center">
								<p class="pa-2 pt-3 mb-0">
									[{{ part.billing_price | money }}]
								</p>
							</v-flex>
							<v-flex xs2 class="text-xs-right">
								<p class="pa-2 pt-3 mb-0">
									{{ part.billing_price * part.quantity | money }}
								</p>
							</v-flex>
							<v-flex xs1 class="text-xs-right">
								<v-tooltip top>
									<v-btn slot="activator" icon>
										<v-icon>edit</v-icon>
									</v-btn>
									<span>Edit part</span>
								</v-tooltip>

							</v-flex>
							<v-flex xs1 class="text-xs-right">
								<v-tooltip top>
									<v-btn
									 	@click="removePart(part)"
										slot="activator"
										icon
									>
										<v-icon>cancel</v-icon>
									</v-btn>
									<span>Remove part</span>
								</v-tooltip>

							</v-flex>


						</v-layout>
						<v-divider></v-divider>
					</v-container>


				</v-card-text>

				<v-card-text v-if="showPartsForm">
					<job-parts-form
						:job="job.id"
						@part-added="partAdded"
					></job-parts-form>
				</v-card-text>

			</v-card>


		</v-flex>

	</v-layout>



</template>

<script>
	import ParentForm from './_Parent-form';
	import PartsForm from './Job-parts-form';
	import Helpers from './../../app/helpers';


	export default{
		props: ['action', 'job', 'workOrder', 'editState', 'shopRate', 'saveForm', 'addPart'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					work_order_id: {value: '', errors: []},
					is_premade: {value: false, errors: []},
					title: {value: '', errors: []},
					description: {value: '', errors: []},
					is_flat_rate: {value: false, errors: []},
					flat_rate: {value: 0, errors: []},
					flat_rate_cost: {value: 0, errors: []},
					hours: {value: 0, errors: []},
					shop_rate: {value: '', errors: []},
					parts: {value: {}, errors: []},
					tech_id: {value: '', errors: []}
				},
				completeSelect: [
					{ text: 'No', value: 0 },
					{ text: 'Yes', value: 1 }
				],
				isFlatRate: false,
				showPartsForm: false
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
			'parent-form': ParentForm,
			'job-parts-form': PartsForm
		},

		methods: {

			saved (){
				if(! this.editState){
					// Clear form
					Helpers.clearForm(this.form, 'work_order_id', {
						// Default values
						is_premade: false,
						hours: 0,
						is_flat_rate: false,
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
			},

			hidePartsForm (){
				this.showPartsForm = false;
			},

			partAdded (part){
				// Make a unique key for the part
				var uniqid = Math.round(new Date().valueOf() + Math.random());
				// Add id to part
				part.id = uniqid;
				// Add part to parts array in form
				this.form.parts.value[uniqid] = part;
				// Hide form
				this.showPartsForm = false;

				console.log(part.id);
			},

			removePart (part){
				delete this.form.parts.value[part.id];
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
