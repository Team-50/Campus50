<template>
    <KeuanganLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-video-input-component
            </template>
            <template v-slot:name>
                TRANSAKSI SPP
            </template>
            <template v-slot:subtitle>
                TAHUN AKADEMIK {{tahun_akademik}}
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
                        Halaman ini digunakan untuk melakukan transaksi SPP mahasiswa baru dan lama.
                    </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter1 v-on:changeTahunAkademik="changeTahunAkademik" ref="filter1" />
        </template>
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
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
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="datatable"
                        :search="search"
                        item-key="id"
                        sort-by="nama_mhs"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR TRANSAKSI</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>         
                                <v-btn color="indigo darken-3" elevation="0" small class="primary" @click.stop="addItem">
                                    <v-icon size="21px">mdi-plus-circle</v-icon>
                                </v-btn>             
                                <v-dialog v-model="dialogfrm" max-width="500px" persistent>         
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">TAMBAH TRANSAKSI T.A {{tahun_akademik}}</span>
                                            </v-card-title>
                                            <v-card-text>
                                                <v-text-field 
                                                    v-model="formdata.nim" 
                                                    label="NIM"
                                                    outlined
                                                    :rules="rule_nim"
                                                    :disabled="dashboard =='mahasiswa'">
                                                </v-text-field>                  
                                                <v-select
                                                    v-model="formdata.semester_akademik"
                                                    :items="daftar_semester"                                    
                                                    label="TRANSAKSI DILAKUKAN PADA SEMESTER"                                                                                                
                                                    :rules="rule_semester"
                                                    item-text="text"
                                                    item-value="id"
                                                    outlined/>                                                      
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
                                                <v-btn 
                                                    color="blue darken-1" 
                                                    text 
                                                    @click.stop="buatTransaksi" 
                                                    :loading="btnLoading"
                                                    :disabled="!form_valid||btnLoading">
                                                        BUAT
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.tanggal="{ item }">    
                            {{$date(item.tanggal).format('DD/MM/YYYY')}}
                        </template>
                        <template v-slot:item.sub_total="{ item }">    
                            {{item.sub_total|formatUang}}
                        </template>
                        <template v-slot:item.idsmt="{ item }">     
                            {{$store.getters['uiadmin/getNamaSemester'](item.idsmt)}}
                        </template>
                        <template v-slot:item.nama_status="{ item }">    
                            <v-chip :color="item.style" dark>{{item.nama_status}}</v-chip>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="viewItem(item)">
                                mdi-eye
                            </v-icon>                             
                        </template>
                        <template v-slot:body.append v-if="datatable.length > 0">
                            <tr class="grey lighten-4 font-weight-black">
                                <td class="text-right" colspan="7">TOTAL TRANSAKSI PAID</td>
                                <td class="text-right" >{{totaltransaksi_paid|formatUang}}</td> 
                                <td></td>
                                <td></td>     
                            </tr> 
                            <tr class="grey lighten-4 font-weight-black">
                                <td class="text-right" colspan="7">TOTAL TRANSAKSI UNPAID</td>
                                <td class="text-right" >{{totaltransaksi_unpaid|formatUang}}</td> 
                                <td></td>
                                <td></td>     
                            </tr> 
                            <tr class="grey lighten-4 font-weight-black">
                                <td class="text-right" colspan="7">TOTAL TRANSAKSI CANCELED</td>
                                <td class="text-right" >{{totaltransaksi_canceled|formatUang}}</td> 
                                <td></td>
                                <td></td>     
                            </tr> 
                            <tr class="grey lighten-4 font-weight-black">
                                <td class="text-right" colspan="7">TOTAL TRANSAKSI</td>
                                <td class="text-right" >{{(totaltransaksi_canceled+totaltransaksi_paid+totaltransaksi_unpaid)|formatUang}}</td> 
                                <td></td>
                                <td></td>     
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
import Filter1 from '@/components/sidebar/FilterMode1';
export default {
    name:'TransaksiSPP',
    created()
    {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];   
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
                disabled:true,
                href:'#'
            }
        ];        
        this.tahun_akademik = this.$store.getters['uiadmin/getTahunAkademik'];      
    },
    mounted()
    {
        this.initialize()
    },
    data: () => ({
        firstloading:true,
        breadcrumbs: [],   
        tahun_akademik:0,
        btnLoading: false,    

        //tables
        datatableLoading:false,     
        datatable: [], 
        headers: [                                                
            { text: 'KODE BILLING', value: 'no_transaksi',width:100,sortable:true },
            { text: 'TANGGAL', value: 'tanggal',width:90,sortable:true },
            { text: 'NIM', value: 'nim',sortable:true,width:100 },
            { text: 'NAMA MAHASISWA', value: 'nama_mhs',sortable:true, width:250 },          
            { text: 'BULAN', value: 'nama_bulan',width:100,sortable:true },
            { text: 'SMT', value: 'idsmt',width:50,sortable:false },
            { text: 'JUMLAH', value: 'sub_total',width:100,sortable:false,align:'right' },
            { text: 'STATUS', value: 'nama_status',width:100,sortable:false },          
            { text: 'AKSI', value: 'actions', sortable: false,width:50 },
        ],      
        expanded: [],
        search: "", 

        dialogfrm:false,      

        //form data   
        form_valid:true,         
        daftar_semester: [],      
        formdata: {
            nim: "",
            semester_akademik: ""
        },
        formdefault: {
            nim: "",
            semester_akademik: ""
        },
        rule_nim: [
            value => !!value || "Nomor Induk Mahasiswa (NIM) mohon untuk diisi !!!",
            value => /^[0-9]+$/.test(value) || 'Nomor Induk Mahasiswa (NIM) hanya boleh angka',
        ], 
        rule_semester: [
            value => !!value || "Mohon dipilih Semester untuk transaksi ini !!!"
        ],  

    }),
    methods : {
        changeTahunAkademik (tahun)
        {
            this.tahun_akademik=tahun;
        },
        initialize:async function() 
        {
            this.datatableLoading=true;
            await this.$ajax.post('/keuangan/transaksi-spp',          
            {
                TA: this.tahun_akademik,
            },
            {
                headers: {
                    Authorization: this.$store.getters['auth/Token']
                }
            }).then(({ data }) => {               
                this.datatable = data.transaksi;    
                this.datatableLoading=false;
            });         
            this.firstloading=false;
            this.$refs.filter1.setFirstTimeLoading(this.firstloading);       
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
        async addItem ()
        {
            this.daftar_semester=this.$store.getters['uiadmin/getDaftarSemester'];  
            this.formdata.semester_akademik=this.semester_akademik;
            if (this.dashboard =='mahasiswa')
            {
                this.formdata.nim=this.$store.getters['auth/AttributeUser']('username');
            }
            this.dialogfrm=true;
        },
        viewItem(item)
        {
            this.$router.push('/keuangan/transaksi-spp/'+item.transaksi_id);
        },
        buatTransaksi:async function() {
            if (this.$refs.frmdata.validate())
            {
                await this.$ajax.post('/keuangan/transaksi-spp/new',
                    {
                        nim: this.formdata.nim, 
                        semester_akademik: this.formdata.semester_akademik,                                                        
                        TA: this.tahun_akademik,                                 
                    },
                    {
                        headers: {
                            Authorization: this.$store.getters['auth/Token']
                        }
                    }
                ).then(({ data }) => {                                        
                    this.btnLoading = false;                            
                    this.$router.push('/keuangan/transaksi-spp/tambah/'+data.transaksi.id);
                }).catch(() => {
                    this.btnLoading = false;
                });    
            }            
        },
        closedialogfrm() {
            this.dialogfrm = false;
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault);                    
                this.$refs.frmdata.reset(); 
                }, 300
            );
        },
    },
    computed: {
        totaltransaksi_paid()
        {
            var total=0;
            this.datatable.forEach(item => {
                if (item.status==1)
                {
                    total+=item.sub_total;
                }
            }); 
            return total;
        },
        totaltransaksi_unpaid()
        {
            var total=0;
            this.datatable.forEach(item => {
                if (item.status==0)
                {
                    total+=item.sub_total;
                }
            }); 
            return total;
        },
        totaltransaksi_canceled()
        {
            var total=0;
            this.datatable.forEach(item => {
                if (item.status==2)
                {
                    total+=item.sub_total;
                }
            }); 
            return total;
        }
    }, 
    watch: {
        
        search (val)
        {
            console.log('saerch',val);
        }
    },
    components: {
        KeuanganLayout,
        ModuleHeader,   
        Filter1    
    },
}
</script>
