<template>
    <KeuanganLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-video-input-component
            </template>
            <template v-slot:name>
                BIAYA KOMPONEN PER PERIODE
            </template>
            <template v-slot:subtitle>
                TAHUN PENDAFTARAN {{tahun_pendaftaran}} - PROGRAM STUDI {{nama_prodi}}
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
                        Halaman ini berisi informasi biaya komponen biaya per periode yang masing-masing.
                    </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar v-if="dashboard!='mahasiswa'&&dashboard!='mahasiswabaru'">
            <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeProdi="changeProdi" ref="filter7" />	
        </template>
        <v-container fluid>
            <v-row class="mb-4" no-gutters v-if="dashboard!='mahasiswa'&&dashboard!='mahasiswabaru'">
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            FILTER
                        </v-card-title>
                        <v-card-text>
                            <v-select
                                label="KELAS"
                                v-model="filter_idkelas"
                                :items="daftar_kelas"
                                item-text="text"
                                item-value="id"                                
                                outlined
                                clearable
                            />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>    
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
                                outlined
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
                        sort-by="idkelas"
                        show-expand
                        :disable-pagination="true"
                        :hide-default-footer="true"
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">     
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR BIAYA KOMPONEN PER PERIODE</v-toolbar-title>
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
                                    :loading="btnLoading"
                                    :disabled="btnLoading"
                                    @click.stop="loadkombiperiode"
                                    v-if="dashboard!='mahasiswa'&&dashboard!='mahasiswabaru'">
                                        GENERATE KOMPONEN BIAYA
                                </v-btn>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.biaya="props">
                            <v-edit-dialog
                                :return-value.sync="props.item.biaya"
                                large                                
                                @save="saveItem({id:props.item.id,biaya:props.item.biaya})"
                                @cancel="cancelItem"
                                @open="openItem"
                                @close="closeItem"> 
                                    {{ props.item.biaya|formatUang }}         
                                    <template v-slot:input>
                                        <div class="mt-4 title">Update Biaya</div>             
                                        <v-currency-field 
                                            label="BIAYA KOMPONEN" 
                                            :min="null"
                                            :max="null"                                            
                                            outlined
                                            autofocus
                                            v-model="props.item.biaya">             
                                        </v-currency-field>
                                    </template>
                            </v-edit-dialog>
                        </template>
                        <template v-slot:item.idkelas="{item}">
                            {{$store.getters['uiadmin/getNamaKelas'](item.idkelas)}}
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
    </KeuanganLayout>
</template>
<script>
import KeuanganLayout from '@/views/layouts/KeuanganLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter7 from '@/components/sidebar/FilterMode7';
export default {
    name:'BiayaKomponenPeriode',
    created()
    {
        this.dashboard=this.$store.getters['uiadmin/getDefaultDashboard'];
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'KEUANGAN',
                disabled:false,
                href:'/keuangan'
            },
            {
                text:'BIAYA KOMPONEN PER PERIODE',
                disabled:true,
                href:'#'
            }
        ];
        this.tahun_pendaftaran = this.$store.getters['uiadmin/getTahunPendaftaran'];         
        this.prodi_id=this.$store.getters['uiadmin/getProdiID']
        this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](this.prodi_id);
        this.daftar_kelas=this.$store.getters['uiadmin/getDaftarKelas'];          
        this.initialize();
    },
    data: () => ({
        dashboard:null,
        firstloading:true,
        breadcrumbs: [],
        tahun_pendaftaran:0,
        prodi_id:null,
        nama_prodi:null,
        filter_idkelas: "",
        daftar_kelas: [],
        
        btnLoading: false,
        datatableLoading:false,
        expanded: [],
        datatable: [],
        headers: [            
            { text: 'ID KOMPONEN', value: 'kombi_id',width:10,sortable:false },                       
            { text: 'NAMA KOMPONEN', value: 'nama_kombi',sortable:false},
            { text: 'PERIODE', value: 'periode',width:150,sortable:false },          
            { text: 'KELAS', value: 'nkelas',width:120,sortable:false },          
            { text: 'BIAYA', value: 'biaya',width:150,sortable:false },          
        ],  
        search: "",    
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
            this.datatableLoading=true;
            await this.$ajax.post('/keuangan/biayakomponenperiode',          
            {
                TA: this.tahun_pendaftaran,
                prodi_id: this.prodi_id
            },
            {
                headers: {
                    Authorization: this.$store.getters['auth/Token']
                }
            }).then(({ data }) => {               
                this.datatable = data.kombi;    
                this.datatableLoading=false;
            });         
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
        loadkombiperiode:async function()
        {
            this.btnLoading = true;
            await this.$ajax.post('/keuangan/biayakomponenperiode/loadkombiperiode',          
            {
                TA: this.tahun_pendaftaran,
                prodi_id: this.prodi_id
            },
            {
                headers: {
                    Authorization: this.$store.getters['auth/Token']
                }
            }).then(({ data }) => {               
                this.datatable = data.kombi;    
                this.btnLoading = false;
            });   
        },
        saveItem:async function ({id,biaya})
        {
            await this.$ajax.post('/keuangan/biayakomponenperiode/updatebiaya',          
            {
                id:id,
                biaya:biaya
            },
            {
                headers: {
                    Authorization: this.$store.getters['auth/Token']
                }
            }).then(() => {               
                this.initialize();            
            });  
        },
        cancelItem()
        {

        },
        openItem()
        {

        },
        closeItem()
        {

        },
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
        },
        filter_idkelas(val)
        {
            if (!this.firstloading)
            {
                if (val && typeof val !== 'undefined' && val.length > 0) 
                {
                    this.datatableLoading=true;
                    this.$ajax.post('/keuangan/biayakomponenperiode',          
                    {
                        TA: this.tahun_pendaftaran,
                        prodi_id: this.prodi_id,
                        filter_idkelas:val
                    },
                    {
                        headers: {
                            Authorization: this.$store.getters['auth/Token']
                        }
                    }).then(({ data }) => {               
                        this.datatable = data.kombi;    
                        this.datatableLoading=false;
                    });         
                }
                else
                {
                    this.initialize();
                }
            }  
        }
    },
    components: {
        KeuanganLayout,
        ModuleHeader,  
        Filter7,     
    },
}
</script>