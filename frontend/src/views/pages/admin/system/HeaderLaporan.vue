<template>
    <SystemConfigLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-page-layout-header
            </template>
            <template v-slot:name>
                HEADER LAPORAN
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
                    Mengatur halaman header untuk laporan
                    </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid>  
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card>
                            
                            <v-card-text>
                                <v-text-field 
                                    v-model="formdata.header_1" 
                                    label="HEADER 1"
                                    outlined>
                                </v-text-field>                                                                                                                                                                                               
                                <v-text-field 
                                    v-model="formdata.header_2" 
                                    label="HEADER 2"
                                    outlined>
                                </v-text-field>
                                <v-text-field 
                                    v-model="formdata.header_3" 
                                    label="HEADER 3"
                                    outlined>
                                </v-text-field>
                                <v-text-field 
                                    v-model="formdata.header_4" 
                                    label="HEADER 4"
                                    outlined>
                                </v-text-field>
                                <v-text-field 
                                    v-model="formdata.header_address" 
                                    label="HEADER ALAMAT INSTITUSI"
                                    outlined>
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
                text:'PERGURUAN TINGGI',
                disabled:false,
                href:'#'
            },
            {
                text:'HEADER LAPORAN',
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
        formdata: {
            header_1:null,
            header_2:null,
            header_3:null,
            header_4:null,
            header_address:null,
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
                let setting = data.setting;               
                this.formdata.header_1=setting.HEADER_1;
                this.formdata.header_2=setting.HEADER_2;
                this.formdata.header_3=setting.HEADER_3;
                this.formdata.header_4=setting.HEADER_4;
                this.formdata.header_address=setting.HEADER_ADDRESS;
            });          
            
        },
        save() {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading = true;    
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method':'PUT', 
                        'pid':'Header Laporan',
                        setting:JSON.stringify({
                            701: this.formdata.header_1,
                            702: this.formdata.header_2,
                            703: this.formdata.header_3,
                            704: this.formdata.header_4,
                            705: this.formdata.header_address,
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
            }
        }
    },
    computed: { 
        ...mapGetters('auth',{            
            ACCESS_TOKEN:'AccessToken',        
            TOKEN:'Token',              
        }),
    },
    components: {
		SystemConfigLayout,
        ModuleHeader,      
	}
}
</script>