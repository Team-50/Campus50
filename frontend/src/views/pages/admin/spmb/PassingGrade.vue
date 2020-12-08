<template>
    <SPMBLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-calendar-account
            </template>
            <template v-slot:name>
                PASSING GRADE NILAI UJIAN
            </template>
            <template v-slot:subtitle>
                {{jadwal_ujian.nama_kegiatan}} - TAHUN PENDAFTARAN {{jadwal_ujian.ta}} SEMESTER {{$store.getters['uiadmin/getNamaSemester'](jadwal_ujian.idsmt)}}
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
                    Berisi informasi passing grade nilai dari sebuah jadwal ujian
                </v-alert>
            </template>
        </ModuleHeader>
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="datatable"                        
                        item-key="id"
                        sort-by="id"
                        show-expand
                        :disable-pagination="true"
                        :hide-default-footer="true"
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">     
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR NILAI PASSING GRADE</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-btn 
                                    color="primary" 
                                    class="mb-2" 
                                    :loading="btnLoading"
                                    :disabled="btnLoading"
                                    @click.stop="loadprodi">
                                        GENERATE
                                </v-btn>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.kjur="{item}">
                            {{$store.getters['uiadmin/getProdiName'](item.kjur)}}
                        </template>
                        <template v-slot:item.nilai="props">
                            <v-edit-dialog
                                :return-value.sync="props.item.nilai"
                                large                                
                                @save="saveItem({id:props.item.id,nilai:props.item.nilai})"
                                @cancel="cancelItem"
                                @open="openItem"
                                @close="closeItem"> 
                                    {{ props.item.nilai }}                                    
                                    <template v-slot:input>
                                        <div class="mt-4 title">Update Nilai</div>                                        
                                        <v-text-field 
                                            label="NILAI PASSING GRADE" 
                                            :rules="rule_angka"
                                            outlined
                                            autofocus
                                            v-model="props.item.nilai">                                        
                                        </v-text-field>
                                    </template>
                            </v-edit-dialog>
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">                          
                                    <strong>ID:</strong>{{ item.id }}          
                                    <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                                    <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                                </v-col>                                
                            </td>
                        </template>
                        <template v-slot:no-data>
                            Data belum tersedia
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-container>
    </SPMBLayout>
</template>
<script>
import SPMBLayout from '@/views/layouts/SPMBLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name:'PassingGrade',
    created () {
        this.jadwal_ujian_id = this.$route.params.idjadwalujian;     
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'SPMB',
                disabled:false,
                href:'#'
            },
            {
                text:'JADWAL UJIAN PMB',
                disabled:false,
                href:'/spmb/jadwalujianpmb'
            },
            {
                text:'PASSING GRADE',
                disabled:true,
                href:'#'
            }
        ]; 
        this.initialize();    
    },
    data:()=>({
        jadwal_ujian_id:null,
        jadwal_ujian:{
            id:0,                        
            nama_kegiatan:'',            
            ta:'',                        
            idsmt:'',                                    
        },
        breadcrumbs:[],        
        dashboard:null,

        btnLoading:false,
        datatableLoading:false,        
        expanded:[],
        datatable:[],
        headers: [                                        
            { text: 'PROGRAM STUDI', value: 'kjur', sortable: true},
            { text: 'NILAI', value: 'nilai', sortable: false,width:100 },                        
        ],
        search:'',

        //form rules
        rule_angka:[
            value => /^(100(\.0{1,2})?|[1-9]?\d(\.\d{1,2})?)$/.test(value) || 'Isi dengan nilai antara 0.00 s.d 100.00', 
        ],
    }),
    methods: {
        initialize:async function () 
        {
            this.datatableLoading=true;
            await this.$ajax.post('/spmb/passinggrade',
            {
                jadwal_ujian_id:this.jadwal_ujian_id,                
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                 
                this.datatableLoading=false;
                this.jadwal_ujian=data.jadwal_ujian;      
                this.datatable=data.passing_grade;                               
            }).catch(()=>{
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
        loadprodi:async function ()
        {
            this.btnLoading=true;
            await this.$ajax.post('/spmb/passinggrade/loadprodi',
                {
                    jadwal_ujian_id:this.jadwal_ujian_id,               
                },
                {
                    headers:{
                        Authorization:this.$store.getters['auth/Token']
                    }
                }
            ).then(()=>{         
                this.btnLoading=false;
                this.initialize();
            }).catch(()=>{
                this.btnLoading=false;
            });        
        },
        saveItem:async function ({id,nilai})
        {
            this.btnLoading=true;
            await this.$ajax.post('/spmb/passinggrade/'+id,            
            {
                _method:'put',
                id:id,
                nilai:nilai
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(()=>{        
                this.btnLoading=false;       
                this.initialize();                        
            });  
        },
        cancelItem()
        {

        },
        openItem()
        {

        },
        closeItem()
        {

        },
    },
    computed: {        
        
    },
    components:{
        SPMBLayout,
        ModuleHeader,        
    },
}
</script>