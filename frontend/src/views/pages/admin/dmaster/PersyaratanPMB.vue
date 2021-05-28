<template>
    <DataMasterLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-format-list-checks
            </template>
            <template v-slot:name>
                PERSYARATAN PMB
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
                    color="orange"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                    Halaman untuk mengelola persyaratan PMB setiap tahun pendaftaran.
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
                                <v-toolbar-title>DAFTAR PERSYARATAN</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-btn color="warning" small elevation="0" class="ma-2 mr-2 primary" @click.stop="showDialogCopyPersyaratan" v-if="$store.getters['auth/can']('DMASTER-PERSYARATAN-PMB_STORE')">SALIN PERSYARATAN PMB</v-btn>
                                <v-btn 
                                    color="indigo darken-3" 
                                    small
                                    elevation="0"
                                    class="primary" 
                                    @click.stop="tambahItem"                                    
                                    v-if="$store.getters['auth/can']('DMASTER-PERSYARATAN-PMB_STORE')">
                                    <v-icon size="21px">mdi-plus-circle</v-icon>
                                </v-btn>
                                <v-dialog v-model="dialogfrm" max-width="500px" persistent>         
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }}</span>
                                            </v-card-title>
                                            <v-card-text>                                                                           
                                                <v-text-field 
                                                    v-model="formdata.nama_persyaratan" 
                                                    label="NAMA PERSYARATAN"
                                                    outlined
                                                    :rules="rule_nama_persyaratan">
                                                </v-text-field>                    
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
                                                <v-btn 
                                                    color="blue darken-1" 
                                                    text 
                                                    @click.stop="save" 
                                                    :loading="btnLoading"
                                                    :disabled="!form_valid||btnLoading">
                                                        SIMPAN
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </v-dialog>
                                <v-dialog v-model="dialogdetailitem" max-width="750px" persistent>
                                    <v-card>
                                        <v-card-title>
                                            <span class="headline">DETAIL DATA</span>
                                        </v-card-title>
                                        <v-card-text>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>ID :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.id}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>TAHUN PENDAFTARAN :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.ta}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>                 
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>NAMA PERSYARATAN :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.nama_persyaratan}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                     
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>CREATED :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{ $date(formdata.created_at).format('DD/MM/YYYY HH:mm') }}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>                 
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>PROSES :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.proses}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                     
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>UPDATED :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{ $date(formdata.updated_at).format('DD/MM/YYYY HH:mm') }}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>                                                                                                                      
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">KELUAR</v-btn>
                                        </v-card-actions>
                                    </v-card>         
                                </v-dialog>
                                <v-dialog v-model="dialogcopypersyaratan" max-width="500px" persistent>     
                                    <v-form ref="frmdialogcopypersyaratan" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">SALIN PERSYARATAN PMB</span>
                                            </v-card-title>                 
                                            <v-card-text>       
                                                <v-alert
                                                    class="info"
                                                    dark>
                                                    Salin persyaratan PMB dari tahun pendaftaran berikut ke tahun akademik {{tahun_pendaftaran}}
                                                </v-alert>
                                                <v-select
                                                    v-model="dari_tahun_pendaftaran"
                                                    :items="daftar_ta"                                                    
                                                    label="TAHUN PENDAFTARAN"
                                                    :rules="rule_dari_tahun_pendaftaran"
                                                    outlined/>             
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-spacer></v-spacer>
                                                    <v-btn color="blue darken-1" text @click.stop="closedialogsalinpersyaratan">BATAL</v-btn>
                                                    <v-btn 
                                                        color="blue darken-1" 
                                                        text 
                                                        @click.stop="salinpersyaratan" 
                                                        :loading="btnLoading"
                                                        :disabled="!form_valid||btnLoading">
                                                            SALIN
                                                    </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="viewItem(item)">
                                mdi-eye
                            </v-icon>
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="editItem(item)">
                                mdi-pencil
                            </v-icon>
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
            <Filter9 v-on:ChangeTahunPendaftaran="ChangeTahunPendaftaran" ref="filter19" />	
        </template>
    </DataMasterLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import DataMasterLayout from '@/views/layouts/DataMasterLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter9 from '@/components/sidebar/FilterMode9';
export default {
    name:'PersyaratanPMB',
    created() {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.ACCESS_TOKEN
            },
            {
                text:'DATA MASTER',
                disabled:false,
                href:'/dmaster'
            },
            {
                text:'PERSYARATAN PMB',
                disabled:true,
                href:'#'
            }
        ];        
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];    
        this.initialize()
    },
    data: () => ({ 
        firstloading:true,      
        tahun_pendaftaran:null,      

        btnLoading: false,
        datatableLoading:false,
        expanded: [],
        datatable: [],    
        headers: [
            { text: 'PROSES', value: 'proses', sortable:true,width:120  }, 
            { text: 'NAMA PERSYARATAN', value: 'nama_persyaratan',sortable:true },       
            { text: 'TA', value: 'ta',sortable:true,width:80, align:'center' },       
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },
        ],
        search: "",  

        //dialog
        dialogfrm:false,
        dialogdetailitem:false,
        dialogcopypersyaratan:false,

        //form data   
        form_valid:true, 
        daftar_ta: [],       
        dari_tahun_pendaftaran:null,        
        formdata: {
            id: "",    
            proses:'pmb',    
            nama_persyaratan:null,    
            prodi_id:null, 
            ta: "",                 
        },
        formdefault: {
            id: "",    
            proses:'pmb',                     
            nama_persyaratan:null,    
            prodi_id:null, 
            ta: "",                 
        },
        editedIndex: -1,

        //form rules      
        rule_nama_persyaratan: [
            value => !!value || "Mohon Nama Program Studi untuk diisi !!!",            
        ],       
        rule_dari_tahun_pendaftaran: [
            value => !!value || "Mohon Tahun Pendaftaran sumber persyaratan untuk dipilih !!!",            
        ],           
    }),
    methods: {
        ChangeTahunPendaftaran (tahun)
        {
            this.tahun_pendaftaran=tahun;
        },      
        initialize:async function() 
        {
            this.datatableLoading=true;
            await this.$ajax.post('/datamaster/persyaratan',
            {
                TA: this.tahun_pendaftaran
            },
            {
                headers: {
                    Authorization: this.TOKEN
                }
            }).then(({ data }) => {               
                this.datatable = data.persyaratan;
                this.datatableLoading=false;
            }).catch(() => {
                this.datatableLoading=false;
            });  
            this.firstloading=false;
            this.$refs.filter19.setFirstTimeLoading(this.firstloading); 
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
        tambahItem:async function()
        {   
            this.dialogfrm=true;
        },
        viewItem (item) {
            this.formdata=item;     
            this.dialogdetailitem=true;            
        },  
        editItem:async function (item) {            
            this.editedIndex = this.datatable.indexOf(item);      
            this.formdata=item;      
            this.dialogfrm = true
        },  
        showDialogCopyPersyaratan()
        {
            let list_ta = this.$store.getters['uiadmin/getDaftarTA'];  
            for (var i =0; i < list_ta.length; i++)
            {
                var item = list_ta[i];      
                if (this.tahun_pendaftaran!=item.value)
                {
                    this.daftar_ta.push({
                        value:item.value,
                        text:item.text
                    })
                }   
            }            
            this.dialogcopypersyaratan=true;
        },
        save:async function() {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading = true;
                if (this.editedIndex > -1) 
                {
                    await this.$ajax.post('/datamaster/persyaratan/'+this.formdata.id,
                        {
                            '_method':'PUT',        
                            nama_persyaratan: this.formdata.nama_persyaratan,                                                        
                        },
                        {
                            headers: {
                                Authorization: this.TOKEN
                            }
                        }
                    ).then(() => {   
                        this.initialize();
                        this.btnLoading = false;
                        this.closedialogfrm();            
                    }).catch(() => {
                        this.btnLoading = false;
                    });     
                    
                } else {                    
                    await this.$ajax.post('/datamaster/persyaratan/store',
                        {
                            proses: this.formdata.proses,                                
                            nama_persyaratan: this.formdata.nama_persyaratan,                                                                                   
                            ta: this.tahun_pendaftaran,                 
                        },
                        {
                            headers: {
                                Authorization: this.TOKEN
                            }
                        }
                    ).then(() => {   
                        this.initialize();      
                        this.btnLoading = false;
                        this.closedialogfrm();
                    }).catch(() => {
                        this.btnLoading = false;
                    });
                }
            }
        },
        salinpersyaratan()
        {
            if (this.$refs.frmdialogcopypersyaratan.validate())
            {
                this.btnLoading = true;
                this.$ajax.post('/datamaster/persyaratan/salin/'+this.tahun_pendaftaran,
                    {
                        dari_tahun_pendaftaran: this.dari_tahun_pendaftaran,
                        proses:'pmb',
                    },
                    {
                        headers: {
                            Authorization: this.TOKEN
                        }
                    }
                ).then(({ data }) => {   
                    this.datatable=data.persyaratan;
                    this.btnLoading = false;
                    this.closedialogsalinpersyaratan();
                }).catch(() => {
                    this.btnLoading = false;
                });
            }
        },
        deleteItem (item) {           
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus persyaratan '+item.nama_persyaratan+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoading = true;
                    this.$ajax.post('/datamaster/persyaratan/'+item.id,
                        {
                            '_method':'DELETE',
                        },
                        {
                            headers: {
                                Authorization: this.TOKEN
                            }
                        }
                    ).then(() => {   
                        const index = this.datatable.indexOf(item);
                        this.datatable.splice(index, 1);
                        this.btnLoading = false;
                    }).catch(() => {
                        this.btnLoading = false;
                    });
                }                
            });
        },
        closedialogdetailitem() {
            this.dialogdetailitem = false;
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault)
                this.editedIndex = -1
                }, 300
            );
        },
        closedialogfrm() {
            this.dialogfrm = false;
            setTimeout(() => {                              
                this.$refs.frmdata.resetValidation();                     
                this.formdata = Object.assign({}, this.formdefault);  
                this.editedIndex = -1
                }, 300
            );
        },
        closedialogsalinpersyaratan() {                       
            this.dialogcopypersyaratan = false; 
            setTimeout(() => {                
                this.$refs.frmdialogcopypersyaratan.reset();                     
                this.editedIndex = -1
                }, 300
            );           
        },
    },
    computed: {
        ...mapGetters('auth',{            
            ACCESS_TOKEN:'AccessToken',        
            TOKEN:'Token',              
        }),
        formTitle() {
            return this.editedIndex === -1 ? 'TAMBAH PERSYARATAN PMB' : 'UBAH PERSYARATAN PMB'
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
    },
    components: {
        DataMasterLayout,
        ModuleHeader,
        Filter9        
    },

}
</script>