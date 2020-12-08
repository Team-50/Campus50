<template>
    <KeuanganLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-video-input-component
            </template>
            <template v-slot:name>
                STATUS TRANSAKSI
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
                        Halaman ini berisi informasi status transaksi.
                    </v-alert>
            </template>
        </ModuleHeader>        
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="datatable"                        
                        item-key="id_status"
                        sort-by="id_status"
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
                                <v-toolbar-title>DAFTAR STATUS TRANSAKSI</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>                                
                            </v-toolbar>
                        </template>  
                        <template v-slot:item.style="props">
                            <v-edit-dialog
                                :return-value.sync="props.item.style"
                                large                                
                                @save="saveItem({id:props.item.id_status,style:props.item.style})"
                                @cancel="cancelItem"
                                @open="openItem"
                                @close="closeItem">                                     
                                    <v-chip :color="props.item.style" dark>{{props.item.style}}</v-chip>                                  
                                    <template v-slot:input>
                                        <div class="mt-4 title">Update Style</div>                                        
                                        <v-text-field 
                                            label="STYLE STATUS TRANSAKSI"                                             
                                            outlined
                                            autofocus
                                            v-model="props.item.style">                                        
                                        </v-text-field>
                                    </template>
                            </v-edit-dialog>
                        </template>                      
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">                          
                                    <strong>ID:</strong>{{ item.id_status }}          
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
    </KeuanganLayout>
</template>
<script>
import KeuanganLayout from '@/views/layouts/KeuanganLayout';
import ModuleHeader from '@/components/ModuleHeader';

export default {
    name:'StatusTransaksi',
    created()
    {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'KEUANGAN',
                disabled:false,
                href:'/keuangan'
            },
            {
                text:'STATUS TRANSAKSI',
                disabled:true,
                href:'#'
            }
        ];        
        this.initialize();
    },  
    data: () => ({
        firstloading:true,
        breadcrumbs:[],         
        
        btnLoading:false,
        datatableLoading:false,
        expanded:[],
        datatable:[],
        headers: [            
            { text: 'ID', value: 'id_status',width:10,sortable:false },                                           
            { text: 'NAMA STATUS', value: 'nama_status',sortable:false},
            { text: 'STYLE', value: 'style',width:200,sortable:false },                        
        ],      
        
    }),
    methods : {        
        initialize:async function()
		{
            this.datatableLoading=true;            
            await this.$ajax.get('/keuangan/statustransaksi',
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{               
                this.datatable = data.status;                
                this.datatableLoading=false;
            });                     
            this.firstloading=false;                        
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
        saveItem:async function ({id,style})
        {
            await this.$ajax.post('/keuangan/statustransaksi/'+id,            
            {
                _method:'put',                
                id_status:id,
                style:style
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(()=>{               
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
    components:{
        KeuanganLayout,
        ModuleHeader,            
    },
}
</script>