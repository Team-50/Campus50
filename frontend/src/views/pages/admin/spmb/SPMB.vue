<template>
    <SPMBLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-monitor-dashboard
            </template>
            <template v-slot:name>
                SPMB 
            </template>
            <template v-slot:subtitle>
                TAHUN PENDAFTARAN {{tahun_pendaftaran}}
            </template>
            <template v-slot:breadcrumbs>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0">
                    <template v-slot:divider>
                        <v-icon>mdi-chevron-right</v-icon>
                    </template>
                </v-breadcrumbs>
            </template>
            <template v-slot:desc>
                <v-alert                                        
                    color="cyan"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                    dashboard untuk memperoleh ringkasan informasi seleksi penerimaan mahasiswa baru (SPMB).
                    </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter9 v-on:changeTahunPendaftaran="changeTahunPendaftaran" ref="filter9" />
        </template>
        <v-container fluid>
            <v-row dense>
                <v-col xs="12" sm="6" md="3">
                    <v-card                         
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/spmb/pendaftaranbaru')"
                        dark>
                        <v-card-title class="headline">
                            JUMLAH REGISTRASI
                        </v-card-title>   
                        <v-card-subtitle>
                            Jumlah calon mahasiswa baru yang melakukan registrasi
                        </v-card-subtitle>
                        <v-card-text>
                            {{total_registrasi}}
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
                <v-col xs="12" sm="6" md="3">
                    <v-card                         
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/spmb/formulirpendaftaran')"
                        dark>
                        <v-card-title class="headline">
                            JUMLAH ISI FORMULIR
                        </v-card-title>   
                        <v-card-subtitle>
                            Jumlah calon mahasiswa baru yang mengisi formulir pendaftaran
                        </v-card-subtitle>
                        <v-card-text>
                            {{total_isi_formulir}}
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
                <v-col xs="12" sm="6" md="3">
                    <v-card                         
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/spmb/laporankelulusan')"
                        dark>
                        <v-card-title class="headline">
                            JUMLAH LULUS
                        </v-card-title>   
                        <v-card-subtitle>
                            Jumlah calon mahasiswa baru yang dinyatakan lulus
                        </v-card-subtitle>
                        <v-card-text>
                            {{total_lulus}}
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
                <v-col xs="12" sm="6" md="3">
                    <v-card                         
                        class="clickable green darken-1"
                        color="#385F73" 
                        @click.native="$router.push('/spmb/laporankelulusan')"
                        dark>
                        <v-card-title class="headline">
                            JUMLAH TIDAK LULUS
                        </v-card-title>   
                        <v-card-subtitle>
                            Jumlah calon mahasiswa baru yang dinyatakan tidak lulus
                        </v-card-subtitle>
                        <v-card-text>
                            {{total_tidak_lulus}}
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
            </v-row>
            <v-row>
                <v-col xs="12" sm="12" md="6">
                    <v-card class="mb-3">
                        <v-card-title>
                            Registrasi Mahasiswa Baru
                        </v-card-title>
                        <v-card-subtitle>
                            Jumlah registrasi form pendaftaran calon mahasiswa baru per program studi.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-data-table 
                                :loading="datatableLoading"
                                loading-text="Loading... Please wait"
                                :dense="true"                                                  
                                :disable-pagination="true"
                                :hide-default-footer="true"
                                :headers="headers"
                                :items="daftar_registrasi">                                    
                                <template v-slot:body.append v-if="daftar_registrasi.length > 0">
                                    <tr class="grey lighten-4 font-weight-black">
                                        <td class="text-right" colspan="2">TOTAL</td>
                                        <td class="text-right">{{total_registrasi}}</td>                                                                                
                                    </tr>
                                </template>
                                <template v-slot:no-data>                            
                                    belum ada data calon mahasiswa baru yang mengisi formulir pendaftaran.
                                </template>                           
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/> 
                <v-col xs="12" sm="12" md="6">
                    <v-card class="mb-3">
                        <v-card-title>
                            Melengkapi Formulir Calon Mahasiswa Baru
                        </v-card-title>
                        <v-card-subtitle>
                            Jumlah calon mahasiswa baru yang mengisi formulir pendaftaran per program studi.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-data-table 
                                :loading="datatableLoading"
                                loading-text="Loading... Please wait"
                                :dense="true"                                                  
                                :disable-pagination="true"
                                :hide-default-footer="true"
                                :headers="headers"
                                :items="daftar_isi_formulir">                                    
                                <template v-slot:body.append v-if="daftar_isi_formulir.length > 0">
                                    <tr class="grey lighten-4 font-weight-black">
                                        <td class="text-right" colspan="2">TOTAL</td>
                                        <td class="text-right">{{total_isi_formulir}}</td>                                                                                
                                    </tr>
                                </template>
                                <template v-slot:no-data>                            
                                    belum ada data calon mahasiswa baru yang mengisi formulir.
                                </template>                           
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
            </v-row>  
            <v-row>
                <v-col xs="12" sm="12" md="6">
                    <v-card class="mb-3">
                        <v-card-title>
                            Mahasiswa Lulus
                        </v-card-title>
                        <v-card-subtitle>
                            Jumlah calon mahasiswa baru yang lulus per program studi.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-data-table 
                                :loading="datatableLoading"
                                loading-text="Loading... Please wait"
                                :dense="true"                                                  
                                :disable-pagination="true"
                                :hide-default-footer="true"
                                :headers="headers"
                                :items="daftar_lulus">                                    
                                <template v-slot:body.append v-if="daftar_lulus.length > 0">
                                    <tr class="grey lighten-4 font-weight-black">
                                        <td class="text-right" colspan="2">TOTAL</td>
                                        <td class="text-right">{{total_lulus}}</td>                                                                                
                                    </tr>
                                </template>
                                <template v-slot:no-data>                            
                                    belum ada data calon mahasiswa baru yang lulus.
                                </template>                           
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
                <v-col xs="12" sm="12" md="6">
                    <v-card class="mb-3">
                        <v-card-title>
                            Mahasiswa Tidak Lulus
                        </v-card-title>
                        <v-card-subtitle>
                            Jumlah calon mahasiswa baru yang tidak lulus per program studi.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-data-table 
                                :loading="datatableLoading"
                                loading-text="Loading... Please wait"
                                :dense="true"                                                  
                                :disable-pagination="true"
                                :hide-default-footer="true"
                                :headers="headers"
                                :items="daftar_tidak_lulus">                                    
                                <template v-slot:body.append v-if="daftar_lulus.length > 0">
                                    <tr class="grey lighten-4 font-weight-black">
                                        <td class="text-right" colspan="2">TOTAL</td>
                                        <td class="text-right">{{total_tidak_lulus}}</td>                                                                                
                                    </tr>
                                </template>
                                <template v-slot:no-data>                            
                                    belum ada data calon mahasiswa baru yang tidak lulus.
                                </template>                           
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  

            </v-row>
        </v-container>
    </SPMBLayout>
</template>
<script>
import SPMBLayout from '@/views/layouts/SPMBLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter9 from '@/components/sidebar/FilterMode9';
export default {
    name: 'SPMB',
    created ()
	{
		this.breadcrumbs = [
			{
				text:'HOME',
				disabled:false,
				href:'/dashboard/'+this.$store.getters['auth/AccessToken']
			},
			{
				text:'SPMB',
				disabled:true,
				href:'#'
			}
        ];				
        this.tahun_pendaftaran = this.$store.getters['uiadmin/getTahunPendaftaran']; 
    },
    mounted()
    {
        this.initialize();
    },
    data: () => ({
        datatableLoading:false,
        firstloading:true,
        breadcrumbs:[],        
        tahun_pendaftaran:0,
        
        //statistik
        total_registrasi:0,
        daftar_registrasi:[], 

        total_isi_formulir:0,
        daftar_isi_formulir:[],        

        total_lulus:0,
        daftar_lulus:[],        
        
        total_tidak_lulus:0,
        daftar_tidak_lulus:[],        
        headers: [                        
            { text: 'NAMA PRODI', value: 'nama_prodi', sortable:false},               
            { text: 'JENJANG', value: 'nama_jenjang', sortable:false},               
            { text: 'JUMLAH', align:'end',value: 'jumlah', sortable:false},                
        ], 
    }),
    methods : {
        changeTahunPendaftaran (tahun)
        {
            this.tahun_pendaftaran=tahun;
        },
		initialize:async function()
		{	
            this.datatableLoading=true;            
            await this.$ajax.post('/dashboard/pmb',
            {
                TA:this.tahun_pendaftaran,                
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                            
                this.daftar_registrasi = data.daftar_registrasi;
                this.total_registrasi = data.total_registrasi;
                
                this.daftar_isi_formulir = data.daftar_isi_formulir;
                this.total_isi_formulir = data.total_isi_formulir;
                
                this.daftar_lulus = data.daftar_lulus;
                this.total_lulus = data.total_lulus;

                this.daftar_tidak_lulus = data.daftar_tidak_lulus;
                this.total_tidak_lulus = data.total_tidak_lulus;

                this.datatableLoading=false;
            }).catch(()=>{
                this.datatableLoading=false;
            });
            this.firstloading=false;            
            this.$refs.filter9.setFirstTimeLoading(this.firstloading); 
        }
    },
    watch:{
        tahun_pendaftaran()
        {
            if (!this.firstloading)
            {
                this.initialize();
            }            
        },
    },
    components:{
        SPMBLayout,
        ModuleHeader,           
        Filter9,        
    },
}
</script>