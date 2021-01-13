<template>
    <SystemConfigLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-variable
            </template>
            <template v-slot:name>
                VARIABLES
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
                    Mengatur berbagai macam variable default sistem. Perubahan berlaku pada Login selanjutnya.
                    </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid>  
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card>
                            <v-card-title>
                                PERKULIAHAN DAN PMB
                            </v-card-title>
                            <v-card-text>
                                <v-row>
                                    <v-col xs="12" sm="12" md="4">
                                        <v-select
                                            v-model="formdata.default_ta" 
                                            :items="daftar_ta"                
                                            label="TAHUN AKADEMIK"
                                            outlined
                                            :rules="rule_default_ta"/>
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                                    <v-col xs="12" sm="12" md="4">
                                        <v-select
                                            v-model="formdata.default_semester" 
                                            :items="daftar_semester"
                                            item-text="text"
                                            item-value="id"
                                            label="SEMESTER AKADEMIK"
                                            outlined
                                            :rules="rule_default_semester"/>            
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                                    <v-col xs="12" sm="12" md="4">
                                        <v-select
                                            v-model="formdata.tahun_pendaftaran" 
                                            :items="daftar_ta"                                            
                                            label="TAHUN PENDAFTARAN"
                                            outlined
                                            :rules="rule_tahun_pendaftaran"/>            
                                    </v-col>
                                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
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
    name: 'Variables',
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
                text:'PERGURUAN TINGGI',
                disabled:false,
                href:'#'
            },
            {
                text:'VARIABLES',
                disabled:true,
                href:'#'
            }
        ];
        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.daftar_semester=this.$store.getters['uiadmin/getDaftarSemester'];  
        this.initialize();
    },
    data: () => ({
        breadcrumbs:[],        
        btnLoading:false,   
        //form
        form_valid:true,   
        daftar_ta:[],
        daftar_semester:[],
        formdata: {
            default_ta:'',
            default_semester:'',            
            tahun_pendaftaran:0,
        },
        //form rules        
        rule_default_ta:[
            value => !!value||"Mohon untuk dipilih Tahun Akademik !!!",             
        ], 
        rule_default_semester:[
            value => !!value||"Mohon untuk diisi Semester !!!",             
        ],
        rule_tahun_pendaftaran:[
            value => !!value||"Mohon untuk dipilih Tahun Pendaftaran !!!",                                 
        ]
    }),
    methods: {
        initialize:async function () 
        {
            await this.$ajax.get('/system/setting/variables',
            {
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data})=>{  
                let setting = data.setting;                           
                this.formdata.default_ta=setting.DEFAULT_TA;
                this.formdata.default_semester=setting.DEFAULT_SEMESTER;                
                this.formdata.tahun_pendaftaran=setting.DEFAULT_TAHUN_PENDAFTARAN;
            });          
            
        },
        save () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;                
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method':'PUT', 
                        'pid':'Variable default sistem',
                        setting:JSON.stringify({
                            201:this.formdata.default_ta,
                            202:this.formdata.default_semester,                            
                            203:this.formdata.tahun_pendaftaran,
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