<template>
    <SPMBLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-calendar-account
            </template>
            <template v-slot:name>
                JADWAL UJIAN PMB
            </template>
            <template v-slot:subtitle>
                TAHUN PENDAFTARAN {{tahun_pendaftaran}} - SEMESTER {{nama_semester_pendaftaran}}
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
                    Berisi daftar dan pengelolaan jadwal ujian PMB.
                </v-alert>
            </template>
        </ModuleHeader>   
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
                        sort-by="name"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">

                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR JADWAL UJIAN PMB</v-toolbar-title>
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
                                    @click.stop="addItem"
                                    v-if="dashboard !='mahasiswabaru' && dashboard !='mahasiswa'">
                                        TAMBAH
                                </v-btn>
                                <v-dialog v-model="dialogfrm" max-width="800px" persistent>
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }}</span>
                                            </v-card-title>
                                            <v-card-text>
                                                <v-text-field 
                                                    v-model="formdata.nama_kegiatan" 
                                                    label="NAMA UJIAN ONLINE"
                                                    outlined
                                                    :rules="rule_nama_kegiatan">
                                                </v-text-field>  
                                                Jumlah soal, pastikan lebih kecil atau sama dengan jumlah soal BANK SOAL.                                                                            
                                                <v-text-field 
                                                    v-model="formdata.jumlah_soal" 
                                                    label="JUMLAH SOAL"
                                                    outlined
                                                    :rules="rule_jumlah_soal">
                                                </v-text-field>  
                                                <v-menu
                                                    ref="menuTanggalAkhirPendaftaran"
                                                    v-model="menuTanggalAkhirPendaftaran"
                                                    :close-on-content-click="false"
                                                    :return-value.sync="formdata.tanggal_akhir_daftar"
                                                    transition="scale-transition"
                                                    offset-y
                                                    max-width="290px"
                                                    min-width="290px">
                                                    <template v-slot:activator="{ on }">
                                                        <v-text-field
                                                            v-model="formdata.tanggal_akhir_daftar"
                                                            label="TANGGAL AKHIR PENDAFTARAN"                                            
                                                            readonly
                                                            outlined
                                                            v-on="on"></v-text-field>
                                                    </template>
                                                    <v-date-picker
                                                        v-model="formdata.tanggal_akhir_daftar"                                        
                                                        no-title                                
                                                        scrollable>                                                        
                                                        <v-spacer></v-spacer>
                                                        <v-btn text color="primary" @click="menuTanggalAkhirPendaftaran = false">Cancel</v-btn>
                                                        <v-btn text color="primary" @click="$refs.menuTanggalAkhirPendaftaran.save(formdata.tanggal_akhir_daftar)">OK</v-btn>
                                                    </v-date-picker>
                                                </v-menu>                                                
                                                <v-menu
                                                    ref="menuTanggalUjian"
                                                    v-model="menuTanggalUjian"
                                                    :close-on-content-click="false"
                                                    :return-value.sync="formdata.tanggal_ujian"
                                                    transition="scale-transition"
                                                    offset-y
                                                    max-width="290px"
                                                    min-width="290px">
                                                    <template v-slot:activator="{ on }">
                                                        <v-text-field
                                                            v-model="formdata.tanggal_ujian"
                                                            label="TANGGAL UJIAN"                                            
                                                            readonly
                                                            outlined
                                                            v-on="on">
                                                        </v-text-field>
                                                    </template>
                                                    <v-date-picker
                                                        v-model="formdata.tanggal_ujian"                                        
                                                        no-title                                
                                                        scrollable>                                                        
                                                        <v-spacer></v-spacer>
                                                        <v-btn text color="primary" @click="menuTanggalUjian = false">Cancel</v-btn>
                                                        <v-btn text color="primary" @click="$refs.menuTanggalUjian.save(formdata.tanggal_ujian)">OK</v-btn>
                                                    </v-date-picker>
                                                </v-menu>
                                                <v-menu
                                                    ref="menuJamMulaiUjian"
                                                    v-model="menuJamMulaiUjian"
                                                    :close-on-content-click="false"
                                                    :nudge-right="40"
                                                    :return-value.sync="formdata.jam_mulai_ujian"
                                                    transition="scale-transition"
                                                    offset-y
                                                    max-width="290px"
                                                    min-width="290px">
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field
                                                            v-model="formdata.jam_mulai_ujian"
                                                            label="JAM MULAI UJIAN"                                                            
                                                            readonly
                                                            outlined
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        ></v-text-field>
                                                    </template>
                                                    <v-time-picker
                                                        v-if="menuJamMulaiUjian"
                                                        v-model="formdata.jam_mulai_ujian"
                                                        full-width
                                                        format="24hr"
                                                        @click:minute="$refs.menuJamMulaiUjian.save(formdata.jam_mulai_ujian)"
                                                    ></v-time-picker>
                                                </v-menu>
                                                <v-menu
                                                    ref="menuJamSelesaiUjian"
                                                    v-model="menuJamSelesaiUjian"
                                                    :close-on-content-click="false"
                                                    :nudge-right="40"
                                                    :return-value.sync="formdata.jam_selesai_ujian"
                                                    transition="scale-transition"
                                                    offset-y
                                                    max-width="290px"
                                                    min-width="290px">
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field
                                                            v-model="formdata.jam_selesai_ujian"
                                                            label="JAM SELESAI UJIAN"                                                            
                                                            readonly
                                                            outlined
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        ></v-text-field>
                                                    </template>
                                                    <v-time-picker
                                                        v-if="menuJamSelesaiUjian"
                                                        v-model="formdata.jam_selesai_ujian"
                                                        full-width
                                                        format="24hr"
                                                        @click:minute="$refs.menuJamSelesaiUjian.save(formdata.jam_selesai_ujian)"
                                                    ></v-time-picker>
                                                </v-menu> 
                                                <v-select
                                                    label="RUANG UJIAN"
                                                    :items="daftar_ruangan"
                                                    v-model="formdata.ruangkelas_id"
                                                    item-text="namaruang"
                                                    item-value="id"
                                                    outlined
                                                />
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
                                                <v-btn 
                                                    color="blue darken-1" 
                                                    text 
                                                    @click.stop="save" 
                                                    :loading="btnLoading"
                                                    :disabled="!form_valid||btnLoading">
                                                        SIMPAN
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </v-dialog>
                                <v-dialog v-model="dialogdetailitem" max-width="800px" persistent>
                                    <v-card>
                                        <v-card-title>
                                            <span class="headline">DETAIL DATA</span>
                                        </v-card-title>
                                        <v-card-text>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>ID :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.id}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                                                
                                                
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>JUMLAH SOAL :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.jumlah_soal}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>NAMA UJIAN ONLINE :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.nama_kegiatan}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>TANGGAL AKHIR DAFTAR :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{$date(formdata.tanggal_akhir_daftar).format('DD/MM/YYYY')}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>TANGGAL UJIAN :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{$date(formdata.tanggal_ujian).format('DD/MM/YYYY')}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>WAKTU UJIAN PMB :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{$date(formdata.tanggal_ujian).format('DD/MM/YYYY')}} {{formdata.jam_mulai_ujian}} - {{formdata.jam_selesai_ujian}} ({{durasiUjian(formdata)}} Menit)
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>STATUS PENDAFTARAN :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.status_pendaftaran == 0 ? 'BUKA':'TUTUP'}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>CREATED :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{$date(formdata.created_at).format('DD/MM/YYYY HH:mm')}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>STATUS UJIAN :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.status_ujian == 0 ? 'BUKA':'TUTUP'}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>UPDATED :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{$date(formdata.updated_at).format('DD/MM/YYYY HH:mm')}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="green darken-1" text @click.stop="mulaiUjian" :disabled="btnLoading" :loading="btnLoading">MULAI</v-btn>
                                            <v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">KELUAR</v-btn>
                                        </v-card-actions>
                                    </v-card>                                    
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.tanggal_ujian="{ item }">
                            {{$date(item.tanggal_ujian).format('DD/MM/YYYY')}}
                        </template>
                        <template v-slot:item.tanggal_akhir_daftar="{ item }">
                            {{$date(item.tanggal_akhir_daftar).format('DD/MM/YYYY')}}
                        </template>
                        <template v-slot:item.durasi_ujian="{ item }">
                            {{item.jam_mulai_ujian}} - {{item.jam_selesai_ujian}} <br>({{durasiUjian(item)}} Menit)
                        </template>
                        <template v-slot:item.actions="{ item }" v-if="dashboard !='mahasiswabaru' && dashboard !='mahasiswa'">
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="viewItem(item)">
                                mdi-eye
                            </v-icon>
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="editItem(item)">
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                small
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="deleteItem(item)">
                                mdi-delete
                            </v-icon>
                        </template>
                        <template v-slot:item.actions v-else>
                            N.A
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">
                                    <strong>ID:</strong>{{ item.id }} 
                                    <strong>Ruangan:</strong>{{item.namaruang}} 
                                    <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }} 
                                    <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }} 
                                </v-col>     
                                <v-col cols="12">
                                    <v-btn text small color="primary" :to="'/spmb/jadwalujianpmb/passinggrade/'+item.id" v-if="dashboard !='mahasiswabaru' && dashboard !='mahasiswa'">TENTUKAN PASSING GRADE</v-btn>
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
    name:'JadwalUjianPMB',
    created () {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];
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
                disabled:true,
                href:'#'
            }
        ];        
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];        
        this.semester_pendaftaran=this.$store.getters['uiadmin/getSemesterPendaftaran'];  
        this.nama_semester_pendaftaran=this.$store.getters['uiadmin/getNamaSemester'](this.semester_pendaftaran);  
        this.initialize();
    },  
    data () 
    { 
        let tanggal_ujian=this.$date().format('YYYY-MM-DD');
        return {
            breadcrumbs:[],        
            dashboard:null,
            
            firstloading:true,
            tahun_pendaftaran:null,
            semester_pendaftaran:null,
            nama_semester_pendaftaran:null,

            btnLoading:false,
            datatableLoading:false,
            expanded:[],
            datatable:[],
            headers: [                                        
                { text: 'NAMA UJIAN', value: 'nama_kegiatan', sortable: true,width:300 },
                { text: 'TGL. UJIAN', value: 'tanggal_ujian', sortable: true,width:100 },
                { text: 'TGL. AKHIR PENDAFTARAN', value: 'tanggal_akhir_daftar', sortable: true,width:100 },
                { text: 'DURASI UJIAN', value: 'durasi_ujian', sortable: true,width:100 },
                { text: 'JUMLAH PESERTA', value: 'jumlah_peserta', sortable: true,width:100 },
                { text: 'AKSI', value: 'actions', sortable: false,width:100 },
            ],
            search:'',    

            //dialog
            dialogfrm:false,
            dialogdetailitem:false,
            
            //form data   
            form_valid:true, 
            jumlah_bank_soal:0,
            daftar_ruangan:[],
            
            menuTanggalUjian:false,        
            menuJamMulaiUjian:false,        
            menuJamSelesaiUjian:false,        
            menuTanggalAkhirPendaftaran:false,        
            formdata: {
                id:0,                        
                nama_kegiatan:'',
                jumlah_soal:'',   
                tanggal_ujian:tanggal_ujian,    
                jam_mulai_ujian:'',                    
                jam_selesai_ujian:'',                    
                tanggal_akhir_daftar:tanggal_ujian,                                                                
                ruangkelas_id:'',                        
                ta:'',                        
                idsmt:'',                        
                status_pendaftaran:'',                        
                status_ujian:'',                        
                created_at: '',           
                updated_at: '',           

            },
            formdefault: {
                id:0,                        
                nama_kegiatan:'',             
                jumlah_soal:'',                                   
                tanggal_ujian:this.$date().format('YYYY-MM-DD'),   
                jam_mulai_ujian:'',                    
                jam_selesai_ujian:'',                              
                tanggal_akhir_daftar:tanggal_ujian,                        
                durasi_ujian:'',                        
                ruangkelas_id:'',                        
                ta:'',                        
                idsmt:'',                        
                status_pendaftaran:'',                        
                status_ujian:'',                        
                created_at: '',           
                updated_at: '',       
            },
            editedIndex: -1,

            //form rules          
            rule_nama_kegiatan:[
                value => !!value||"Mohon untuk di isi nama ujian online !!!",                  
            ], 
            rule_jumlah_soal:[
                value => !!value||"Mohon untuk di isi jumlah soal ujian !!!",  
                value => /^[0-9]+$/.test(value) || 'Jumlah soal ujian hanya boleh angka',    
                value => {
                    if (value && typeof value !== 'undefined' && value.length > 0) 
                    {
                        let jumlah_bank_soal = parseInt(this.jumlah_bank_soal);
                        let jumlah_soal = parseInt(value);
                        return jumlah_soal <= jumlah_bank_soal ||"Jumlah soal harus lebih kecil atau sama dengan jumlah di bank soal ("+jumlah_bank_soal+")";
                    }
                    else
                    {
                        return true;
                    }
                }            
            ], 
        }
    },
    methods: {
        changeTahunPendaftaran (tahun)
        {
            this.tahun_pendaftaran=tahun;
        },
        changeSemesterPendaftaran (semester)
        {
            this.semester_pendaftaran=semester;
        },
        initialize:async function () 
        {
            this.datatableLoading=true;
            await this.$ajax.post('/spmb/jadwalujianpmb',
            {
                tahun_pendaftaran:this.tahun_pendaftaran,
                semester_pendaftaran:this.semester_pendaftaran
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                      
                this.jumlah_bank_soal=data.jumlah_bank_soal;                
                this.datatable = data.jadwal_ujian;
                this.datatableLoading=false;
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
        addItem:async function ()
        {
            this.btnLoading=true;
            await this.$ajax.get('/datamaster/ruangankelas',       
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{               
                this.daftar_ruangan = data.ruangan;
                this.btnLoading=false;
                this.dialogfrm=true;
            }).catch(()=>{
                this.btnLoading=false;
            });              
        },
        durasiUjian (item)
        {
            let waktu_mulai = this.$date(item.tanggal_ujian + ' '+item.jam_mulai_ujian);
            let waktu_selesai = this.$date(item.tanggal_ujian + ' '+item.jam_selesai_ujian);
            return waktu_selesai.diff(waktu_mulai,'minute');
        },
        viewItem (item) {
            this.formdata=item;      
            this.dialogdetailitem=true;              
            
            // this.$ajax.get('/path/'+item.id,{
            //     headers: {
            //         Authorization:this.$store.getters['auth/Token']
            //     }
            // }).then(({data})=>{               
                                           
            // });                      
        },  
        mulaiUjian:async function ()
        {
            this.btnLoading=true;
            await this.$ajax.post('/spmb/jadwalujianpmb/updatestatusujian/'+this.formdata.id,
            {
                '_method':'PUT',
                status_ujian:1,                
            },
            {
                headers:{
                    Authorization:this.$store.getters['auth/Token']
                }
            }
        ).then(()=>{                                       
            this.btnLoading=false;            
        }).catch(()=>{
            this.btnLoading=false;
        });
        },
        editItem:async function (item) {            
            await this.$ajax.get('/datamaster/ruangankelas',       
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{               
                this.daftar_ruangan = data.ruangan;
                this.btnLoading=false;
                this.editedIndex = this.datatable.indexOf(item);
                this.formdata = Object.assign({}, item);
                this.dialogfrm=true;
            }).catch(()=>{
                this.btnLoading=false;
            });              
        },    
        save:async function () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                if (this.editedIndex > -1) 
                {
                    await this.$ajax.post('/spmb/jadwalujianpmb/'+this.formdata.id,
                        {
                            '_method':'PUT',
                            nama_kegiatan:this.formdata.nama_kegiatan,
                            jumlah_soal:this.formdata.jumlah_soal,
                            tanggal_ujian:this.formdata.tanggal_ujian,    
                            jam_mulai_ujian:this.formdata.jam_mulai_ujian,                    
                            jam_selesai_ujian:this.formdata.jam_selesai_ujian,                    
                            tanggal_akhir_daftar:this.formdata.tanggal_akhir_daftar,   
                            durasi_ujian:this.durasiUjian(this.formdata),                                                                         
                            ruangkelas_id:this.formdata.ruangkelas_id,                                  
                        },
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token']
                            }
                        }
                    ).then(()=>{                           
                        this.closedialogfrm();
                        this.btnLoading=false;
                        this.initialize();
                    }).catch(()=>{
                        this.btnLoading=false;
                    });                 
                    
                } else {
                    await this.$ajax.post('/spmb/jadwalujianpmb/store',
                        {               
                            nama_kegiatan:this.formdata.nama_kegiatan,
                            jumlah_soal:this.formdata.jumlah_soal,
                            tanggal_ujian:this.formdata.tanggal_ujian,    
                            jam_mulai_ujian:this.formdata.jam_mulai_ujian,                    
                            jam_selesai_ujian:this.formdata.jam_selesai_ujian,                    
                            tanggal_akhir_daftar:this.formdata.tanggal_akhir_daftar,                                                                            
                            durasi_ujian:this.durasiUjian(this.formdata),                                                                            
                            ruangkelas_id:this.formdata.ruangkelas_id,
                            ta:this.tahun_pendaftaran,                        
                            idsmt:this.semester_pendaftaran,                                       
                        },
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token']
                            }
                        }
                    ).then(()=>{                           
                        this.closedialogfrm();
                        this.btnLoading=false;
                        this.initialize();
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }
            }
        },
        deleteItem (item) {           
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus data dengan ID '+item.id+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoading=true;
                    this.$ajax.post('/spmb/jadwalujianpmb/'+item.id,
                        {
                            '_method':'DELETE',
                        },
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token']
                            }
                        }
                    ).then(()=>{   
                        const index = this.datatable.indexOf(item);
                        this.datatable.splice(index, 1);
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }                
            });
        },
        closedialogdetailitem () {
            this.dialogdetailitem = false;            
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault)
                this.editedIndex = -1
                }, 300
            );
        },
        closedialogfrm () {
            this.dialogfrm = false;            
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault);                
                this.editedIndex = -1
                this.$refs.frmdata.reset(); 
                }, 300
            );
        },
    },
    computed: {        
        formTitle () {
            return this.editedIndex === -1 ? 'TAMBAH JADWAL' : 'UBAH JADWAL'
        },        
    },
    components:{
        SPMBLayout,
        ModuleHeader,        
    },

}
</script>