<template>
		<div>
				<v-app-bar
					app
					elevation="0"
					color="#f7f8fd">
					<v-app-bar-nav-icon @click.stop="drawer = !drawer" :class="this.$store.getters['uiadmin/getTheme']('V_APP_BAR_NAV_ICON_CSS_CLASS')"></v-app-bar-nav-icon>
					<v-toolbar-title class="headline clickable" @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken']).catch(err => {})">
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
						[{{DEFAULT_ROLE}}]
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
			<v-divider></v-divider>
						<v-list
							expand
							dense
							rounded>
								<v-list-item :to="{path:'/system-setting'}" v-if="CAN_ACCESS('SYSTEM-SETTING-GROUP')" link :class="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_CSS_CLASS')" :color="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-account</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>BOARD KONFIG. SISTEM</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-subheader style="color:#f0935c">PERGURUAN TINGGI</v-subheader>
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-IDENTITAS-DIRI')" to="/system-setting/identitasdiri">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-identifier</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														IDENTITAS DIRI
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-VARIABLES')" to="/system-setting/variables">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-variable</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														VARIABLES
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-subheader style="color:#f0935c">HEADER</v-subheader>
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-IDENTITAS-DIRI')" to="/system-setting/headerlaporan">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-page-layout-header</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														HEADER LAPORAN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-subheader style="color:#f0935c">SERVER</v-subheader>            
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-VARIABLES')" to="/system-setting/captcha">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-puzzle</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														CAPTCHA
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>    
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-VARIABLES')" to="/system-setting/email">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-email</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														EMAIL
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-subheader style="color:#f0935c">PLUGIN</v-subheader>                    
								<v-list-item link v-if="CAN_ACCESS('PLUGINS-H2H-ZOOMAPI_BROWSE')" to="/system-setting/zoom">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-desktop-mac-dashboard</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														ZOOM
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-subheader v-if="CAN_ACCESS('SYSTEM-SETTING-THEMES_BROWSE')" style="color:#f0935c">THEMES</v-subheader>                    
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-THEMES_BROWSE')" to="/system-setting/themes/colordashboard">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-monitor-clean</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														WARNA DASHBOARD
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-THEMES_BROWSE')" to="/system-setting/themes/layout">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-shape-plus</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														LAYOUT
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
		name:'SystemConfigLayout', 
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