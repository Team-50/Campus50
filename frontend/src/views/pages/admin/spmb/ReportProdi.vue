<template>
    <SPMBLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-newspaper-variant-outline
            </template>
            <template v-slot:name>
                LAPORAN PMB PROGRAM STUDI
            </template>
            <template v-slot:subtitle>
                TAHUN PENDAFTARAN {{tahun_pendaftaran}} - {{nama_prodi}}
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
                    color="orange"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                        Berisi laporan formulir pendaftaran, silahkan melakukan filter tahun akademik dan program studi.
                </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-text-field
                                v-model="search"
                                append-icon="mdi-database-search"
                                label="Search"
                                single-line
                                hide-details
                            ></v-text-field>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="datatable"
                        :search="search"
                        item-key="id"
                        sort-by="name"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR MAHASISWA BARU</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>  
                                <v-btn 
                                    color="indigo darken-3"
                                    small
                                    class="primary"
                                    elevation="0" 
                                    @click.stop="printtoexcel"
                                    :loading="btnLoading"
                                    :disabled="btnLoading">
                                    <v-icon size="21px">mdi-printer</v-icon>
                                </v-btn>   
                            </v-toolbar>
                        </template>
                        <template v-slot:item.foto="{ item }">    
                            <v-badge
                                    bordered
                                    :color="badgeColor(item)"
                                    :icon="badgeIcon(item)"
                                    overlap
                                >                
                                    <v-avatar size="30">             
                                        <v-img :src="$api.storageURL+'/'+item.foto" />                                          
                                    </v-avatar>                                                                       
                            </v-badge>
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">
                                    <strong>ID:</strong>{{ item.id }}
                                    <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                                    <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                                </v-col>     
                            </td>
                        </template>
                        <template v-slot:no-data>
                            Data belum tersedia
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-container>        
        <template v-slot:filtersidebar>
            <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeProdi="changeProdi" ref="filter7" />	
        </template>
    </SPMBLayout>
</template>
<script>
import SPMBLayout from '@/views/layouts/SPMBLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter7 from '@/components/sidebar/FilterMode7';
export default {
    name: 'ReportProdi', 
    created()
    {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'SPMB',
                disabled:false,
                href:'/spmb'
            },
            {
                text:'LAPORAN PMB PROGRAM STUDI',
                disabled:true,
                href:'#'
            }
        ];
        let prodi_id=this.$store.getters['uiadmin/getProdiID'];
        this.prodi_id=prodi_id;
        this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];    
        this.initialize()   
    },
    data: () => ({
        firstloading:true,
        prodi_id:null,
        tahun_pendaftaran:null,
        nama_prodi:null,

        dialogprofilmhsbaru:false,
        breadcrumbs: [],      
        dashboard:null,

        btnLoading: false,
        datatableLoading:false,
        expanded: [],
        datatable: [],
        headers: [                        
            { text: '', value: 'foto', width:70 },             
            { text: 'NAMA MAHASISWA', value: 'name',width:350,sortable:true },
            { text: 'NOMOR HP', value: 'nomor_hp',width:100},
            { text: 'KELAS', value: 'nkelas',width:100,sortable:true },          
        ],
        search: "",
        
        datamhsbaru: {}
    }),
    methods : {
        changeTahunPendaftaran (tahun)
        {
            this.tahun_pendaftaran=tahun;
        },
        changeProdi (id)
        {
            this.prodi_id=id;
        },
		initialize:async function()
		{	
            switch(this.dashboard)
            {
                case 'mahasiswabaru':

                break;
                default :
                    this.datatableLoading=true;
                    await this.$ajax.post('/spmb/formulirpendaftaran',
                    {
                        TA: this.tahun_pendaftaran,
                        prodi_id: this.prodi_id,
                    },
                    {
                        headers: {
                            Authorization: this.$store.getters['auth/Token']
                        }
                    }).then(({ data }) => {               
                        this.datatable = data.pmb;    
                        this.datatableLoading=false;
                    });         
            }
            this.firstloading=false;
            this.$refs.filter7.setFirstTimeLoading(this.firstloading); 
        },
        dataTableRowClicked(item)
        {
            if ( item === this.expanded[0])
            {
                this.expanded=[];    
            }
            else
            {
                this.expanded=[item];
            }               
        },
        badgeColor(item)
        {
            return item.active == 1 ? 'success':'error'
        },
        badgeIcon(item)
        {
            return item.active == 1 ? 'mdi-check-bold':'mdi-close-thick'
        },
        printtoexcel:async function()
        {
            this.btnLoading = true;
            await this.$ajax.post('/spmb/reportspmbprodi/printtoexcel',
                {
                    TA: this.tahun_pendaftaran,                                            
                    prodi_id: this.prodi_id,  
                    nama_prodi: this.nama_prodi,               
                },
                {
                    headers: {
                        Authorization: this.$store.getters['auth/Token']
                    },
                    responseType:'arraybuffer'
                }
            ).then(({ data }) => {              
                const url = window.URL.createObjectURL(new Blob([data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'laporan_prodi_'+Date.now()+'.xlsx');    
                link.setAttribute('id', 'download_laporan');    
                document.body.appendChild(link);
                link.click();         
                document.body.removeChild(link);
                this.btnLoading = false;
            }).catch(() => {
                this.btnLoading = false;
            });     
        }     
    },
    watch: {
        tahun_pendaftaran()
        {
            if (!this.firstloading)
            {
                this.initialize();
            }            
        },
        prodi_id(val)
        {
            if (!this.firstloading)
            {
                this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](val);
                this.initialize();
            }            
        }
    },
    components: {
        SPMBLayout,
        ModuleHeader,              
        Filter7    
    },
}
</script>