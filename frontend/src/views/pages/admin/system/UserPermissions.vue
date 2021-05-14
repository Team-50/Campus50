<template>
    <v-card color="grey lighten-4">
        <v-toolbar elevation="2"> 
            <v-toolbar-title>PROFIL DAN PERMISSION USER</v-toolbar-title>
            <v-divider
                class="mx-4"
                inset
                vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-icon                
                @click.stop="exit">
                mdi-close-thick
            </v-icon>
        </v-toolbar>
        <v-card-text>
            <v-container fluid>
                <v-row>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>USER ID:</v-card-title>  
                            <v-card-subtitle>
                                {{user.id}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>NOMOR HP:</v-card-title>  
                            <v-card-subtitle>
                                {{user.nomor_hp}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                </v-row>
                <v-row>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>USERNAME:</v-card-title>  
                            <v-card-subtitle>
                                {{user.username}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>THEME:</v-card-title>  
                            <v-card-subtitle>
                                {{user.theme}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                </v-row>
                <v-row>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>NAMA:</v-card-title>  
                            <v-card-subtitle>
                                {{user.name}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>ROLE:</v-card-title>  
                            <v-card-subtitle>
                                {{role_user}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                </v-row>
                <v-row>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>EMAIL:</v-card-title>  
                            <v-card-subtitle>
                                {{user.email}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    <v-col xs="12" sm="12" md="6">
                        <v-card flat class="mb-2">
                            <v-card-title>CREATED/UPDATED:</v-card-title>  
                            <v-card-subtitle>
                                {{$date(user.created_at).format('DD/MM/YYYY HH:mm')}} ~ {{$date(user.updated_at).format('DD/MM/YYYY HH:mm')}}
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                </v-row>
                <v-row class="mb-4" no-gutters>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>
                                FILTER ROLE DAN PENCARIAN PERMISSION
                            </v-card-title>
                            <v-card-text>
                                <v-select
                                    label="ROLES"
                                    :items="daftar_role"
                                    v-model="role_name"
                                >         
                                </v-select>
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
                            :loading="datatableLoading"
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
            <v-btn color="blue darken-1" text @click.stop="exit">KELUAR</v-btn>
            <v-btn 
                color="blue darken-1" 
                text 
                :loading="btnLoading"
                :disabled="btnLoading||!permissions_selected.length > 0"
                @click.stop="save">
                    SIMPAN
            </v-btn>
        </v-card-actions>
    </v-card>
</template>
<script>
export default {
    name: 'UserPermissions',
    mounted()
    {
        this.role_name=this.role_default;
        this.initialize();
    },
    data: () => ({
        btnLoading: false,
        datatableLoading:false,
        //tables
        headers: [                        
            { text: 'NAMA PERMISSION', value: 'name' },
            { text: 'GUARD', value: 'guard_name' }, 
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },       
        ],
        search: "",

        role_name:null,
        daftar_role: [],

        daftar_permissions: [],
        permissions_selected: [],

    }),
    props: {                        
        user: {
            type:Object,
            required:true
        },
        role_default: {
            required:true
        }
    },
    methods: {
        initialize()
        {
            this.$ajax.get('/system/users/'+this.user.id+'/roles',              
                {
                    headers: {
                        Authorization: this.$store.getters['auth/Token']
                    }
                }
            ).then(({ data }) => {   
                this.daftar_role=data.roles;
            });
        }, 
        save()
        {
            this.btnLoading = true;
            this.$ajax.post('/system/users/storeuserpermissions',
                {
                    user_id: this.user.id,
                    chkpermission: this.permissions_selected
                },
                {
                    headers: {
                        Authorization: this.$store.getters['auth/Token']
                    }
                }
            ).then(() => {   
                this.exit();    
            }).catch(() => {
                this.btnLoading = false;
            });
        },
        revoke(item)
        {   
            this.btnLoading = true;         
            this.$ajax.post('/system/users/revokeuserpermissions',
                {
                    user_id: this.user.id,
                    name:item.name
                },
                {
                    headers: {
                        Authorization: this.$store.getters['auth/Token']
                    }
                }
            ).then(() => {   
                this.exit();    
            }).catch(() => {
                this.btnLoading = false;
            });
        },
        exit()
        {
            this.$emit('closeUserPermissions');           
        }
    },
    computed: {
        role_user()
        {
            return this.daftar_role.join(',').toUpperCase();
        }
    },
    watch: {
        async role_name(val)
        {
            if(val)
            {
                this.datatableLoading=true;
                await this.$ajax.get('/system/setting/rolesbyname/'+this.role_name+'/permission',{
                    headers: {
                        Authorization: this.$store.getters['auth/Token']
                    }
                }).then(({ data }) => {
                    this.daftar_permissions = data.permissions;
                });
                await this.$ajax.get('/system/users/'+this.user.id+'/permission',{
                    headers: {
                        Authorization: this.$store.getters['auth/Token']
                    }
                }).then(({ data }) => {
                    this.permissions_selected = data.permissions;        
                });
                this.datatableLoading=false;
            }
        }
    }
}
</script>