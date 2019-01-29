/*
 *	Has logic to validate and submit a form
 *	Has logic to populate a form and put the form into ‘edit state’
 *	Emits a saved event when complete
 *	Has slot to show input fields
 *	Has a prop to indicate fields and validation
*/
<template>
  <v-container fluid>
      <v-form>
        <slot name="form-fields"></slot>
      </v-form>

    </v-card>
  </v-container>
</template>

<script>
	export default{

		props: {
			fields: {
				type: Object,
				required: true
			},
      saveForm: {
        type: Boolean,
        default: false
      },
			action: {
				type: String,
				default: ''
			},
			removeAction: {
				type: String,
				default: ''
			},
      removeKey : {
        type: String,
        default: 'id'
      },
			editState: {
				default: false
			},
      removePayload: {
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

    watch: {
      saveForm (bool){
        if(bool){
          console.log("Save form");
          //this.save();
        }
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
        if(this.removePayload){
          var payload = this.removePayload;
        } else {
          var payload = this.fields.id.value;
        }
      	// Dispatch event to store
      	this.$store.dispatch(this.removeAction, payload)
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
