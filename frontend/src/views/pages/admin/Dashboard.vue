<template>
    <AdminLayout>		
        <v-container v-if="dashboard=='mahasiswabaru'">
            <DashboardMB />
        </v-container>        
        <v-container fluid v-else>
            <v-row>
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('DMASTER-GROUP')">
                    <v-card 
                        min-height="140"
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/dmaster')"
                        dark>
                        <v-card-title class="headline">
                            DATA MASTER
                        </v-card-title>                        
                        <v-card-text>
                            Pengaturan berbagai parameter sebagai referensi dari modul-modul lain dalam sistem.
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SPMB-GROUP')">
                    <v-card 
                        min-height="140"
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/spmb')"
                        dark>
                        <v-card-title class="headline">
                            SPMB
                        </v-card-title>                        
                        <v-card-text>
                            Modul ini digunakan untuk mengelola Seleksi Penerimaan Mahasiswa Baru (SPMB) tahun {{tahun_pendaftaran}}.
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('KEUANGAN-GROUP')">
                    <v-card 
                        min-height="140"
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/keuangan')"
                        dark>
                        <v-card-title class="headline">
                            KEUANGAN
                        </v-card-title>                        
                        <v-card-text>
                            Modul ini digunakan untuk mengelola Keuangan Perguruan Tinggi.
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('AKADEMIK-GROUP')">
                    <v-card 
                        min-height="140"
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/akademik')"
                        dark>
                        <v-card-title class="headline">
                            AKADEMIK
                        </v-card-title>                        
                        <v-card-text>
                            Modul ini digunakan untuk mengelola Akademik Perguruan Tinggi.
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-USERS-GROUP')">
                    <v-card 
                        min-height="140"
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/system-users')"
                        dark>
                        <v-card-title class="headline">
                            USER SISTEM
                        </v-card-title>                        
                        <v-card-text>
                            Modul ini digunakan untuk mengelola user sistem.
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-SETTING-GROUP')">
                    <v-card 
                        min-height="140"
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/system-setting')"
                        dark>
                        <v-card-title class="headline">
                            KONFIGURASI SISTEM
                        </v-card-title>                        
                        <v-card-text>
                            Modul ini digunakan untuk mengatur berbagai macam konfigurasi sistem.
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-USERS-GROUP')">
                    <v-card 
                        min-height="140"
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/system-migration')"
                        dark>
                        <v-card-title class="headline">
                            MIGRASI SISTEM
                        </v-card-title>                        
                        <v-card-text>
                            Modul ini digunakan untuk melakukan migrasi data atau sistem lama.
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
                <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-USERS-GROUP')">
                    <template>
  <v-card
    class="mx-auto"
    color="#26c6da"
    dark
    max-width="400"
  >
    <v-card-title>
      <v-icon
        large
        left
      >
        mdi-twitter
      </v-icon>
      <span class="title font-weight-light">Twitter</span>
    </v-card-title>

    <v-card-text class="headline font-weight-bold">
      "Turns out semicolon-less style is easier and safer in TS because most gotcha edge cases are type invalid as well."
    </v-card-text>

    <v-card-actions>
      <v-list-item class="grow">
        <v-list-item-avatar color="grey darken-3">
          <v-img
            class="elevation-6"
            alt=""
            src="https://avataaars.io/?avatarStyle=Transparent&topType=ShortHairShortCurly&accessoriesType=Prescription02&hairColor=Black&facialHairType=Blank&clotheType=Hoodie&clotheColor=White&eyeType=Default&eyebrowType=DefaultNatural&mouthType=Default&skinColor=Light"
          ></v-img>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>Evan You</v-list-item-title>
        </v-list-item-content>

        <v-row
          align="center"
          justify="end"
        >
          <v-icon class="mr-1">
            mdi-heart
          </v-icon>
          <span class="subheading mr-2">256</span>
          <span class="mr-1">·</span>
          <v-icon class="mr-1">
            mdi-share-variant
          </v-icon>
          <span class="subheading">45</span>
        </v-row>
      </v-list-item>
    </v-card-actions>
  </v-card>
</template>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>            
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