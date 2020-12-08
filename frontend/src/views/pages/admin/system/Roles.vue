<template>
    <SystemUserLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-account-group
            </template>
            <template v-slot:name>
                ROLES
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
                    Masing-masing user bisa memiliki beberapa role, minimal 1 role untuk bisa menggunakan sistem. Setiap role memiliki permission.
                    </v-alert>
            </template>
        </ModuleHeader>  
         <v-container fluid>    
            <v-row class="mb-4" no-gutters>
                <v-col xs="12" sm="12" md="12">
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
                <v-col xs="12" sm="12" md="12">
                    <v-data-table
                        :headers="headers"
                        :items="datatable"
                        :search="search"
                        item-key="id"
                        sort-by="name"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait"
                        @click:row="dataTableRowClicked"
                    >
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR ROLE</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-dialog v-model="dialog" max-width="500px" persistent>
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }}</span>
                                            </v-card-title>

                                            <v-card-text>
                                                <v-alert
                                                    color="error"
                                                    :value="form_error_message.length>0"
                                                    icon="mdi-close-octagon-outline"
                                                >
                                                    {{form_error_message}} 
                                                </v-alert>
                                                <v-container fluid>
                                                    <v-row>
                                                        <v-col cols="12" sm="12" md="12">
                                                            <v-text-field 
                                                                v-model="editedItem.name" 
                                                                label="NAMA ROLE"
                                                                :rules="rule_role_name">
                                                            </v-text-field>
                                                        </v-col>                                            
                                                    </v-row>
                                                </v-container>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click.stop="close">BATAL</v-btn>
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
                                <v-dialog v-model="dialogRolePermission" max-width="800px" persistent>                                                                    
                                    <RolePermissions :role="editedItem" :daftarpermissions="daftar_permissions" :permissionsselected="permissions_selected" v-on:closeRolePermissions="closeRolePermissions" />
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                small
                                class="mr-2"
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="setPermission(item)"
                            >
                                mdi-axis-arrow-lock
                            </v-icon>
                            <v-icon
                                small
                                class="mr-2"
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="viewItem(item)"
                            >
                                mdi-eye
                            </v-icon>
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="editItem(item)"
                            >
                                mdi-pencil
                            </v-icon>
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <strong>ID:</strong>{{ item.id }}
                                <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                                <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                            </td>
                        </template>
                        <template v-slot:no-data>
                            Data belum tersedia
                        </template>
                    </v-data-table>                    
                </v-col>
                <v-dialog v-model="dialogdetail" width="800px">                                    
                    <v-card>
                        <v-card-title>
                            <span class="headline">DETAIL ROLE</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container fluid>
                                <v-row no-gutters>
                                    <v-col xs="12" sm="6" md="6">
                                        <v-card flat>
                                            <v-card-title>ID :</v-card-title>
                                            <v-card-subtitle>
                                                {{editedItem.id}}
                                            </v-card-subtitle>
                                        </v-card>
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    <v-col xs="12" sm="6" md="6">
                                        <v-card flat>
                                            <v-card-title>
                                                TANGGAL BUAT :
                                            </v-card-title>
                                            <v-card-subtitle>
                                                {{$date(editedItem.created_at).format('DD/MM/YYYY HH:mm')}}
                                            </v-card-subtitle>
                                        </v-card>
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                </v-row>  
                                <v-row no-gutters>
                                    <v-col xs="12" sm="6" md="6">
                                        <v-card flat>
                                            <v-card-title>NAMA ROLE :</v-card-title>
                                            <v-card-subtitle>
                                                {{editedItem.name}}
                                            </v-card-subtitle>
                                        </v-card>
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    <v-col xs="12" sm="6" md="6">
                                        <v-card flat>
                                            <v-card-title>TANGGAL UBAH :</v-card-title>
                                            <v-card-subtitle>
                                                {{$date(editedItem.updated_at).format('DD/MM/YYYY HH:mm')}}
                                            </v-card-subtitle>
                                        </v-card>
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                </v-row>  
                                <v-row no-gutters>
                                    <v-col cols="12">
                                        <v-data-table                                                        
                                            :headers="headersdetail"
                                            :items="permissions_selected"
                                            item-key="name"
                                            sort-by="name"                                            
                                            class="elevation-1"
                                        >
                                        </v-data-table>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card-text>
                        <v-card-actions>
                            
                        </v-card-actions>
                    </v-card>                                    
                </v-dialog>
            </v-row>
        </v-container>
    </SystemUserLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import SystemUserLayout from '@/views/layouts/SystemUserLayout';
import ModuleHeader from '@/components/ModuleHeader';
import RolePermissions from '@/views/pages/admin/system/RolePermissions';
export default {
    name: 'Roles',
    created()
    {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.ACCESS_TOKEN
            },
            {
                text:'USER SISTEM',
                disabled:false,
                href:'/system-users'
            },
            {
                text:'ROLES',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize()
    },    
    data: () => ({
        breadcrumbs:[],
        datatableLoading:false,
        btnLoading:false,          
        expanded:[],        
        datatable: [],
        daftar_permissions: [],
        permissions_selected: [],
        //tables
        headers: [                        
            { text: 'NAMA ROLE', value: 'name' },
            { text: 'GUARD', value: 'guard_name' },            
            { text: 'AKSI', value: 'actions', sortable: false,width:130 },
        ],
        //tables
        headersdetail: [                        
            { text: 'NAMA PERMISSION', value: 'name' },
            { text: 'GUARD', value: 'guard_name' },                          
        ],
        search:'',
        //form
        form_valid:true,
        dialog: false,
        dialogdetail: false,
        dialogRolePermission: false,
        editedIndex: -1,
        editedItem: {
            id:0,
            name: '',           
            guard: '',           
            created_at: '',           
            updated_at: '',           
        },
        defaultItem: {
            id:0,
            name: '',           
            guard: 'api',           
            created_at: '',           
            updated_at: '',           
        },
        //form rules        
        rule_role_name:[
            value => !!value||"Mohon untuk di isi nama Role !!!",  
            value => /^[A-Za-z]*$/.test(value) || 'Nama Role hanya boleh string',                
        ], 
        form_error_message:''
    }),
    methods: {
        initialize () 
        {
            this.datatableLoading=true;
            this.$ajax.get('/system/setting/roles',{
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data,status})=>{
                if (status==200)
                {
                    this.datatable = data.roles;
                    this.datatableLoading=false;
                }     
            
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
            this.editedIndex = this.datatable.indexOf(item);
            this.editedItem = Object.assign({}, item);

            this.$ajax.get('/system/setting/roles/'+item.id+'/permission',{
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data,status})=>{
                if (status==200)
                {
                    this.permissions_selected = data.permissions;
                }                 
            });  
            
            this.dialogdetail = true;
        },
        editItem (item) {
            this.editedIndex = this.datatable.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },
        setPermission (item) {            
            this.$ajax.get('/system/setting/permissions',{
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data,status})=>{
                if (status==200)
                {
                    this.daftar_permissions = data.permissions;
                }                 
            });          

            this.$ajax.get('/system/setting/roles/'+item.id+'/permission',{
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data,status})=>{
                if (status==200)
                {
                    this.permissions_selected = data.permissions;
                }                 
            });  
            this.dialogRolePermission = true;
            this.editedItem=item;
        
        },
        close () {
            this.btnLoading=false;
            this.dialog = false;
            this.$refs.frmdata.reset(); 
            this.form_error_message='';           
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
                }, 300
            );
        },
        closeRolePermissions () {    
            this.permissions_selected=[];
            this.dialogRolePermission = false;  
        },
        save () {
            this.form_error_message='';
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                if (this.editedIndex > -1) 
                {
                    this.$ajax.post('/system/setting/roles/'+this.editedItem.id,
                        {
                            '_method':'PUT',
                            name:this.editedItem.name.toLowerCase(),
                        },
                        {
                            headers:{
                                Authorization:this.TOKEN
                            }
                        }
                    ).then(({data})=>{   
                        Object.assign(this.datatable[this.editedIndex], data.roles);
                        this.close();
                    }).catch(()=>{
                        this.btnLoading=false;
                    });                    
                    
                } else {
                    this.$ajax.post('/system/setting/roles/store',
                        {
                            name:this.editedItem.name.toLowerCase()
                        },
                        {
                            headers:{
                                Authorization:this.TOKEN
                            }
                        }
                    ).then(({data})=>{   
                        this.datatable.push(data.roles);
                        this.close();
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }
            }
        },        
    },
    computed:{        
        formTitle () {
            return this.editedIndex === -1 ? 'TAMBAH ROLE' : 'EDIT ROLE'
        },
        ...mapGetters('auth',{            
            ACCESS_TOKEN:'AccessToken',          
            TOKEN:'Token',                                  
        }),
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
    },   
    components:{
		SystemUserLayout,
        ModuleHeader,
        RolePermissions
	}


}
</script>