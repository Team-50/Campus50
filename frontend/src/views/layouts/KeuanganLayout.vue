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
					left
				>
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
		<v-divider
			class="mx-4"
			inset
			vertical
		></v-divider>
		<v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight">
			<v-icon>mdi-menu-open</v-icon>
		</v-app-bar-nav-icon>            
	</v-app-bar>    
		<!-- role sebagai mahasiswabaru dan mahasiswa -->
		<v-navigation-drawer
			v-model="drawer"
			width="300"
			dark
			:class="this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_CSS_CLASS')"
			:color="this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_COLOR')"
			:temporary="temporaryleftsidebar"
			app v-if="dashboard == 'mahasiswabaru'">
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
				<v-list-item :to="{path: '/dashboard/' + $store.getters['auth/AccessToken']}" link :class="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_CSS_CLASS')" :color="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-monitor-dashboard</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>MENU UTAMA</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-divider></v-divider>				
				<v-list-item link to="/keuangan/channelpembayaran">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-numeric-1-circle</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							CHANNEL PEMBAYARAN
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item link to="/spmb/formulirpendaftaran">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-numeric-2-circle</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							BIODATA
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>                    
				<v-list-item link to="/spmb/persyaratan">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-numeric-3-circle</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							PERSYARATAN
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item link to="/keuangan/konfirmasipembayaran">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-numeric-4-circle</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							KONFIRMASI PEMBAYARAN
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>        
			</v-list>
		</v-navigation-drawer>
		<!-- role selain mahasiswabaru dan mahasiswa -->
		<v-navigation-drawer
			v-model="drawer" width="300"
			dark
			:class="this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_CSS_CLASS')"
			:color="this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_COLOR')"
			:temporary="temporaryleftsidebar"
			app v-else>
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
								<v-list-item :to="{path:'/keuangan'}" link  :class="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_CSS_CLASS')" :color="this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')" v-if="CAN_ACCESS('KEUANGAN-GROUP') && dashboard!='mahasiswabaru' && dashboard!='mahasiswa'">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-monitor-dashboard</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>BOARD KEUANGAN</v-list-item-title>
										</v-list-item-content>
								</v-list-item>   
								<v-subheader style="color:#f0935c">DAFTAR ULANG</v-subheader>
								<v-list-item link to="/keuangan/channelpembayaran">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-source-fork</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														CHANNEL PEMBAYARAN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>                        
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-STATUS-TRANSAKSI_BROWSE')" to="/keuangan/statustransaksi">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-cash-register</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														STATUS TRANSAKSI
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>                        
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-KOMPONEN-BIAYA_BROWSE')" to="/keuangan/biayakomponen">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-cash-multiple</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														BIAYA KOMPONEN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>                        
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-BIAYA-KOMPONEN-PERIODE_BROWSE')" to="/keuangan/biayakomponenperiode">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-note-multiple</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														BIAYA KOMPONEN PERIODE
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>                        
								<v-subheader v-if="dashboard!='mahasiswabaru' && dashboard!='mahasiswa'" style="color:#f0935c">METODE PEMBAYARAN</v-subheader>
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-METODE-TRANSFER-BANK_BROWSE')" to="/keuangan/transferbank">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-bank-transfer</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														TRANSFER BANK
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item> 
								<v-subheader style="color:#f0935c">TRANSAKSI</v-subheader>
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-KONFIRMASI-PEMBAYARAN_BROWSE')" to="/keuangan/konfirmasipembayaran">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-eye-check</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														KONFIRMASI PEMBAYARAN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>        
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-TRANSAKSI_BROWSE')" to="/keuangan/transaksi">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-file-document</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														DAFTAR TRANSAKSI
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-divider class="mb-2"/>
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-TRANSAKSI-BIAYA-PENDAFTARAN_BROWSE')" to="/keuangan/transaksi-pendaftaranmhsbaru">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-account-cash</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														PENDAFTARAN MHS BARU
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-TRANSAKSI-PENGEMBANGAN_BROWSE')" to="/keuangan/transaksi-pengembangan">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-book-open</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														TRANSAKSI PENGEMBANGAN
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>                          
								<v-list-item link v-if="CAN_ACCESS('KEUANGAN-TRANSAKSI-SPP_BROWSE')" to="/keuangan/transaksi-spp">
										<v-list-item-icon class="mr-2">
												<v-icon>mdi-calendar-account</v-icon>
										</v-list-item-icon>
										<v-list-item-content>
												<v-list-item-title>
														TRANSAKSI SPP
												</v-list-item-title>
										</v-list-item-content>
								</v-list-item>                          
						</v-list>
				</v-navigation-drawer>
				<v-navigation-drawer v-model="drawerRight" width="300" app fixed right temporary v-if="showrightsidebar">
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
	import {mapGetters} from "vuex";
	export default {
		name: "KeuanganLayout",   
		created()
		{
				this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];        
		},
		props: {
				showrightsidebar: {
						type:Boolean,
						default: true
				},
				temporaryleftsidebar: {
						type:Boolean,
						default:false
				},
		},
			data: () => ({
				loginTime: 0,
				drawer: null,
				drawerRight: null,				
				dashboard: null,
			}),
			methods: {        
		logout() {
			this.loginTime=0;
			this.$ajax.post("/auth/logout",
				{},
				{
					headers: {
						Authorization: this.TOKEN,
					}
				}
			).then(()=> {     
					this.$store.dispatch("auth/logout");	
					this.$store.dispatch("uifront/reinit");	
					this.$store.dispatch("uiadmin/reinit");	
					this.$router.push("/");
			})
			.catch(() => {
					this.$store.dispatch("auth/logout");	
					this.$store.dispatch("uifront/reinit");	
					this.$store.dispatch("uiadmin/reinit");	
					this.$router.push("/");
			});
		},
		isBentukPT (bentuk_pt) {
			return this.$store.getters["uifront/getBentukPT"]==bentuk_pt?true:false;
		}
	},
		computed: {
			...mapGetters("auth", {
				AUTHENTICATED: "Authenticated",
				ACCESS_TOKEN: "AccessToken",
				TOKEN: "Token",
				DEFAULT_ROLE: "DefaultRole",
				ROLE: "Role",
				CAN_ACCESS: "can", 
				ATTRIBUTE_USER: "AttributeUser",
			}),
			APP_NAME() {
				return process.env.VUE_APP_NAME;
			},
			photoUser() {
				let img = this.ATTRIBUTE_USER("foto");
				var photo;
				if (img == "") {
					photo = this.$api.storageURL + "/storage/images/users/no_photo.png";
				} else {
					photo = this.$api.storageURL + "/" + img;
				}
				return photo;
			},
		},
		watch: {
			loginTime: {
				handler(value) {
					if (value >= 0) {
						setTimeout(() => {
							this.loginTime = this.AUTHENTICATED == true ? this.loginTime + 1 : -1;
						}, 1000);
					} else {
						this.$store.dispatch("auth/logout");
						this.$router.replace("/login");
					}
				},
				immediate: true,
			},
		},
	};
</script>