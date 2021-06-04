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
																{{ROLE}}
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

						<v-divider
								class="mx-4"
								inset
								vertical
						></v-divider>
			<v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight">
								<v-icon>mdi-menu-open</v-icon>
			</v-app-bar-nav-icon>            
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
						{{ATTRIBUTE_USER('username')}}
					</v-list-item-title>
					<v-list-item-subtitle>
						{{ROLE}}
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
			<v-divider></v-divider>
						
						<v-list
							expand
							dense
							rounded>
								<v-list-item :to="{path:'/elearning'}" link :class="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_CSS_CLASS')" :color="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')" v-if="CAN_ACCESS('ELEARNING-GROUP')">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-monitor-dashboard</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>BOARD E-LEARNING</v-list-item-title>
										</v-list-item-content>
								</v-list-item>   
								<v-subheader style="color:#f0935c">DATA MASTER</v-subheader>                
								<v-list-item link to="/elearning/kelas" v-if="CAN_ACCESS('ELEARNING-GROUP')">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-book-multiple</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														MATERI
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>   
								<v-subheader style="color:#f0935c">PERKULIAHAN</v-subheader>
								<v-list-item link to="/elearning/kelas" v-if="CAN_ACCESS('ELEARNING-GROUP')">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-google-classroom</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														KELAS
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>   
						</v-list>
				</v-navigation-drawer>
				<v-navigation-drawer
					v-model="drawerRight" width="300" app fixed right temporary v-if="showrightsidebar">
						<v-list dense>
								<v-list-item>		
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-menu-open</v-icon>
										</v-list-item-icon>			
										<v-list-item-content>									
												<v-list-item-title class="title">
														OPTIONS
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-divider></v-divider>
								<v-list-item class="teal lighten-5 mb-5">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-filter</v-icon>
										</v-list-item-icon>
										<v-list-item-content>								
												<v-list-item-title>FILTER</v-list-item-title>
										</v-list-item-content>		
								</v-list-item>
								<slot name="filtersidebar"/>		                	
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
		name:'AkademikLayout',   
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
				drawerRight:null, 
				
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
				paramid ()
				{
						var id='empty';            
						switch (this.$route.name)
						{
								case 'PerkuliahanPenyelenggaraanDosenPengampu':
										id=this.$route.params.idpenyelenggaraan;
								break;
								case 'PerkuliahanKRSDetail':
										id=this.$route.params.krsid;
								break;
								case 'PerkuliahanKRSTambahMatkul':
										id=this.$route.params.krsid;
								break;
								case 'PerkuliahanPembagianKelasPeserta':
										id=this.$route.params.kelas_mhs_id;
								break;
						}            
						return id;
				}
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