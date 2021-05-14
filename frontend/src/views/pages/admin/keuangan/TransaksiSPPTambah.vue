<template>
    <KeuanganLayout :showrightsidebar="false">
        <ModuleHeader>
            <template v-slot:icon>
                mdi-video-input-component
            </template>
            <template v-slot:name>
                TRANSAKSI SPP
            </template>
            <template v-slot:subtitle  v-if="data_transaksi">
                TAHUN AKADEMIK / SEMESTER TRANSAKSI: {{data_transaksi.ta}} - {{$store.getters['uiadmin/getNamaSemester'](data_transaksi.idsmt)}}
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
                        Halaman ini digunakan untuk melakukan transaksi SPP mahasiswa baru dan lama.
                    </v-alert>
            </template>
        </ModuleHeader>
        <v-container fluid v-if="data_transaksi">           
            <v-row>   
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <span class="headline">DATA TRANSAKSI <v-chip :color="data_transaksi.style" dark>{{data_transaksi.nama_status}}</v-chip></span>
                            <v-spacer></v-spacer>
                            <v-icon                
                                @click.stop="closeTambahTransaksi(transaksi_id)">
                                mdi-close-thick
                            </v-icon>
                        </v-card-title>
                        <v-card-text>
                            <v-row no-gutters>
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>ID TRANSAKSI:</v-card-title>
                                        <v-card-subtitle>
                                            {{data_transaksi.id}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>KODE BILLING :</v-card-title>
                                        <v-card-subtitle>
                                            {{data_transaksi.no_transaksi}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>NIM / NO.FORMULIR:</v-card-title>
                                        <v-card-subtitle>
                                            {{data_transaksi.nim}} / {{data_transaksi.no_formulir}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>NAMA MAHASISWA:</v-card-title>
                                        <v-card-subtitle>
                                            {{data_transaksi.nama_mhs}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>PROGRAM STUDI:</v-card-title>
                                        <v-card-subtitle>
                                            {{this.$store.getters['uiadmin/getProdiName'](data_transaksi.kjur)}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>             
                                
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>KELAS:</v-card-title>
                                        <v-card-subtitle>
                                            {{data_transaksi.nkelas}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>TOTAL:</v-card-title>
                                        <v-card-subtitle>
                                            {{data_transaksi.total|formatUang}}
                                        </v-card-subtitle>
                                    </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                    <v-card flat>
                                        <v-card-title>CREATED / UPDATED :</v-card-title>
                                        <v-card-subtitle>
                                            {{$date(data_transaksi.created_at).format('DD/MM/YYYY HH:mm')}} / {{$date(data_transaksi.updated_at).format('DD/MM/YYYY HH:mm')}}
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
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>      
                        <v-data-table
                            :headers="headers"
                            :items="item_selected"                                                                       
                            :disable-pagination="true"
                            :hide-default-footer="true"                                                                
                            item-key="no_bulan"                                                   
                            class="elevation-1"
                            :loading="datatableLoading"
                            loading-text="Loading... Please wait">
                            <template v-slot:top>
                                <v-toolbar flat color="white">
                                    <v-toolbar-title>DAFTAR BULAN SPP YANG AKAN DIBAYAR</v-toolbar-title>
                                    <v-divider
                                        class="mx-4"
                                        inset
                                        vertical
                                    ></v-divider>
                                    <v-spacer></v-spacer>    
                                    <v-btn color="primary" class="mb-2" @click.stop="save" :disabled="!(item_selected.length >0) || (data_transaksi.status==1 || data_transaksi.status==2)" :loading="btnLoading">SIMPAN</v-btn>                                 
                                </v-toolbar>
                            </template>   
                            <template v-slot:item.biaya_kombi="{ item }">  
                                {{item.biaya_kombi|formatUang}}
                            </template>
                            <template v-slot:item.actions="{ item }">  
                                <v-icon
                                    small
                                    :loading="btnLoading"
                                    :disabled="btnLoading"
                                    @click.stop="deleteItem(item)">
                                    mdi-delete
                                </v-icon>
                            </template>
                            <template v-slot:body.append v-if="item_selected.length > 0">
                                <tr class="grey lighten-4 font-weight-black">
                                    <td>JUMLAH</td>
                                    <td>{{totalBulan}} Bulan</td> 
                                    <td></td>
                                    <td>{{totalBiayaKombi|formatUang}}</td>
                                    <td></td>     
                                </tr> 
                            </template>   
                            <template v-slot:no-data>
                                daftar bulan yang akan dibayar belum tersedia; silahkan pilih bulan di bawah ini.
                            </template>                     
                        </v-data-table>
                    </v-form>
                </v-col>
            </v-row>
            <v-row class="mb-4" no-gutters v-if="data_transaksi.status==0">
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="enrichedDataTable"   
                        v-model="item_selected"                                                  
                        :disable-pagination="true"
                        :hide-default-footer="true"                                                                
                        item-key="id"
                        show-select                                             
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR BULAN SPP</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>         
                            </v-toolbar>
                        </template>                        
                        <template v-slot:item="{ item }">    
                            <tr>
                                <td>
                                    <v-checkbox                                                       
                                        :disabled="item.status==1"
                                        class="pa-0 ma-0"
                                        :ripple="false"
                                        v-model="item_selected"
                                        :value="item"
                                        hide-details>
                                    </v-checkbox>
                                </td>
                                <td>
                                    {{item.no_bulan}}
                                </td>
                                <td>
                                    {{item.nama_bulan}}
                                </td>
                                <td>
                                    {{item.tahun}}
                                </td>
                                <td>
                                    {{item.biaya_kombi|formatUang}}
                                </td>
                                <td>N.A</td>
                            </tr>
                        </template>                        
                        <template v-slot:no-data>
                            Data transaksi SPP belum tersedia
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
    name:'TransaksiSPPTambah',
    created()
    {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];   
        this.transaksi_id=this.$route.params.transaksi_id;        
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
                text:'TRANSAKSI SPP',
                disabled:false,
                href:'/keuangan/transaksi-spp'
            },
            {
                text:'TAMBAH',
                disabled:true,
                href:'#'
            }
        ];              
        this.initialize();
        this.tahun_akademik = this.$store.getters['uiadmin/getTahunAkademik'];  
    },  
    data: () => ({
        transaksi_id:null,
        data_transaksi:null,
        item_selected: [],

        breadcrumbs: [],   
        tahun_akademik:0,
        btnLoading: false,            
        //tables
        datatableLoading:false,     
        datatable: [], 
        headers: [                                                
            { text: 'NO. BULAN', value: 'no_bulan',width:120,sortable:false },
            { text: 'BULAN', value: 'nama_bulan',sortable:false },          
            { text: 'TAHUN', value: 'tahun',sortable:false },          
            { text: 'BIAYA KOMBI', value: 'biaya_kombi',sortable:false }, 
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },         
        ],            
        //form
        form_valid:true  
    }),
    methods : {
        changeTahunAkademik (tahun)
        {
            this.tahun_akademik=tahun;
        },
        initialize:async function() 
        {
            this.datatableLoading=true;
            await this.$ajax.get('/keuangan/transaksi-spp/'+this.transaksi_id,    
            {
                headers: {
                    Authorization: this.$store.getters['auth/Token']
                }
            }).then(({ data }) => {       
                this.data_transaksi=data.transaksi;        
                this.datatable = data.transaksi_detail;    
                this.item_selected = data.item_selected;    
                this.datatableLoading=false;
            });         
        }, 
        save:async function() {
            if (this.$refs.frmdata.validate())
            {
                
                this.btnLoading = true;
                await this.$ajax.post('/keuangan/transaksi-spp/store',
                    {
                        id: this.transaksi_id,    
                        bulan_selected:JSON.stringify(Object.assign({},this.item_selected)),                                                
                    },
                    {
                        headers: {
                            Authorization: this.$store.getters['auth/Token']
                        }
                    }
                ).then(() => {                       
                    this.btnLoading = false;
                    this.$router.go();
                }).catch(() => {
                    this.btnLoading = false;
                });
            }
        },      
        deleteItem (item) {           
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus data dengan ID '+item.id+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoading = true;
                    var bulan=[1,2,3,4,5,6,7,8,9,10,11,12];
                    if (bulan.includes(item.id))
                    {
                        const index = this.item_selected.indexOf(item);
                        this.item_selected.splice(index, 1);
                        this.btnLoading = false;
                    }                    
                    else
                    {
                        this.$ajax.post('/keuangan/transaksi-spp/'+item.id,
                            {
                                '_method':'DELETE',
                            },
                            {
                                headers: {
                                    Authorization: this.$store.getters['auth/Token']
                                }
                            }
                        ).then(() => {   
                            this.btnLoading = false;
                            this.$router.go();                
                        }).catch(() => {
                            this.btnLoading = false;
                        });
                    }
                }                
            });
        },
        closeTambahTransaksi (transaksi_id)
        {
            this.$router.push('/keuangan/transaksi-spp/'+transaksi_id);
        },
    },   
    computed: {
        enrichedDataTable()
        {
            return this.datatable.map(x => ({ ...x, isSelectable: x.status ==0 }));
        },
        totalBulan()
        {
            return this.item_selected.length;
        },
        totalBiayaKombi()
        {
            var total = 0;
            var index;
            for (index in this.item_selected)
            {
                total = total + parseInt(this.item_selected[index].biaya_kombi);
            }            
            return total;
        }
    },
    components: {
        KeuanganLayout,
        ModuleHeader,           
    },
}
</script>
