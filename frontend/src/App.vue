<template>
	<v-app>
		<router-view />
		<v-overlay :value="isLoading">
			<v-progress-circular indeterminate size="64" color="amber" />
		</v-overlay>		
		<v-snackbar v-model="snackbar_success" :color="snackbar_color" :top="true">
			{{ page_message }}<br>			
		</v-snackbar>
		<v-snackbar v-model="snackbar_error" :color="snackbar_color" :top="true">
			{{ page_message }}<br>
			<div v-for="err in page_form_error_message" v-bind:key="err.name">
				<strong>{{err.field}}</strong>
				<div v-for="error in err.error" v-bind:key="error.message">
					{{error.message}}
				</div>
			</div>
		</v-snackbar>
		<confirm ref="confirm"></confirm>
	</v-app>
</template>
<script>
import confirm from "./components/Confirm"
export default {	
			name: "Campus50",
			created(){
				this.$ajax.interceptors.request.use((config) => {
					this.setLoading(true);
					return config;
				}, (error) => {
					this.setLoading(false);
					return Promise.reject(error);
				});
				this.$ajax.interceptors.response.use (response => {
					let data = response.data;			
					switch (data.pid)
					{
						case 'store' :
						case 'update' :
						case 'destroy' :
						case 'resendemail' :
							this.snackbar_color='success';
							this.snackbar_success=true;
							this.page_message = data.message;
						break;
					}
					this.setLoading(false);
					return response;
				},error => {
					const {
						config,
						response: {data, status}
					}=error;
					
					switch (status)
					{
						case 401:					
							if (data.page != 'login')
							{
								this.$store.dispatch('auth/logout');
								this.$store.dispatch('uifront/reinit');	
								this.$store.dispatch('uiadmin/reinit');	
								this.snackbar_color='error';
								this.snackbar_error=true;	
								this.page_message='('+status+': '+data.error+') Token telah expire mohon login kembali';	
							}
						break;
						case 403:
							this.snackbar_error=true;	
							this.snackbar_color='error';
							this.page_message='('+status+': Forbidden) '+data.message+' to access resource ['+config.baseURL+config.url+']';					
						break;
						case 404:
							this.snackbar_error=true;	
							this.snackbar_color='error';
							this.page_message='('+status+': '+data.error+') Mohon untuk dicek url route ('+config.baseURL+config.url+') apakah tersedia';					
						break;
						case 405:
							this.snackbar_error=true;	
							this.snackbar_color='error';
							this.page_message='('+status+': '+data.exception+') Mohon untuk dicek HTTP METHOD ';					
						break;
						case 422:
							this.snackbar_color='error';
							this.snackbar_error=true;	
							var error_messages=[];
							for (var p in data)
							{
								var messages=[];
								var error_list=data[p];
								if (Array.isArray(error_list))
								{						
									for (var i = 0; i < error_list.length;i++ )
									{
										messages.push({
											'message':error_list[i]
										})
									}
									error_messages.push({
										field:p,
										error:messages
									});
								}
								else
								{
									error_messages.push({
										field:p,
										error: [{
											'message':data[p]
										}]
									});		
								}						
							}					
							this.page_form_error_message=error_messages;
							this.page_message='('+status+': Unprocessible Entity) ';	
						break;				
						case 500:			
							this.snackbar_error=true;			
							this.snackbar_color='error';
							this.page_message='('+status+' (internal server eror): '+data.message;					
						break;
					}
					this.setLoading(false);
					return Promise.reject(error);
				});
			},
			mounted()
			{
				this.$root.$confirm = this.$refs.confirm;
			},
			data ()
		{
			return{
			// ajaxloading
			refCount: 0,
			isLoading: false,
			snackbar_success:false,
			snackbar_error:false,
			snackbar_color:'error',
			page_message: "",
			page_form_error_message: {}
			}
		},
			methods: {
			setLoading(isLoading) {
				if (isLoading) {
					this.refCount++;
					this.isLoading = true;
				} else if (this.refCount > 0) {
					this.refCount--;
					this.isLoading = this.refCount > 0;
				}
			},
		},
		components: {
			confirm,
		},
	};
</script>
