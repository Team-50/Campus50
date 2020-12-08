<template>
    <v-card>
        <v-card-title>
            <span class="headline">ROLE PERMISSIONS</span>
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
                                            <v-card-subtitle>
                                                {{role.id}}
                                            </v-card-subtitle>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>TANGGAL BUAT :</v-card-title>
                                            <v-card-subtitle>
                                                {{$date(role.created_at).format('DD/MM/YYYY HH:mm')}}
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
                                                {{role.name}}
                                            </v-card-subtitle>
                                        </v-card>
                                   </v-col>
                                   <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                   <v-col xs="12" sm="6" md="6">
                                       <v-card flat>
                                            <v-card-title>TANGGAL UBAH :</v-card-title>
                                            <v-card-subtitle>
                                                {{$date(role.updated_at).format('DD/MM/YYYY HH:mm')}}
                                            </v-card-subtitle>
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
                                <v-tooltip color="info" bottom>
                                    <template v-slot:activator="{on,attrs}">
                                        <v-btn 
                                            icon                                        
                                            :loading="btnLoading"
                                            :disabled="btnLoading"
                                            v-bind="attrs"
                                            v-on="on"
                                            @click.stop="revoke(item)">
                                            <v-icon small>
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>                                                                          
                                    </template>
                                    <span>Hapus Permission dari Role ini</span>  
                                </v-tooltip>
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
    name: 'RolePermissions',
    data: () => ({
        btnLoading:false,
        //tables
        headers: [                        
            { text: 'NAMA PERMISSION', value: 'name' },
            { text: 'GUARD', value: 'guard_name' },      
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },          
        ],
        search:'',        
        perm_selected:[]
    }),
    methods: {
        save()
        {
            this.btnLoading=true;
            this.$ajax.post('/system/setting/roles/storerolepermissions',
                {
                    role_id:this.role.id,
                    chkpermission:this.permissions_selected
                },
                {
                    headers:{
                        Authorization:this.TOKEN
                    }
                }
            ).then(()=>{   
                this.btnLoading=false;
                this.close();                
            }).catch(()=>{
                this.btnLoading=false;
            });
        },
        revoke(item)
        {   
            this.btnLoading=true;         
            this.$ajax.post('/system/setting/roles/revokerolepermissions',
                {
                    role_id:this.role.id,
                    name:item.name
                },
                {
                    headers:{
                        Authorization:this.TOKEN
                    }
                }
            ).then(()=>{   
                this.btnLoading=false;
                this.close();                
            }).catch(()=>{
                this.btnLoading=false;
            });
        },
        close()
        {
            this.btnLoading=false;
            this.permissions_selected=[];
            this.$emit('closeRolePermissions',this.role.id);
        }
    },
    props:{
        role:Object,
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