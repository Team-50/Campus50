<template>
    <AkademikLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-account
            </template>
            <template v-slot:name>
                DOSEN WALI
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
                        Halaman ini berisi daftar DOSEN WALI / PENDAMPING AKADEMIK yang bertanggungjawab untuk membantu pembelajaran mahasiswa.
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
                        :items="daftar_users"
                        :search="search"
                        item-key="id"
                        sort-by="name"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait"
                    >
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR DOSEN WALI</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer> 
                                <v-dialog v-model="dialogAlihkan" max-width="500px" persistent>
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">Alihkan Mahasiswa</span>
                                            </v-card-title>                                            
                                            <v-card-subtitle>
                                                Alihkan Mahasiswa dari Dosen wali ini ke Dosen wali lain.
                                            </v-card-subtitle>                                            
                                            <v-card-text>                                                                                                
                                                                                                                                
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click.stop="close">BATAL</v-btn>
                                                <v-btn 
                                                    color="blue darken-1" 
                                                    text 
                                                    @click.stop="alihkan" 
                                                    :loading="btnLoading"
                                                    :disabled="!form_valid||btnLoading">ALIHKAN</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </v-dialog>                                
                            </v-toolbar>
                        </template>
                        <template v-slot:item.nidn="{ item }">
                            {{(item.nidn && item.nidn.length > 0) > 0 ? item.nidn:'N.A'}}
                        </template>
                        <template v-slot:item.is_dw="{ item }">
                            {{item.is_dw == false ? 'BUKAN':'YA'}}
                        </template>
                        <template v-slot:item.actions="{ item }">                            
                            <v-icon
                                small
                                class="mr-2"
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="editItem(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                small
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="deleteItem(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </template>
                        <template v-slot:item.foto="{ item }">                            
                            <v-avatar size="30">
                                <v-img :src="$api.url+'/'+item.foto" />                                
                            </v-avatar>                                                                                                  
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">
                                    <strong>ID:</strong>{{ item.id }}
                                    <strong>Email:</strong>{{ item.email }}
                                    <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                                    <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                                </v-col>                                
                            </td>
                        </template>
                        <template v-slot:no-data>
                            Data belum tersedia
                        </template>
                    </v-data-table>
                    <p class="text--secondary">DW : Dosen Wali</p>
                </v-col>
            </v-row>
        </v-container>
    </AkademikLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import AkademikLayout from '@/views/layouts/AkademikLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name: 'DosenWali',  
    created () {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.ACCESS_TOKEN
            },
            {
                text:'AKADEMIK',
                disabled:false,
                href:'/akademik'
            },
            {
                text:'DOSEN WALI',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize()
    },  
   
    data: () => ({ 
        role_id:0,
        datatableLoading:false,
        btnLoading:false,      
        //tables
        headers: [                        
            { text: '', value: 'foto' },
            { text: 'USERNAME', value: 'username',sortable:true },
            { text: 'NAMA DOSEN', value: 'name',sortable:true },
            { text: 'NIDN', value: 'nidn',sortable:true },     
            { text: 'NIPY', value: 'nipy',sortable:true },     
            { text: 'NOMOR HP', value: 'nomor_hp',sortable:true },     
            { text: 'DW', value: 'is_dw',sortable:true },     
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },
        ],
        expanded:[],
        search:'',
        daftar_users: [],        

        //form
        form_valid:true,
        dialog: false,
        dialogAlihkan: false,        
        editedIndex: -1,        
        editedItem: {
            id:0,
            username: '',           
            password: '',           
            name: '',      
            nidn:'',   
            nipy:'',         
            email: '',           
            nomor_hp:'',                 
            is_dw:false,      
            created_at: '',           
            updated_at: '',   
        },
        defaultItem: {
            id:0,
            username: '',           
            password: '',           
            name: '',    
            nidn:'',
            nipy:'',       
            email: '',           
            nomor_hp: '',          
            is_dw:false,    
            created_at: '',           
            updated_at: '',        
        },
        //form rules        
        rule_user_name:[
            value => !!value||"Mohon untuk di isi nama Dosen !!!",  
            value => /^[A-Za-z\s]*$/.test(value) || 'Nama Dosen hanya boleh string dan spasi',                
        ],         
        rule_nipy:[
            value => !!value||"Mohon untuk di isi Nomor Induk Pegawai Yayasan (NIPY) dari User ini !!!",                          
        ], 
        rule_user_email:[
            value => !!value||"Mohon untuk di isi email User !!!",  
            value => /.+@.+\..+/.test(value) || 'Format E-mail harus benar',       
        ], 
        rule_user_nomorhp:[
            value => !!value||"Nomor HP mohon untuk diisi !!!",
            value => /^\+[1-9]{1}[0-9]{1,14}$/.test(value) || 'Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388',
        ], 
        rule_user_username:[
            value => !!value||"Mohon untuk di isi username User !!!",  
            value => /^[A-Za-z_]*$/.test(value) || 'Username hanya boleh string dan underscore',                    
        ], 
        rule_user_password:[
            value => !!value||"Mohon untuk di isi password User !!!",
            value => {
                if (value && typeof value !== 'undefined' && value.length > 0){
                    return value.length >= 8 || 'Minimial Password 8 karaketer';
                }
                else
                {
                    return true;
                }
            }
        ], 
        rule_user_passwordEdit:[
            value => {
                if (value && typeof value !== 'undefined' && value.length > 0){
                    return value.length >= 8 || 'Minimial Password 8 karaketer';
                }
                else
                {
                    return true;
                }
            }
        ],
    }),
    methods: {
        initialize:async function () 
        {
            this.datatableLoading=true;
            await this.$ajax.get('/akademik/dosenwali',{
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data})=>{               
                this.daftar_users = data.users;
                this.role_id=data.role.id;
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
        showDialogTambahUserDosen:async function ()
        {
            this.dialog = true;            
        },
        editItem:async function (item) {
            this.editedIndex = this.daftar_users.indexOf(item)
            item.password='';            
            this.editedItem = Object.assign({}, item);                              
            this.dialogAlihkan = true;
        },        
        close () {            
            this.btnLoading=false;
            this.dialog = false;
            this.dialogAlihkan = false;            
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
                this.$refs.frmdata.resetValidation(); 
                }, 300
            );
        },    
        alihkan () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                this.$ajax.post('/akademik/dosenwali/'+this.editedItem.id,
                    {
                        '_method':'PUT',
                        user_id:this.editedItem.id,                            
                        pid:'alihkan_mhs'
                    },
                    {
                        headers:{
                            Authorization:this.TOKEN
                        }
                    }
                ).then(()=>{   
                    this.initialize();
                    this.close();
                }).catch(()=>{
                    this.btnLoading=false;
                });           
            }
        },        
        deleteItem (item) {           
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus dosen wali '+item.username+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoading=true;
                    this.$ajax.post('/akademik/dosenwali/'+item.id,
                        {
                            '_method':'DELETE',
                        },
                        {
                            headers:{
                                Authorization:this.TOKEN
                            }
                        }
                    ).then(()=>{   
                        const index = this.daftar_users.indexOf(item);
                        this.daftar_users.splice(index, 1);
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }
            });
        },
    },
    computed: {        
        ...mapGetters('auth',{            
            ACCESS_TOKEN:'AccessToken',          
            TOKEN:'Token',                                  
        }),
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
        dialogAlihkan (val) {
            val || this.close()
        },        
    },    
    components:{
        AkademikLayout,
        ModuleHeader,        
    },
}
</script>