<template>
    <AdminLayout>		
        <v-container v-if="dashboard=='mahasiswabaru'">
            <DashboardMB />
        </v-container>        
        <v-container fluid v-else color="#f1f2f6">
            <v-row>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('DMASTER-GROUP')">
                    <v-card
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#2ed573"
                    @click.native="$router.push('/dmaster')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#2ed573">
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
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SPMB-GROUP')">
                    <v-card
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#1e90ff"
                    @click.native="$router.push('/spmb')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#1e90ff">
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
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#3742fa"
                    @click.native="$router.push('/keuangan')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#3742fa">
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
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#a4b0be"
                    @click.native="$router.push('/akademik')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#a4b0be">
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
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#ff7f50"
                    @click.native="$router.push('/kemahasiswaan')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#ff7f50">
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
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#ffa502"
                    @click.native="$router.push('/elearning')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#ffa502">
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
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#ff6348"
                    @click.native="$router.push('/system-users')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#ff6348">
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
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#ff4757"
                    @click.native="$router.push('/system-setting')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#ff4757">
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
                    class="mx-auto clickable"
                    max-width="344"
                    min-height="210"
                    color="#747d8c"
                    @click.native="$router.push('/system-migration')">
                        <div class="text-center pt-4">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                large
                                elevation ="0"
                                color="white">
                                <v-icon color="#747d8c">
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
    created ()
	{
        this.TOKEN = this.$route.params.token;        
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
        breadcrumbs:[],
        TOKEN:null,
        dashboard:null,

        tahun_pendaftaran:''
	}),
	methods : {
		initialize:async function()
		{	            
            await this.$ajax.get('/auth/me',                
            {
                headers: {
                    Authorization:'Bearer '+this.TOKEN
                }
            }).then(({data})=>{          
                this.dashboard = data.role[0];    
                this.$store.dispatch('uiadmin/changeDashboard',this.dashboard);                                       
            });                 
            this.$store.dispatch('uiadmin/init',this.$ajax);              
            this.tahun_pendaftaran = this.$store.getters['uifront/getTahunPendaftaran'];            
		}
	},
	computed:{
        
	},
    components:{
		AdminLayout,        
        DashboardMB,        
	}
}
</script>