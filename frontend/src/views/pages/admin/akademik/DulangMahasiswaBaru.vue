<template>
    <AkademikLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-monitor-dashboard
            </template>
            <template v-slot:name>
                DAFTAR ULANG MAHASISWA BARU 
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
                    color="cyan"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                    Halaman untuk melakukan daftar calon mahasiswa yang sudah melakukan daftar ulang dan juga untuk melakukan daftar ulang bagi yang belum.
                </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeProdi="changeProdi" ref="filter7" />	
        </template>
        <v-container fluid>             
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdulang" v-model="form_valid" lazy-validation>
                        <v-card
                            :loading="true"
                            color="yellow lighten-4"                            
                            outlined>
                            <v-card-title>
                                TAMBAH DAFTAR ULANG
                            </v-card-title>
                            <v-card-text>            
                                <v-text-field                                     
                                    v-model="formdata.nim"
                                    label="NIM"   
                                    :rules="rule_nim"                                                                  
                                    outlined />    
                                <v-btn 
                                    large                            
                                    @click.stop="tambahItem" 
                                    :loading="btnLoading"
                                    :disabled="!form_valid||btnLoading">
                                        DAFTAR
                                </v-btn>
                            </v-card-text>
                        </v-card>
                    </v-form>
                    <v-dialog v-model="dialogfrm" width="600" persistent v-if="dialogfrm">                                    
                        <v-card color="grey lighten-4">
                            <v-toolbar 
                                elevation="2"> 
                                <v-toolbar-title>TAMBAH DAFTAR ULANG</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-icon                
                                    @click.stop="closedialogfrm()">
                                    mdi-close-thick
                                </v-icon>
                            </v-toolbar>
                            <v-card-text>                                            
                                <v-row>
                                    <v-col cols="12">
                                        <v-card flat>
                                            <v-card-title>ID :</v-card-title>
                                            <v-card-subtitle>
                                                {{data_mhs.user_id}}
                                            </v-card-subtitle>
                                        </v-card>
                                        <v-card flat>
                                            <v-card-title>NOMOR FORMULIR :</v-card-title>
                                            <v-card-subtitle>
                                                {{data_mhs.no_formulir}}
                                            </v-card-subtitle>
                                        </v-card>
                                        <v-card flat>
                                            <v-card-title>NAMA MAHASISWA :</v-card-title>
                                            <v-card-subtitle>
                                                {{data_mhs.nama_mhs}}
                                            </v-card-subtitle>
                                        </v-card>
                                    </v-col>                                                
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                            <v-card>                                                            
                                                <v-card-text>            
                                                    <v-text-field 
                                                        v-model="formdata.nim"
                                                        label="NIM"   
                                                        :rules="rule_nim"                                                                  
                                                        outlined />                                                                 
                                                    <v-select
                                                        label="DOSEN WALI :"
                                                        v-model="formdata.dosen_id"
                                                        :items="daftar_dw"
                                                        item-text="name"
                                                        item-value="id"
                                                        :rules="rule_dw"
                                                        outlined/>  
                                                </v-card-text>
                                                <v-card-actions>
                                                    <v-spacer></v-spacer>
                                                    <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
                                                    <v-btn 
                                                        color="blue darken-1" 
                                                        text 
                                                        @click.stop="save" 
                                                        :loading="btnLoadingTable"
                                                        :disabled="!form_valid||btnLoadingTable">
                                                            SIMPAN
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-form>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>                                
                    </v-dialog>
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
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        :disable-pagination="true"
                        :hide-default-footer="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR MAHASISWA</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.idkelas="{item}">
                            {{$store.getters['uiadmin/getNamaKelas'](item.idkelas)}}
                        </template>
                        <template v-slot:item.actions="{ item }">
                           <v-icon
                                small
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="deleteItem(item)">
                                mdi-delete
                            </v-icon>    
                        </template>           
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">                          
                                    <strong>id:</strong>{{ item.id }}          
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
    </AkademikLayout>
</template>
<script>
import AkademikLayout from '@/views/layouts/AkademikLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter7 from '@/components/sidebar/FilterMode7';
export default {
    name: 'DulangMahasiswaBaru',
    created () {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'AKADEMIK',
                disabled:false,
                href:'/akademik'
            },
            {
                text:'DAFTAR ULANG',
                disabled:false,
                href:'#'
            },
            {
                text:'DAFTAR ULANG MAHASISWA BARU',
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
        nama_prodi:null,
        tahun_pendaftaran:null,

        btnLoading:false,
        btnLoadingTable:false,
        datatableLoading:false,
        expanded:[],
        datatable:[],      
        headers: [
            { text: 'NO. FORMULIR', value: 'no_formulir', sortable:true,width:150  },   
            { text: 'NIM', value: 'nim', sortable:true,width:150  },   
            { text: 'NIRM', value: 'nirm', sortable:true,width:150  },   
            { text: 'NAMA MAHASISWA', value: 'nama_mhs',sortable:true },                           
            { text: 'KELAS', value: 'idkelas',sortable:true,width:120, },                           
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },
        ],  
        search:'', 

        data_mhs:{},  

        //formdata
        form_valid:true,   
        dialogfrm:false, 
        daftar_dw:[],     

        formdata: {                        
            nim:'',
            nirm:'',
            dosen_id:''           
        },
        formdefault: {                        
            nim:'',
            nirm:'',
            dosen_id:''           
        },
        rule_nim:[
            value => !!value||"Nomor Induk Mahasiswa (NIM) mohon untuk diisi !!!",
            value => /^[0-9]+$/.test(value) || 'Nomor Induk Mahasiswa (NIM) hanya boleh angka',
        ], 
        rule_nirm:[
            value => !!value||"Nomor Induk Registrasi Masuk (NIRM) mohon untuk diisi !!!",
            value => /^[0-9]+$/.test(value) || 'Nomor Induk Registrasi Masuk (NIRM) hanya boleh angka',
        ], 
        rule_dw:[
            value => !!value||"Mohon dipilih Dosen Wali untuk Mahasiswa ini !!!"
        ],

    }),
    methods: {
        changeTahunPendaftaran (tahun)
        {
            this.tahun_pendaftaran=tahun;
        },
        changeProdi (id)
        {
            this.prodi_id=id;
        },
        initialize:async function () 
        {
            this.datatableLoading=true;
            await this.$ajax.post('/akademik/dulang/mhsbaru',
            {
                prodi_id:this.prodi_id,
                ta:this.tahun_pendaftaran
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{               
                this.datatable = data.mahasiswa;
                this.datatableLoading=false;
            }).catch(()=>{
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
        async tambahItem ()
        {               
            await this.$ajax.post('/keuangan/transaksi/'+this.formdata.nim+'/sppmhsbaru',
            {
                jenis_id:'nim'
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                                  
                console.log(data);
            });   

            // await this.$ajax.get('/akademik/dosenwali',{
            //     headers: {
            //         Authorization:this.$store.getters['auth/Token']
            //     }
            // }).then(({data})=>{                                  
            //     this.dialogfrm=true;
            //     this.daftar_dw = data.users; 
            // });   

        },
        deleteItem (item)
        {
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus daftar ulang '+item.nama_mhs+' ?', { color: 'red',width:600,'desc':'proses ini juga menghapus seluruh data akademik namun KEUANGAN TETAP ADA.' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoadingTable=true;
                    this.$ajax.post('/akademik/dulang/mhsbaru'+item.id,
                        {
                            '_method':'DELETE',
                        },
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token']
                            }
                        }
                    ).then(()=>{   
                        const index = this.datatable.indexOf(item);
                        this.datatable.splice(index, 1);
                        this.btnLoadingTable=false;
                    }).catch(()=>{
                        this.btnLoadingTable=false;
                    });
                }                
            });
        },
        closedialogfrm () {            
            this.dialogfrm = false;            
            setTimeout(() => {       
                this.formdata = Object.assign({}, this.formdefault);                                
                this.data_mhs = Object.assign({}, {});   
                }, 300
            );
        },
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
        AkademikLayout,
        ModuleHeader,    
        Filter7               
    },
}
</script>