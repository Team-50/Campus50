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
                    color="cyan"
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
    name: 'HeaderLaporan',
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
        breadcrumbs:[],
        datatableLoading:false,
        btnLoading:false,   
        //form
        form_valid:true,   
        formdata: {
            dmaster:null,            
        },        
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
                console.log(setting);
            });          
            
        },
        save () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;                
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method':'PUT', 
                        'pid':'Header Laporan',
                        setting:JSON.stringify({
                            701:this.formdata.header_1,
                            702:this.formdata.header_2,
                            703:this.formdata.header_3,
                            704:this.formdata.header_4,
                            705:this.formdata.header_address,
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