<template>
		<div>
				<v-app-bar app class="white" elevation="0">
					<v-toolbar-title @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken']).catch(err => {})">
								<span>
									<v-img
											max-width="400"
											:src="$api.storageURL+'/storage/images/applogo.png'"
											>
										</v-img>
								</span>
						</v-toolbar-title>
					<v-spacer></v-spacer>
						<v-menu
								:close-on-content-click="true"
								origin="center center"
								transition="scale-transition"
								:offset-y="true"
								bottom 
								left>
								<template v-slot:activator="{on}">
										<v-avatar size="30">
												<v-img :src="photoUser" v-on="on" />
										</v-avatar>                    
								</template>
								<v-list>
										<v-list-item>
												<v-list-item-avatar>
														<v-img :src="photoUser"></v-img>
												</v-list-item-avatar>
												<v-list-item-content>					
														<v-list-item-title class="title">
																{{ATTRIBUTE_USER('username')}}
														</v-list-item-title>
														<v-list-item-subtitle>     
																[{{DEFAULT_ROLE}}]
														</v-list-item-subtitle>
												</v-list-item-content>
										</v-list-item>                    
										<v-divider/>
										<v-list-item to="/system-users/profil">
												<v-list-item-icon class="mr-2">
							<v-icon>mdi-account</v-icon>
						</v-list-item-icon>
												<v-list-item-title>Profil</v-list-item-title>
										</v-list-item>
										<v-divider/>
										<v-list-item @click.prevent="logout">
												<v-list-item-icon class="mr-2">
							<v-icon>mdi-power</v-icon>
						</v-list-item-icon>
												<v-list-item-title>Logout</v-list-item-title>
										</v-list-item>
								</v-list>
						</v-menu>			
				</v-app-bar>                    
				<v-main>
					<slot />
				</v-main>
		</div>    
</template>
<script>
import {mapGetters} from 'vuex';
export default {
		name:'AdminLayout',
		props: {
				showrightsidebar: {
						type:Boolean,
						default:true
				},
				temporaryleftsidebar: {
						type:Boolean,
						default:false
				},
		},    
		data:()=>({
				loginTime:0,
				drawer:null,
				drawerRight:null, 
		}),     
		methods: {        
				logout ()
				{
						this.loginTime=0;
						this.$ajax.post('/auth/logout',
								{},
								{
										headers: {
												Authorization: this.TOKEN,
										}
								}
						).then(()=> {     
								this.$store.dispatch('auth/logout');	
								this.$store.dispatch('uifront/reinit');	
								this.$store.dispatch('uiadmin/reinit');	
								this.$router.push('/');
						})
						.catch(() => {
								this.$store.dispatch('auth/logout');	
								this.$store.dispatch('uifront/reinit');	
								this.$store.dispatch('uiadmin/reinit');	
								this.$router.push('/');
						});
				},
				isBentukPT (bentuk_pt)
				{
						return this.$store.getters['uifront/getBentukPT']==bentuk_pt?true:false;
				}
	},
		computed: {
				...mapGetters('auth',{
						AUTHENTICATED:'Authenticated',
						ACCESS_TOKEN:'AccessToken',        
						TOKEN:'Token', 
						DEFAULT_ROLE:'DefaultRole',       
						ROLE:'Role',
						CAN_ACCESS:'can',       
						ATTRIBUTE_USER:'AttributeUser',             
				}),
				APP_NAME ()
				{
						return process.env.VUE_APP_NAME;
				},
				photoUser()
		{
			let img=this.ATTRIBUTE_USER('foto');
			var photo;
			if (img == '')
			{
				photo = this.$api.storageURL+'/storage/images/users/no_photo.png';	
			}
			else
			{
				photo = this.$api.storageURL+'/'+img;	
			}
			return photo;
				},
		},
		watch: {
				loginTime: {
						handler(value)
						{
								
								if (value >= 0)
								{
										setTimeout(() => { 
												this.loginTime=this.AUTHENTICATED==true?this.loginTime+1:-1;                                                         
					}, 1000);
								}
								else
								{
										this.$store.dispatch('auth/logout');
										this.$router.replace('/login');
								}
						},
						immediate:true
				},      
		}
}
</script>