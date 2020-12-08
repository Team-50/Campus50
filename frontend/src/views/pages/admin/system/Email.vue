<template>
    <SystemConfigLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-email
            </template>
            <template v-slot:name>
                EMAIL
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
                    Setting Email
                    </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid>  
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card>
                            <v-card-title>
                                Pengiriman Email
                            </v-card-title>
                            <v-card-text>
                               <v-row>
                                   <v-col xs="12" sm="4" md="3">                                       
                                       <v-switch v-model="formdata.email_mhs_isvalid" label="Check Email Mahasiswa Valid"></v-switch>
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
    name: 'Email',
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
                text:'SERVER - EMAIL',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize();
    },
    data: () => ({
        breadcrumbs:[],        
        btnLoading:false,   
        //form
        form_valid:true,   
        formdata: {
            email_mhs_isvalid:null,            
        },
        //form rules        
        
    }),
    methods: {
        initialize:async function () 
        {
            this.datatableLoading=true;
            await this.$ajax.get('/system/setting/variables',
            {
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data})=>{                  
                let setting = data.setting;             
                this.formdata.email_mhs_isvalid=parseInt(setting.EMAIL_MHS_ISVALID);                
            });          
            
        },
        save () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method':'PUT', 
                        'pid':'email',
                        setting:JSON.stringify({
                            910:this.formdata.email_mhs_isvalid,                            
                        }),                                                                                                                            
                    },
                    {
                        headers:{
                            Authorization:this.TOKEN
                        }
                    }
                ).then(()=>{                       
                    this.btnLoading=false;
                }).catch(()=>{
                    this.btnLoading=false;
                });        
            }
        }
    },
    computed:{ 
        ...mapGetters('auth',{            
            ACCESS_TOKEN:'AccessToken',          
            TOKEN:'Token',                                  
        }),
    },
    components:{
		SystemConfigLayout,
        ModuleHeader,        
	}
}
</script>