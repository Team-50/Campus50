<template>
		<div>
				<v-app-bar
						app
						elevation="0"
						color="#f7f8fd">
						<v-app-bar-nav-icon @click.stop="drawer = !drawer" :class="this.$store.getters['uiadmin/getTheme']('V_APP_BAR_NAV_ICON_CSS_CLASS')"></v-app-bar-nav-icon>
						<v-toolbar-title
								class="headline clickable"
								@click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken']).catch(err => {})">
				<span class="headline font-weight-bold mx-1">{{APP_NAME}}</span>
			</v-toolbar-title>
						<v-spacer></v-spacer>   

						<v-divider
								class="mx-4"
								inset
								vertical
						></v-divider>

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

				<v-navigation-drawer
						v-model="drawer"
						width="300"
						dark
						:class="this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_CSS_CLASS')"
						:color="this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_COLOR')"
						:temporary="temporaryleftsidebar"
						app>

			<v-list-item>
				<v-list-item-avatar>
					<v-img :src="photoUser" @click.stop="toProfile"></v-img>
				</v-list-item-avatar>
				<v-list-item-content>					
					<v-list-item-title class="title">
						<span class="headline font-weight-bold mx-1" style="color:#FFFFFF" dark>
							{{ATTRIBUTE_USER('username')}}
						</span>
					</v-list-item-title>
					<v-list-item-subtitle>
						<span style="color:#FFFFFF" dark>
							[{{DEFAULT_ROLE}}]
						</span>
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
			<v-divider></v-divider>

						<v-list
							expand
							dense
							rounded>
								<v-list-item :to="{path:'/system-users'}" v-if="CAN_ACCESS('SYSTEM-USERS-GROUP')" link :class="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_CSS_CLASS')" :color="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')" >
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-desktop-mac-dashboard</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>BOARD USERS</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-list-item link to="/system-users/profil">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-account</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														PROFIL
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-PERMISSIONS')" to="/system-users/permissions">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-account-key</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														PERMISSIONS
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>  
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-ROLES')" to="/system-users/roles">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-account-group</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														ROLES
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>  
								<v-divider v-if="CAN_ACCESS('SYSTEM-SETTING-ROLES')"/>
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-SUPERADMIN_BROWSE')" to="/system-users/superadmin">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-monitor-eye</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														SUPERADMIN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-KEUANGAN_BROWSE')" to="/system-users/keuangan">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-cash-multiple</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														KEUANGAN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-PMB_BROWSE')" to="/system-users/pmb">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-certificate</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														TIM PMB
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-AKADEMIK_BROWSE')" to="/system-users/akademik">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-vector-polygon</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														AKADEMIK
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-PROGRAM-STUDI_BROWSE')" to="/system-users/prodi">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-vector-polyline</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														PROGRAM STUDI
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>  
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-PUSLAHTA_BROWSE')" to="/system-users/puslahta">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-vector-radius</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														PUSLAHTA
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>        
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-DOSEN_BROWSE')" to="/system-users/dosen">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-badge-account</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														DOSEN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>         
								<v-list-item link v-if="dashboard=='dosen'" to="/system-users/biodatadiridosen">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-account</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														BIODATA DIRI
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
						</v-list>
				</v-navigation-drawer>        
				<v-main class="mx-4 mb-4">			
			<slot />
		</v-main>
		</div>    
</template>
<script>
import {mapGetters} from 'vuex';
export default {
		name:'SystemUserLayout',
		created()
		{
				this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];          
		},
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
				
				dashboard:null,
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