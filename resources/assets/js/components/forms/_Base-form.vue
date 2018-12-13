/*
 *	Has logic to validate and submit a form
 *	Has logic to populate a form and put the form into ‘edit state’
 *	Emits a saved event when complete
 *	Has slot to show input fields
 *	Has a prop to indicate fields and validation
*/
<template>
  <div>
    <v-card class="elevation-2 grey lighten-4">
      <v-card-title>
        <h3 class="font-weight-thin ml-2">
          <v-icon>{{ titleIcon }}</v-icon>
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
        <v-tooltip right v-if="editState">
    			<v-btn slot="activator" icon @click="removeDialog = true">
    				<v-icon color="error">delete_sweep</v-icon>
    			</v-btn>
          <span>Remove</span>
        </v-tooltip>
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
          v-if="!hideSaveButton"
          color="teal"
          flat
          @click="save"
          :loading="isSaving"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
      <!-- Remove dialog -->
      <v-dialog
      	v-if="editState"
      	v-model="removeDialog"
      	persistent
      	max-width="300px"
      >
        <v-card>
          <v-card-text class="text-xs-center">
            Permanently remove this?
          </v-card-text>
          <v-card-actions>
          	<v-spacer></v-spacer>
  					<v-btn flat @click.native="removeDialog = false">Cancel</v-btn>
            <v-btn color="red darken-1" flat :loading="isRemoving" @click.native="remove">Remove</v-btn>
            <v-spacer></v-spacer>
          </v-card-actions>
        </v-card>
      </v-dialog>
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

      titleIcon: {
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

      removeKey : {
        type: String,
        default: 'id'
      },

			editState: {
				default: false
			},

      singleButton: {
        default: false
      },

			hideSaveButton: {
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
        var removeKey = this.removeKey;
      	// Dispatch event to store
      	this.$store.dispatch(this.removeAction, this.fields[removeKey].value)
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
