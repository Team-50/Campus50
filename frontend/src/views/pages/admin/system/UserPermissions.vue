<template>
    <v-card>
        <v-card-title>
            <span class="headline">USER PERMISSIONS</span>
        </v-card-title>
        <v-card-text>
            <v-container fluid>
                <v-row class="mb-4" no-gutters>
                    <v-col xs="12" sm="12" md="12">
                        <v-card>
                           <v-card-text>
                                <v-row no-gutters>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>ID :</v-card-title>
                                            <v-card-text>
                                                {{user.id}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>NOMOR HP :</v-card-title>
                                            <v-card-text>
                                                {{user.nomor_hp}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                </v-row>
                                <v-row no-gutters>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>USERNAME :</v-card-title>
                                            <v-card-text>
                                                {{user.username}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                                   
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>THEME :</v-card-title>
                                            <v-card-text>
                                                {{user.theme}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>                                    
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                </v-row>
                                <v-row no-gutters>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>NAMA :</v-card-title>
                                            <v-card-text>
                                                {{user.name}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>CREATED :</v-card-title>
                                            <v-card-text>                                                
                                                {{$date(user.created_at).format('DD/MM/YYYY HH:mm')}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                </v-row>
                                <v-row no-gutters>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>EMAIL :</v-card-title>
                                            <v-card-text>
                                                {{user.email}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>UPDATED :</v-card-title>
                                            <v-card-text>                                                
                                                {{$date(user.updated_at).format('DD/MM/YYYY HH:mm')}}
                                            </v-card-text>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                </v-row>
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
                                ></v-text-field>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row no-gutters> 
                    <v-col col="12">
                        <v-data-table
                            v-model="permissions_selected"
                            :headers="headers"
                            :items="daftar_permissions"
                            :search="search"
                            item-key="name"
                            sort-by="name"
                            show-select
                            class="elevation-1"
                        >
                        <template v-slot:item.actions="{ item }">                            
                            <v-icon
                                small
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="revoke(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-text>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click.stop="close">KELUAR</v-btn>
            <v-btn 
                color="blue darken-1" 
                text 
                :loading="btnLoading"
                :disabled="btnLoading||!perm_selected.length > 0"
                @click.stop="save">
                    SIMPAN
            </v-btn>
        </v-card-actions>
    </v-card>
    
</template>
<script>
import {mapGetters} from 'vuex';
export default {
    name: 'UserPermissions',
    data: () => ({
        btnLoading:false,
        //tables
        headers: [                        
            { text: 'NAMA PERMISSION', value: 'name' },
            { text: 'GUARD', value: 'guard_name' },   
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },         
        ],
        search:'',        
        perm_selected:[],
        perm_revoked:[]
    }),
    methods: {        
        save()
        {
            this.btnLoading=true;
            this.$ajax.post('/system/users/storeuserpermissions',
                {
                    user_id:this.user.id,
                    chkpermission:this.permissions_selected
                },
                {
                    headers:{
                        Authorization:this.TOKEN
                    }
                }
            ).then(()=>{   
                this.close();                
            }).catch(()=>{
                this.btnLoading=false;
            });
        },
        revoke(item)
        {   
            this.btnLoading=true;         
            this.$ajax.post('/system/users/revokeuserpermissions',
                {
                    user_id:this.user.id,
                    name:item.name
                },
                {
                    headers:{
                        Authorization:this.TOKEN
                    }
                }
            ).then(()=>{   
                this.close();                
            }).catch(()=>{
                this.btnLoading=false;
            });
        },
        close()
        {            
            this.btnLoading=false;
            this.permissions_selected=[];     
            this.$emit('closeUserPermissions');
        }
    },
    props:{
        user:Object,
        daftarpermissions:Array,
        permissionsselected:Array,
    },
    computed: {
        ...mapGetters('auth',{                        
            TOKEN:'Token',                                  
        }),
        daftar_permissions()
        {
            return this.daftarpermissions;
        },
        permissions_selected: {
            get ()
            {
                if (this.perm_selected.length >0)
                {
                    return this.perm_selected;
                }
                else
                {
                    return this.permissionsselected;
                }
            },
            set (val)
            {                
                this.perm_selected=val;                
            }
        }

    }
}
</script>