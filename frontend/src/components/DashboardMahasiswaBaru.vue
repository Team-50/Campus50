<template>
    <v-row no-gutters>
        <v-col cols="12">
            <v-timeline align-top :dense="$vuetify.breakpoint.smAndDown">
                <!-- <v-timeline-item color="deep-orange lighten-2" icon="mdi-email-alert" fill-dot>
                    <v-card color="deep-orange lighten-2" dark>
                        <v-card-title class="title">Verifikasi Email</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>Email belum diverifikasi, silahkan diverifikasi !!!.</p>
                            <v-btn
                                color="deep-orange lighten-2"
                                class="mx-0"
                                outlined
                            >
                                Kirim Ulang
                            </v-btn>
                        </v-card-text>                    
                    </v-card>
                </v-timeline-item> -->
                <v-timeline-item color="purple darken-1" icon="mdi-book-variant" fill-dot>
                    <v-card color="purple darken-1" dark>
                        <v-card-title class="title">Formulir Pendaftaran</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>
                                Isi formulir pendaftaran dan lengkapi persyaratannya yaitu:
                                <ul>
                                    <li>Scan Pas Foto</li>
                                    <li>Scan Ijazah Terakhir</li>
                                    <li>Scan KTP</li>
                                    <li>Scan Kartu Keluarga</li>
                                </ul>
                            </p>                            
                            <v-btn
                                color="purple darken-1"
                                class="mx-0 mr-2"
                                outlined
                                to="/spmb/formulirpendaftaran"
                            >
                                Isi Formulir
                            </v-btn>
                            <v-btn
                                color="purple darken-1"
                                class="mx-0"
                                outlined
                                to="/spmb/persyaratan"
                            >
                                Upload Persyaratan
                            </v-btn>
                        </v-card-text>                    
                    </v-card>
                </v-timeline-item>
                <v-timeline-item color="red lighten-2" icon="mdi-account-cash" fill-dot>
                    <v-card color="red lighten-2" dark>
                        <v-card-title class="title">Konfirmasi Pembayaran</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>Konfirmasi Pembayaran Biaya Pendaftaran.</p>
                            <v-btn
                                color="red lighten-2"
                                class="mx-0"
                                outlined
                                to="/keuangan/konfirmasipembayaran">
                                Konfirmasi
                            </v-btn>
                        </v-card-text>                    
                    </v-card>
                </v-timeline-item>                
                <v-timeline-item color="indigo" icon="mdi-head-question-outline" fill-dot v-if="status_ujian">
                    <v-card color="indigo">
                        <v-card-title class="title text--white">Ujian Online</v-card-title>
                        <v-card-text class="white text--primary">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td width="25%">No. Peserta</td>
                                        <td>: {{peserta.no_peserta}}</td>
                                    </tr>
                                     <tr>
                                        <td width="25%">Tanggal Daftar</td>
                                        <td>: {{$date(peserta.created_at).format('DD/MM/YYYY HH:mm')}}</td>
                                    </tr>
                                    <tr>
                                        <td width="25%">Tanggal Ujian</td>
                                        <td>: {{$date(jadwal_ujian.tanggal_ujian).format('DD/MM/YYYY')}}</td>
                                    </tr>
                                    <tr>
                                        <td width="25%">Waktu Ujian</td>
                                        <td>: {{jadwal_ujian.jam_mulai_ujian}} - {{jadwal_ujian.jam_selesai_ujian}} ({{durasiUjian(jadwal_ujian)}})</td>
                                    </tr>
                                    <tr>
                                        <td width="25%">Keterangan</td>
                                        <td>: {{keterangan_ujian}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <v-btn
                                color="indigo"
                                class="mx-0"
                                @click.stop="mulaiUjian"
                                :disabled="ismulai"
                                outlined>
                                Mulai
                            </v-btn>
                        </v-card-text>                    
                    </v-card>
                </v-timeline-item>
                <v-timeline-item color="indigo" icon="mdi-head-question-outline" fill-dot v-else>
                    <v-card color="indigo">
                        <v-card-title class="title text--white">Ujian Online</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>Untuk mengikuti ujian online, silahkan pilih jadwal terlebih dahulu</p>
                            <v-btn
                                color="indigo"
                                class="mx-0 mr-2"
                                @click.stop="showPilihJadwal"
                                outlined>
                                Pilih Jadwal Ujian
                            </v-btn>
                        </v-card-text>                    
                    </v-card>                    
                </v-timeline-item>
                <!-- <v-timeline-item color="green lighten-1" icon="mdi-airballoon" fill-dot>
                    <v-card color="green lighten-1" dark>
                        <v-card-title class="title">Hasil Ujian</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>Hasil Ujian</p>
                            <v-btn
                                color="green lighten-1"
                                class="mx-0"
                                outlined
                            >
                                Button
                            </v-btn>
                        </v-card-text>                    
                    </v-card>
                </v-timeline-item> -->
            </v-timeline>
        </v-col>
        <v-dialog v-model="dialogpilihjadwal" persistent>                       
            <v-card>
                <v-card-title>
                    <span class="headline">Pilih Jadwal Ujian PMB</span>
                </v-card-title>
                <v-card-text>
                    <v-data-table
                        :headers="headers"
                        :items="datatable"                        
                        item-key="id"
                        sort-by="name"                        
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">
                        <template v-slot:item.tanggal_ujian="{ item }">
                            {{$date(item.tanggal_ujian).format('DD/MM/YYYY')}}
                        </template>
                        <template v-slot:item.tanggal_akhir_daftar="{ item }">
                            {{$date(item.tanggal_akhir_daftar).format('DD/MM/YYYY')}}
                        </template>
                        <template v-slot:item.durasi_ujian="{ item }">
                            {{item.jam_mulai_ujian}} - {{item.jam_selesai_ujian}} <br>({{durasiUjian(item)}})
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon                                
                                @click.stop="pilihJadwal(item)"
                                :loading="btnLoading"
                                :disabled="btnLoading">
                                mdi-select-drag
                            </v-icon>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>                    
                </v-card-actions>
            </v-card>            
        </v-dialog>
    </v-row>
</template>
<script>
export default {
    name: 'DashboardMahasiswaBaru',
    created()
    {
        this.initialize();          
        this.$store.dispatch('uiadmin/deletePage','ujianonline');
    },
    data:()=>({
        btnLoading:false,
        datatableLoading:false,        
        datatable:[],
        headers: [                                        
            { text: 'NAMA UJIAN', value: 'nama_kegiatan', sortable: true,width:300 },
            { text: 'TGL. UJIAN', value: 'tanggal_ujian', sortable: true,width:100 },
            { text: 'TGL. AKHIR PENDAFTARAN', value: 'tanggal_akhir_daftar', sortable: true,width:100 },
            { text: 'DURASI UJIAN', value: 'durasi_ujian', sortable: true,width:100 },
            { text: 'RUANGAN', value: 'namaruang', sortable: true,width:100 },
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },
        ],
        dialogpilihjadwal:false,
        ismulai:true,

        status_ujian:false,
        jadwal_ujian:null,
        peserta:null,
        keterangan_ujian:'',
    }),
    methods: {
        initialize:async function ()
        {
            await this.$ajax.get('/spmb/ujianonline/peserta/'+this.$store.getters['auth/AttributeUser']('id'),            
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{          
                if (data.status == 1)               
                {
                    this.status_ujian=true;
                    this.peserta = data.peserta;                       
                    this.jadwal_ujian = data.jadwal_ujian;      
                    this.ismulai=this.jadwal_ujian.status_ujian == 0 ?true:false;
                    if (this.peserta.isfinish==1)
                    {
                        this.ismulai=true;
                        this.keterangan_ujian='SELESAI UJIAN';
                    }
                    else
                    {
                        this.keterangan_ujian='BELUM UJIAN';
                    }
                }
            });  
        },
        showPilihJadwal:async function()
        {
            this.dialogpilihjadwal = true;  
            let tahun_pendaftaran=this.$store.getters['auth/AttributeUser']('ta');        
            let semester_pendaftaran=this.$store.getters['auth/AttributeUser']('idsmt');                                

            this.datatableLoading=true;
            await this.$ajax.post('/spmb/ujianonline/jadwal',
            {
                tahun_pendaftaran:tahun_pendaftaran,
                semester_pendaftaran:semester_pendaftaran
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                        
                this.datatable = data.jadwal_ujian;
                this.datatableLoading=false;
            }).catch(()=>{
                this.datatableLoading=false;
            });  
        },
        pilihJadwal:async function(item)
        {
            this.btnLoading=true;
            await this.$ajax.post('/spmb/ujianonline/daftar',
            {
                user_id:this.$store.getters['auth/AttributeUser']('id'),
                jadwal_ujian_id:item.id,                
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(()=>{               
                this.initialize();         
                this.closedialogfrm();
                this.btnLoading=false;
            }).catch(()=>{
                this.btnLoading=false;
            });  
        },
        durasiUjian (item)
        {
            let waktu_mulai = this.$date(item.tanggal_ujian + ' '+item.jam_mulai_ujian);
            let waktu_selesai = this.$date(item.tanggal_ujian + ' '+item.jam_selesai_ujian);
            return waktu_selesai.diff(waktu_mulai,'minute') + ' menit';
        },
        mulaiUjian:async function()
        {          
            this.btnLoading=false;
            await this.$ajax.post('/spmb/ujianonline/mulaiujian',
            {
                _method:'put',
                user_id:this.$store.getters['auth/AttributeUser']('id'),                
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{               
                this.btnLoading=false;
                this.$store.dispatch('uiadmin/addToPages',{
                    name:'ujianonline',
                    data_ujian:this.jadwal_ujian,
                    data_peserta:data.peserta,                
                });
                this.$router.push('/spmb/ujianonline');                
            }).catch(()=>{
                this.btnLoading=false;
            });              
        },
        closedialogfrm () {
            this.dialogpilihjadwal = false;                        
        },
    }
}
</script>