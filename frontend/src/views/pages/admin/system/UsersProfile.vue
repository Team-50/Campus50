<template>
    <SystemUserLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-account
            </template>
            <template v-slot:name>
                USER PROFILE
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
                        berisi informasi profile user.
                </v-alert>
            </template>
        </ModuleHeader>   
        <v-container fluid>    
            <v-row class="mb-4">
                <v-col cols="12">
                    <v-card color="grey lighten-4">
                        <v-toolbar elevation="2"> 
                            <v-toolbar-title>DATA USER</v-toolbar-title>                             
                        </v-toolbar>
                        <v-card-text>
                            <v-row>
                                <v-col xs="12" sm="6" md="3">
                                    <v-card flat>
                                        <v-card-text>
                                            <v-img :src="$api.storageURL+'/'+formdata.foto" />
                                        </v-card-text>
                                    </v-card>
                                    <v-card flat v-if="dashboard=='mahasiswa'">
                                        <v-card-title>NIM / NIRM</v-card-title>  
                                        <v-card-subtitle>
                                            {{data_mhs.nim}} / {{data_mhs.nirm}}
                                        </v-card-subtitle>
                                    </v-card>
                                    <v-card flat v-if="dashboard=='mahasiswa'">
                                        <v-card-title>PROGRAM STUDI</v-card-title>  
                                        <v-card-subtitle>
                                            {{data_mhs.nama_prodi}}
                                        </v-card-subtitle>
                                    </v-card>
                                    <v-card flat v-if="dashboard=='mahasiswa'">
                                        <v-card-title>KELAS</v-card-title>  
                                        <v-card-subtitle>
                                            {{data_mhs.nama_kelas}}
                                        </v-card-subtitle>
                                    </v-card>
                                    <v-card flat v-if="dashboard=='mahasiswa'">
                                        <v-card-title>DOSEN WALI</v-card-title>  
                                        <v-card-subtitle>
                                            {{data_mhs.dosen_wali}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="9">
                                    <v-row>
                                        <v-col xs="12" sm="6" md="6">
                                             <v-card flat>
                                                <v-card-title>ID:</v-card-title>  
                                                <v-card-subtitle>
                                                    {{formdata.id}}
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                        <v-col xs="12" sm="6" md="6">
                                             <v-card flat>
                                                <v-card-title>EMAIL:</v-card-title>  
                                                <v-card-subtitle>
                                                    {{formdata.email}}
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    </v-row>
                                    <v-row>
                                        <v-col xs="12" sm="6" md="6">
                                             <v-card flat>
                                                <v-card-title>USERNAME:</v-card-title>  
                                                <v-card-subtitle>
                                                    {{formdata.username}}
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                        <v-col xs="12" sm="6" md="6">
                                             <v-card flat>
                                                <v-card-title>ROLE:</v-card-title>  
                                                <v-card-subtitle>
                                                    {{formdata.default_role}}
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    </v-row>
                                    <v-row>
                                        <v-col xs="12" sm="6" md="6">
                                             <v-card flat>
                                                <v-card-title>NOMOR HP:</v-card-title>  
                                                <v-card-subtitle>
                                                    {{formdata.nomor_hp}}
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                        <v-col xs="12" sm="6" md="6">
                                             <v-card flat>
                                                <v-card-title>STATUS:</v-card-title>  
                                                <v-card-subtitle>
                                                    <v-chip 
                                                        :color="userstatus"
                                                        label
                                                        outlined>
                                                        {{formdata.active==1?'AKTIF':'TIDAK AKTIF'}}
                                                    </v-chip>
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    </v-row>
                                    <v-row>
                                        <v-col xs="12" sm="6" md="6">
                                            <v-card flat>
                                                <v-card-title>CREATED_AT:</v-card-title>  
                                                <v-card-subtitle>
                                                    {{$date(formdata.created_at).format('DD/MM/YYYY HH:mm')}}
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                        <v-col xs="12" sm="6" md="6">
                                            <v-card flat>
                                                <v-card-title>UPDATED_AT:</v-card-title>  
                                                <v-card-subtitle>
                                                    {{$date(formdata.updated_at).format('DD/MM/YYYY HH:mm')}}
                                                </v-card-subtitle>
                                            </v-card>
                                        </v-col>
                                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    </v-row>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                            </v-row>
                        </v-card-text>
                    </v-card>                    
                </v-col>
            </v-row>
            <v-row> 
                <v-col xs="12" sm="6" md="6" v-if="dashboard=='mahasiswa'">
                    
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card>
                            <v-card-title>
                                <span class="headline">GANTI PASSWORD</span>
                            </v-card-title>
                            <v-card-text>    
                                 <v-text-field 
                                    v-model="formdata.password" 
                                    label="PASSWORD BARU"
                                    :type="'password'"
                                    outlined
                                    :rules="rule_user_password">
                                </v-text-field> 
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>     
                                <v-btn 
                                    color="blue darken-1" 
                                    text 
                                    @click.stop="save" 
                                    :loading="btnLoading"
                                    :disabled="!form_valid||btnLoading">SIMPAN</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
            </v-row>
        </v-container>
    </SystemUserLayout>
</template>
<script>
import SystemUserLayout from '@/views/layouts/SystemUserLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name:'UsersProfile',
    created() {
        this.dashboard=this.$store.getters['uiadmin/getDefaultDashboard'];
        this.formdata=this.$store.getters['auth/User'];
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'SYSTEM',
                disabled:false,
                href:'#'
            },
            {
                text:'PROFILE USER',
                disabled:true,
                href:'#'
            }
        ];
        if (this.dashboard=='mahasiswa')
        {
            this.fetchMahasiswa();
        }
    }, 
    data ()
    {
        return {
            dashboard:null,

            btnLoading: false,
            datatable: [],
            avatar : null,

            //form data        
            data_mhs: {
                nim:'N.A',
                nirm:'N.A',
                nama_prodi:'N.A',
                nama_kelas:'N.A',
                dosen_wali:'N.A',
            },     
            form_valid:true,       
            formdata: {
                id:0,    
                username: "",       
                password: '',              
                name: "",    
                email: "",    
                nomor_hp: "",    
                theme: "",                    
                foto: "",
                active: "",                                                                           
                default_role: "",                    
                locked: "",                    
                created_at: '',         
                updated_at: '',         
            },
            formdefault: {
                id:0,    
                username: "",       
                password: '',              
                name: "",    
                email: "",    
                nomor_hp: "",    
                theme: "",                    
                foto: "",
                active: "",                                                                           
                default_role: "",                    
                locked: "",                    
                created_at: '',         
                updated_at: '',         
            },
            //form rules  
            rule_foto: [
                value => !!value || "Mohon pilih gambar !!!",
                value =>  !value || value.size < 2000000 || 'File foto harus kurang dari 2MB.'                
            ], 
            rule_user_password: [
                value => !!value || "Mohon untuk di isi password User !!!",
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
        };
    },
    methods: {
        save()
        {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading = true;
                this.$ajax.post('/system/users/updatepassword/'+this.$store.getters['auth/AttributeUser']('id'),
                    {
                        '_method':'PUT',    
                        password: this.formdata.password,       
                    },
                    {
                        headers: {
                            Authorization: this.$store.getters['auth/Token']
                        }
                    }
                ).then(({ data }) => {                                                                            
                    this.$refs.frmdata.reset(); 
                    this.formdata.foto=data.foto;       
                    this.formdata=this.formdefault; 
                    this.btnLoading = false;
                }).catch(() => {
                    this.btnLoading = false;
                });         
            }
        },
        previewImage (e)
        {
            if (typeof e === 'undefined')
            {
                this.avatar=null;
            }
            else
            {
                let reader = new FileReader();
                reader.readAsDataURL(e);
                reader.onload = img => {                    
                    this.photoUser=img.target.result;
                }
            }            
            
        },
        uploadFoto:async function() 
        {
            if (this.$refs.frmuploadfoto.validate())
            {
                if (this.formdata.foto)
                {                
                    this.btnLoading = true;
                    var formdata = new FormData();
                    formdata.append('foto',this.formdata.foto);
                    await this.$ajax.post('/setting/users/uploadfoto/'+this.$store.getters.User.id,formdata,
                        {
                            headers: {
                                Authorization: this.$store.getters['auth/Token'],
                                'Content-Type': 'multipart/form-data'                      
                            }
                        }
                    ).then(({ data }) => {                           
                        this.btnLoading = false;
                        this.$store.dispatch('updateFoto',data.user.foto);            
                    }).catch(() => {
                        this.btnLoading = false;
                    });    
                    this.$refs.frmdata.reset(); 
                }   
            }
        },
        resetFoto:async function() 
        {
            this.btnLoading = true;
            await this.$ajax.post('/setting/users/resetfoto/'+this.$store.getters.User.id,{},
                {
                    headers: {
                        Authorization: this.$store.getters['auth/Token'],          
                    }
                }
            ).then(({ data }) => {                           
                this.btnLoading = false;
                this.$store.dispatch('updateFoto',data.user.foto);
            }).catch(() => {
                this.btnLoading = false;
            });    
        },
        async fetchMahasiswa()
        {
            await this.$ajax.get('/akademik/kemahasiswaan/biodatamhs1/'+this.$store.getters['auth/AttributeUser']('id'),
                {
                    headers: {
                        Authorization: this.$store.getters['auth/Token'],          
                    }
                }
            ).then(({ data }) => {                           
                this.data_mhs=data.mahasiswa;
            })
        }
        
    },
    computed: {        
		photoUser: {
            get()
            {
                if (this.avatar==null)
                {
                    let photo = this.$api.storageURL+'/'+this.$store.getters.User.foto;			
                    return photo;
                }
                else
                {
                   return this.avatar;
                }
                
            },
            set(val)
            {   
                this.avatar = val;
            }
        },
        userstatus()
        {
            return this.formdata.active == 1 ?'green':'red';
        }
    },
    components: {
        SystemUserLayout,
        ModuleHeader,
    },
}
</script>