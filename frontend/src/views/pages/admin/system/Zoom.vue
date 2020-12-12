<template>
    <SystemConfigLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-desktop-mac-dashboard
            </template>
            <template v-slot:name>
                ZOOM
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
                    Setting akun Zoom untuk pertemuan tatap muka
                </v-alert>
            </template>
        </ModuleHeader>   
        <v-container>     
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
                                <v-toolbar-title>DATA AKUN ZOOM</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-dialog v-model="dialogfrm" max-width="600px" persistent>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="primary" dark class="mb-2" v-on="on">TAMBAH</v-btn>
                                    </template>
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }}</span>
                                            </v-card-title>
                                            <v-card-text>
                                                <v-text-field 
                                                    v-model="formdata.zoom_id" 
                                                    label="ZOOM ID"
                                                    outlined
                                                    :disabled="true">
                                                </v-text-field>
                                                <v-text-field 
                                                    v-model="formdata.email" 
                                                    label="EMAIL ZOOM"
                                                    outlined
                                                    :rules="rule_email">
                                                </v-text-field>
                                                <v-text-field 
                                                    v-model="formdata.api_key" 
                                                    label="API KEY"
                                                    outlined
                                                    :rules="rule_api_key">
                                                </v-text-field>
                                                <v-text-field 
                                                    v-model="formdata.api_secret" 
                                                    label="API SECRET"
                                                    outlined
                                                    :rules="rule_api_secret">
                                                </v-text-field> 
                                                <v-text-field 
                                                    v-model="formdata.im_token" 
                                                    label="IM TOKEN"
                                                    outlined
                                                    :rules="rule_im_token">
                                                </v-text-field>       
                                                <v-text-field 
                                                    v-model="formdata.jwt_token" 
                                                    label="JWT TOKEN"
                                                    outlined
                                                    :disabled="true">
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
                                <v-dialog v-model="dialogdetailitem" max-width="500px" persistent>
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
                                                        <v-card-title>CREATED :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{$date(formdata.created_at).format('DD/MM/YYYY HH:mm')}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>NAME :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.name}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>UPDATED :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{$date(formdata.updated_at).format('DD/MM/YYYY HH:mm')}}
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
                            </v-toolbar>
                        </template>
                        <template v-slot:item.id="{ item }">    
                           {{item.id}}
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
    </SystemConfigLayout>
</template>
<script>
import SystemConfigLayout from '@/views/layouts/SystemConfigLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name:'Zoom',
    created () {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'KONFIGURASI SISTEM',
                disabled:false,
                href:'/system-setting'
            },
            {
                text:'PLUGIN - ZOOM',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize()
    },  
    data: () => ({ 
        btnLoading:false,
        datatableLoading:false,
        expanded:[],
        datatable:[],
        headers: [         
            { text: 'ZOOM ID', value: 'zoom_id' },
            { text: 'EMAIL ZOOM', value: 'email' },
            { text: 'API KEY', value: 'api_key' },
            { text: 'STATUS', value: 'status' },
            { text: 'KETERANGAN', value: 'keterangan' },
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },
        ],
        search:'',    

        //dialog
        dialogfrm:false,
        dialogdetailitem:false,

        //form data   
        form_valid:true,         
        formdata: {
            id:0,
            zoom_id:'',                        
            email:'',                        
            api_key:'',
            api_secret:'',
            im_token:'',
            jwt_token:'',
            status:'',
            desc:'',                        
            created_at: '',           
            updated_at: '',           

        },
        formdefault: {
            id:0,
            zoom_id:'',                        
            email:'',                        
            api_key:'',
            api_secret:'',
            im_token:'',
            jwt_token:'',
            status:'',
            desc:'',                        
            created_at: '',           
            updated_at: '',         
        },
        editedIndex: -1,

        //form rules  
        rule_email:[
            value => !!value||"Mohon untuk mengisi Email !!!",
            v => /.+@.+\..+/.test(v) || 'Format E-mail mohon di isi dengan benar',
        ], 
        rule_api_key:[
            value => !!value||"Mohon untuk di isi API Key !!!",  
        ], 
        rule_api_secret:[
            value => !!value||"Mohon untuk di isi API Secret !!!",  
        ],
        rule_im_token:[
            value => !!value||"Mohon untuk di isi IM Token !!!",  
        ],
    }),
    methods: {
        initialize:async function () 
        {
            this.datatableLoading=true;
            await this.$ajax.get('/h2h/zoom',{
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{               
                this.datatable = data.zoom;
                this.datatableLoading=false;
            }).catch(()=>{
                this.datatableLoading=false;
            });  
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
        viewItem (item) {
            this.formdata=item;      
            this.dialogdetailitem=true;              
            // this.$ajax.get('/path/'+item.id,{
            //     headers: {
            //         Authorization:this.$store.getters['auth/Token']
            //     }
            // }).then(({data})=>{               
                                           
            // });                      
        },    
        editItem (item) {
            this.editedIndex = this.datatable.indexOf(item);
            this.formdata = Object.assign({}, item);
            this.dialogfrm = true;
        },    
        save:async function () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                if (this.editedIndex > -1) 
                {
                    await this.$ajax.post('/path/'+this.formdata.id,
                        {
                            '_method':'PUT',
                            name:this.formdata.name,                       
                        },
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token']
                            }
                        }
                    ).then(({data})=>{   
                        Object.assign(this.datatable[this.editedIndex], data.object);
                        this.closedialogfrm();
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });                 
                    
                } else {
                    await this.$ajax.post('/path/store',
                        {
                            name:this.formdata.name,                            
                        },
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token']
                            }
                        }
                    ).then(({data})=>{   
                        this.datatable.push(data.object);
                        this.closedialogfrm();
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }
            }
        },
        deleteItem (item) {           
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus data dengan ID '+item.id+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoading=true;
                    this.$ajax.post('/path/'+item.id,
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
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }                
            });
        },
        closedialogdetailitem () {
            this.dialogdetailitem = false;            
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault)
                this.editedIndex = -1
                }, 300
            );
        },
        closedialogfrm () {
            this.dialogfrm = false;            
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault);                
                this.editedIndex = -1
                this.$refs.frmdata.reset(); 
                }, 300
            );
        },
    },
    computed: {        
        formTitle () {
            return this.editedIndex === -1 ? 'TAMBAH DATA' : 'UBAH DATA'
        },        
    },
    components:{
        SystemConfigLayout,
        ModuleHeader,        
    },

}
</script>