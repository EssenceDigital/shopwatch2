/*
 *	Has logic to validate and submit a form
 *	Has logic to populate a form and put the form into ‘edit state’
 *	Emits a saved event when complete
 *	Has slot to show input fields
 *	Has a prop to indicate fields and validation
*/
<template>

  <v-card class="elevation-2 grey lighten-4">
    <v-card-title>
      <h3 class="font-weight-thin ml-2">
        <v-icon>info</v-icon>
        {{ title }}
      </h3>
    </v-card-title>

    <v-card-text>
      <v-form>
        <slot name="form-fields"></slot>
      </v-form>
    </v-card-text>

    <v-divider></v-divider>

    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn
        v-if="!singleButton"
        color="danger"
        flat
        @click="$emit('close')"
      >
        Cancel
      </v-btn>
      <v-btn
        color="teal"
        flat
        @click="save"
        :loading="isSaving"
      >
        Save
      </v-btn>
    </v-card-actions>
  </v-card>

	</div>

</template>

<script>
	export default{

		props: {
			fields: {
				type: Object,
				required: true
			},

      title: {
				type: String,
				default: ''
			},

			action: {
				type: String,
				default: ''
			},

			removeAction: {
				type: String,
				default: ''
			},

			editState: {
				default: false
			},

      singleButton: {
        default: false
      },

			hideButton: {
				default: false
			}
		},

		data (){
			return {
				isSaving: false,
				removeDialog: false,
				isRemoving: false,
				serverError: false,
				errorMsg: ''
			}
		},

		methods: {
			save (){
      	// Toggle loader
      	this.isSaving = true;
      	// Populate data for POST Request
      	let data = {};
      	for(let field in this.fields){
      		data[field] = this.fields[field].value;
      	}

      	// Dispatch event to store
      	this.$store.dispatch(this.action, data)
      		.then((response) => {
      			// Toggle loader
      			this.isSaving = false;
    				// The form was saved
    				this.$emit('saved', response.id);
      		})
      		.catch((error) => {
      			console.log(error);
      			// Toggle loader
      			this.isSaving = false;

      			if(error.response){
	      			// Form validation errors
	      			if(error.response.data){

								// Check for error response from Laravel controller
								if(error.response.data.result == 'error'){
									this.$router.app.$emit('snackbar', {
										text: error.response.data.message
									});
								}

								// Form errors
								if(error.response.data.errors){
			      			// Cache errors
			      			let errors = error.response.data.errors;
			      			// Send errors to calling form
			      			this.$emit('error', errors);
								}

	      			}
      			}
      		});
			},

			remove (){
      	// Toggle loader
      	this.isRemoving = true;

      	// Dispatch event to store
      	this.$store.dispatch(this.removeAction, this.fields.id.value)
      		.then((response) => {
      			// Toggle loader and dialog
      			this.removeDialog = false;
      			this.isRemoving = false;
    				// The form was saved
    				this.$emit('removed');
      		})
      		.catch((error) => {
      			// Toggle loader and dialog
      			this.removeDialog = false;
      			this.isRemoving = false;

		  			if(error.response){
		    			// Form validation errors
		    			if(error.response.data){
								// Check for error response from Laravel controller
								if(error.response.data.result == 'error'){
									this.$router.app.$emit('snackbar', {
										text: error.response.data.message
									});
								}
							}
						}

      		});
				}
			}



	}
</script>
