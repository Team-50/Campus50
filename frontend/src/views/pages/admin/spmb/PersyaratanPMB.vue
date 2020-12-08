<template>
    <SPMBLayout :ismhsbaru="$store.getters['uifront/getBentukPT']">
        <ModuleHeader>
            <template v-slot:icon>
                mdi-file-document-edit-outline
            </template>
            <template v-slot:name>
                PERSYARATAN
            </template>
            <template v-slot:subtitle v-if="dashboard!='mahasiswabaru'">
                TAHUN PENDAFTARAN {{tahun_pendaftaran}} - {{nama_prodi}}
            </template>
            <template v-slot:breadcrumbs>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0">
                    <template v-slot:divider>
                        <v-icon>mdi-chevron-right</v-icon>
                    </template>
                </v-breadcrumbs>
            </template>
            <template v-slot:desc v-if="dashboard=='mahasiswabaru'">
                <v-alert                                        
                    color="cyan"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                        Berisi daftar peryaratan pendaftaran yang harus di isi dengan benar dan lengkap; <span class="red--text" v-if="!showcomponentpersyaratan">Bila form isian tidak tampil mohon lengkapi terlebih dahulu Form Biodata</span>
                    </v-alert>
            </template>
            <template v-slot:desc v-else>
                <v-alert                                        
                    color="cyan"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                        Berisi file-file persyaratan pendaftaran mahasiswa baru, silahkan melakukan filter tahun akademik dan program studi.
                    </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid v-if="dashboard=='mahasiswabaru'">
            <FormPersyaratan :user_id="$store.getters['auth/AttributeUser']('id')" v-if="showcomponentpersyaratan"/>
        </v-container>
        <v-container fluid v-else>
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
                                <v-dialog v-model="dialogprofilmhsbaru" :fullscreen="true">                                    
                                    <ProfilMahasiswaBaru :item="datamhsbaru" v-on:closeProfilMahasiswaBaru="closeProfilMahasiswaBaru" v-if="dialogprofilmhsbaru" />                                    
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.foto="{ item }">    
                            <v-badge
                                bordered
                                :color="badgeColor(item)"
                                :icon="badgeIcon(item)"
                                :value="item.jumlah_persyaratan > 0"
                                overlap>                
                                <v-avatar size="30">                                        
                                    <v-img :src="$api.url+'/'+item.foto" />                                                                     
                                </v-avatar>                                                                                                  
                            </v-badge>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="viewItem(item)">
                                mdi-eye
                            </v-icon>
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
        <template v-slot:filtersidebar v-if="dashboard!='mahasiswabaru'">
            <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeProdi="changeProdi" ref="filter7" />	
        </template>
    </SPMBLayout>
</template>
<script>
import SPMBLayout from '@/views/layouts/SPMBLayout';
import ModuleHeader from '@/components/ModuleHeader';
import FormPersyaratan from '@/components/FormPersyaratanPMB';
import ProfilMahasiswaBaru from '@/components/ProfilMahasiswaBaru';
import Filter7 from '@/components/sidebar/FilterMode7';
export default {
    name: 'PersyaratanPMB', 
    created () {
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
                text:'FORMULIR PENDAFTARAN',
                disabled:true,
                href:'#'
            }
        ];this.breadcrumbs[1].disabled=(this.dashboard=='mahasiswabaru'||this.dashboard=='mahasiswa');
        
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
        breadcrumbs:[],        
        dashboard:null,

        btnLoading:false,
        datatableLoading:false,
        expanded:[],
        datatable:[],
        headers: [                        
            { text: '', value: 'foto', width:70 },               
            { text: 'NAMA MAHASISWA', value: 'name',width:350,sortable:true },
            { text: 'NOMOR HP', value: 'nomor_hp',width:100},
            { text: 'KELAS', value: 'nkelas',width:100,sortable:true },
            { text: 'STATUS', value: 'status',width:120,sortable:true },
            { text: 'AKSI', value: 'actions', sortable: false,width:50 },
        ],
        search:'',

        showcomponentpersyaratan:false,
        datamhsbaru:{
            
        }
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
            if (this.dashboard == 'mahasiswabaru' || this.dashboard =='mahasiswa')
            {
                await this.$ajax.get('/spmb/formulirpendaftaran/'+this.$store.getters['auth/AttributeUser']('id'),             
                    {
                        headers:{
                            Authorization:this.$store.getters['auth/Token']
                        }
                    },
                    
                ).then(({data})=>{                       
                    this.showcomponentpersyaratan=data.formulir.idkelas==null||data.formulir.idkelas==''?false:true;                    
                });  
            }
            else
            {
                this.datatableLoading=true;
                await this.$ajax.post('/spmb/pmbpersyaratan',
                {
                    TA:this.tahun_pendaftaran,
                    prodi_id:this.prodi_id,
                },
                {
                    headers: {
                        Authorization:this.$store.getters['auth/Token']
                    }
                }).then(({data})=>{                                   
                    this.datatable = data.persyaratan;   
                    this.datatableLoading=false;
                });  
                this.firstloading=false; 
                this.$refs.filter7.setFirstTimeLoading(this.firstloading);               
            }               
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
            return item.persyaratan<item.jumlah_persyaratan ? 'error':'success' ;           
        },
        badgeIcon(item)
        {
            return item.persyaratan<item.jumlah_persyaratan == 1 ? 'mdi-close-thick':'mdi-check-bold';            
        },     
        viewItem(item)
        {
            this.datamhsbaru = Object.assign({},item);            
            this.dialogprofilmhsbaru = true;
        },
        closeProfilMahasiswaBaru ()
        {
            this.dialogprofilmhsbaru = false;            
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
        prodi_id(val)
        {
            if (!this.firstloading)
            {
                this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](val);
                this.initialize();
            }            
        }
    },
    components:{
        SPMBLayout,
        ModuleHeader,        
        FormPersyaratan,
        ProfilMahasiswaBaru,
        Filter7
    },
}
</script>