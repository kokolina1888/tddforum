<template>
	<div>
		<div v-if="signedIn">
	    <div class="form-group">                       
            <textarea id="body" name="body" class="form-control" rows="8" v-model="body">
            </textarea>                  
        </div>
        <button type="submit" class="btn btn-default" @click="addReply">Submit</button>
	</div>
	<div v-else>
		<p>
		Please, sign in to post a reply.
		</p>
	</div>
	</div>
</template>
<script>
export default {
	
	data(){
		return {
			body: '',
			
		}
	},
	computed: {
		signedIn(){
			return window.App.signedIn;
		}
	},
	methods: {
		addReply(){
			axios.post(location.pathname + '/replies', {body: this.body})
				.catch(error => {
					flash(error.response.data, 'danger');
				})
				.then(response => {
					this.body = '';
					flash('Your reply has been posted!');
					this.$emit('created', response.data);

				})
		}
	}
}
</script>