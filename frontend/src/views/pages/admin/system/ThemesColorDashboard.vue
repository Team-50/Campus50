<template>
    <SystemConfigLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-page-layout-header
            </template>
            <template v-slot:name>
                WARNA DASHBOARD
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
                    Mengatur warna box modul di dashboard
                    </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid>  
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card> 
                            <v-card-text>
                                <v-row>
                                    <v-col xs="12" sm="6" md="6">
                                        <v-radio-group v-model="currentBox">
                                            <v-radio label="DATA MASTER" value="dmaster" />
                                            <v-radio label="PERENCANAAN" value="perencanaan" />
                                            <v-radio label="SPMB" value="spmb" />
                                            <v-radio label="KEUANGAN" value="keuangan" />
                                            <v-radio label="AKADEMIK" value="akademik" />
                                            <v-radio label="KEMAHASISWAAN" value="kemahasiswaan" />
                                            <v-radio label="ELEARNING" value="elearning" />
                                            <v-radio label="USER SISTEM" value="user_sistem" />
                                            <v-radio label="KONFIGURASI SISTEM" value="konfigurasi_sistem" />
                                            <v-radio label="MIGRASI SISTEM" value="migrasi_sistem" />
                                        </v-radio-group>
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                    <v-col xs="12" sm="6" md="6">
                                        <v-color-picker v-model="color" mode="hexa" :hide-mode-switch="true"></v-color-picker>
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                </v-row>     
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
            </v-row>
        </v-container>
    </SystemConfigLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import SystemConfigLayout from '@/views/layouts/SystemConfigLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name: 'ThemesColorDashboard',
    created()
    {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.ACCESS_TOKEN
            },
            {
                text:'KONFIGURASI SISTEM',
                disabled:false,
                href:'/system-setting'
            },
            {
                text:'THEMES',
                disabled:false,
                href:'#'
            },
            {
                text:'WARNA DASHBOARD',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize();
    },
    data: () => ({
        breadcrumbs: [],
        datatableLoading:false,
        btnLoading: false, 
        //form
        form_valid:true, 
        currentBox:'dmaster',
        color:'#fff',        
        formdata: {
            dmaster:null,          
            perencanaan:null,          
            spmb:null,          
            keuangan:null,          
            akademik:null,          
            kemahasiswaan:null,          
            elearning:null,          
            user_sistem:null,          
            konfigurasi_sistem:null,          
            migrasi_sistem:null,          
        },      
    }),
    methods: {
        initialize:async function() 
        {
            this.datatableLoading=true;
            await this.$ajax.get('/system/setting/variables',
            {
                headers: {
                    Authorization: this.TOKEN
                }
            }).then(({ data }) => {  
                let setting = JSON.parse(data.setting.COLOR_DASHBOARD);  
                this.showColor=setting.dmaster;                 
                this.formdata.dmaster=setting.dmaster;
                this.formdata.perencanaan=setting.perencanaan;
                this.formdata.spmb=setting.spmb;
                this.formdata.keuangan=setting.keuangan;
                this.formdata.akademik=setting.akademik;
                this.formdata.kemahasiswaan=setting.kemahasiswaan;
                this.formdata.elearning=setting.elearning;
                this.formdata.user_sistem=setting.user_sistem;
                this.formdata.konfigurasi_sistem=setting.konfigurasi_sistem;
                this.formdata.migrasi_sistem=setting.migrasi_sistem;
            
            });          
            
        },
        save() {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading = true;    
                switch (this.currentBox)
                {
                    case 'dmaster':
                        this.formdata.dmaster=this.showColor;
                    break;
                    case 'perencanaan':
                        this.formdata.perencanaan=this.showColor;
                    break;        
                    case 'spmb':
                        this.formdata.spmb=this.showColor;  
                    break;
                    case 'keuangan':
                        this.formdata.keuangan=this.showColor;     
                    break;
                    case 'kemahasiswaan':
                        this.formdata.kemahasiswaan=this.showColor;   
                    break;
                    case 'akademik':
                        this.formdata.akademik=this.showColor;   
                    break;
                    case 'elearning':
                        this.formdata.elearning=this.showColor;    
                    break;
                    case 'user_sistem':
                        this.formdata.user_sistem=this.showColor;   
                    break;
                    case 'konfigurasi_sistem':
                        this.formdata.konfigurasi_sistem=this.showColor; 
                    break;
                    case 'migrasi_sistem':
                        this.formdata.migrasi_sistem=this.showColor;      
                    break;    
                }
                            
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method':'PUT', 
                        'pid':'Color Dashboard',
                        setting:JSON.stringify({
                            807: this.formdata,        
                        }),                                                                                                        
                    },
                    {
                        headers: {
                            Authorization: this.TOKEN
                        }
                    }
                ).then(() => {                       
                    this.btnLoading = false;
                }).catch(() => {
                    this.btnLoading = false;
                });        
                this.$store.dispatch('uiadmin/init',this.$ajax); 
            }
        }
    },
    computed: { 
        ...mapGetters('auth',{            
            ACCESS_TOKEN:'AccessToken',        
            TOKEN:'Token',              
        }),
        showColor: {
            set(val)
            {
                this.color=val;    
            },
            get()
            {
                
                return this.color
            }
        }
    },
    watch: {
        currentBox(val)
        {
            switch (val)
            {
                case 'dmaster':
                    this.showColor=this.formdata.dmaster;
                break;
                case 'perencanaan':
                    this.showColor=this.formdata.perencanaan;     
                break;        
                case 'spmb':
                    this.showColor=this.formdata.spmb;     
                break;
                case 'keuangan':
                    this.showColor=this.formdata.keuangan;     
                break;
                case 'akademik':
                    this.showColor=this.formdata.akademik;     
                break;
                case 'kemahasiswaan':
                    this.showColor=this.formdata.kemahasiswaan;     
                break;
                case 'elearning':
                    this.showColor=this.formdata.elearning;     
                break;
                case 'user_sistem':
                    this.showColor=this.formdata.user_sistem;     
                break;
                case 'konfigurasi_sistem':
                    this.showColor=this.formdata.konfigurasi_sistem;     
                break;
                case 'migrasi_sistem':
                    this.showColor=this.formdata.migrasi_sistem;     
                break;    
            }
        }
    },
    components: {
		SystemConfigLayout,
        ModuleHeader,      
	}
}
</script>