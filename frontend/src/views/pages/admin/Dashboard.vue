<template>
		<AdminLayout v-if="dashboard">		
				<v-container v-if="dashboard=='mahasiswabaru'">
						<DashboardMB />
				</v-container>        
				<v-container fluid v-else color="#f1f2f6">
						<v-row class="mt-n5">
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('DMASTER-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.dmaster"
										@click.native="$router.push('/dmaster')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.dmaster">
																		mdi-monitor-multiple
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														DATA MASTER
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Pengaturan berbagai parameter sebagai referensi dari modul-modul lain dalam sistem.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('PERENCANAAN-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.perencanaan"
										@click.native="$router.push('/perencanaan')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.perencanaan">
																		mdi-airplane-takeoff
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														PERENCANAAN
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul Perencanaan meliputi Renstra P.T dan Penelitian
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SPMB-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.spmb"
										@click.native="$router.push('/spmb')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.spmb">
																		mdi-account-box
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														SPMB
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk mengelola Seleksi Penerimaan Mahasiswa Baru (SPMB) tahun {{tahun_pendaftaran}}.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('KEUANGAN-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.keuangan"
										@click.native="$router.push('/keuangan')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.keuangan">
																		mdi-note-multiple-outline
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														KEUANGAN
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk mengelola Keuangan Perguruan Tinggi.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('AKADEMIK-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.akademik"
										@click.native="$router.push('/akademik')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.akademik">
																		mdi-book
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														AKADEMIK
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk mengelola Akademik Perguruan Tinggi.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-USERS-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.kemahasiswaan"
										@click.native="$router.push('/kemahasiswaan')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.kemahasiswaan">
																		mdi-account-group
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														KEMAHASISWAAN
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk mengelola data kemahasiswaan.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('ELEARNING-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.elearning"
										@click.native="$router.push('/elearning')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.elearning">
																		mdi-lan-connect
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														E-LEARNING
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk mengelola E-Learning.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-USERS-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.user_sistem"
										@click.native="$router.push('/system-users')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.user_sistem">
																		mdi-account-key
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														USER SISTEM
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk mengelola user sistem.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-SETTING-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.konfigurasi_sistem"
										@click.native="$router.push('/system-setting')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.konfigurasi_sistem">
																		mdi-database-plus
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														KONFIGURASI SISTEM
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk mengatur berbagai macam konfigurasi sistem.
												</v-card-subtitle>
										</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
								<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-USERS-GROUP')">
										<v-card
										elevation="0"
										class="mx-auto clickable"
										max-width="344"
										min-height="210"
										:color="color_dashboard.migrasi_sistem"
										@click.native="$router.push('/system-migration')">
												<div class="text-center pt-4">
														<v-btn
																class="mx-2"
																fab
																dark
																large
																elevation ="0"
																color="white">
																<v-icon :color="color_dashboard.migrasi_sistem">
																		mdi-source-pull
																</v-icon>
														</v-btn>
												</div>
												<v-card-title class="white--text font-weight-bold justify-center">
														MIGRASI SISTEM
												</v-card-title>
												<v-card-subtitle class="white--text font-weight-medium text-center">
														Modul ini digunakan untuk melakukan migrasi data atau sistem lama.
												</v-card-subtitle>
										</v-card>
								</v-col>     
						</v-row>
				</v-container>
		</AdminLayout>
</template>
<script>
		import DashboardMB from '@/components/DashboardMahasiswaBaru';
		import AdminLayout from '@/views/layouts/AdminLayout';
		export default {
				name: 'Dashboard',
				created() {
						this.TOKEN = this.$route.params.token;    
						this.tahun_pendaftaran = this.$store.getters['uifront/getTahunPendaftaran'];
						this.color_dashboard=this.$store.getters['uifront/getTheme']('COLOR_DASHBOARD');    
						this.breadcrumbs = [
								{
										text:'HOME',
										disabled:false,
										href:'/dashboard/'+this.TOKEN
								},
								{
										text:'DASHBOARD',
										disabled:true,
										href:'#'
								}
						];	                                     
						this.initialize();
				},
				data: () => ({
						breadcrumbs: [],
						TOKEN:null,
						dashboard:null,

						tahun_pendaftaran: "",
						//theme
						color_dashboard: {}
				}),
				methods : {
					initialize:async function()
					{	            
						await this.$ajax.get('/auth/me',              
						{
								headers: {
										Authorization:'Bearer '+this.TOKEN
								}
						})
						.then(({ data }) => {          
							this.dashboard = data.role[0];    
							this.$store.dispatch('uiadmin/changeDashboard', this.dashboard);     
						})
						.catch(error => {
							if (error.response.status == 401){						
								this.$router.push('/login');
							}
						});     
						this.$store.dispatch('uiadmin/init',this.$ajax); 
					}
				},
				computed: {
						
				},
				components: {
						AdminLayout,      
						DashboardMB,      
				}
		};
</script>