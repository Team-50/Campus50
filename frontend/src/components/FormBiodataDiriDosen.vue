<template>
    <v-row class="mb-4" no-gutters>
        <v-col cols="12">
            <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                <v-card class="mb-4">
                    <v-card-title>
                        BIODATA DIRI DOSEN
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field
                                    label="NIDN"    
                                    v-model="formdata.nidn"    
                                    :rules="rule_nidn"
                                    filled
                                />     
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    label="NIP YAYASAN"    
                                    v-model="formdata.nipy"    
                                    :rules="rule_nipy"
                                    filled
                                />
                            </v-col> 
                        </v-row>   
                        <v-row>
                            <v-col cols="3">
                                <v-text-field
                                    label="GELAR DEPAN"    
                                    v-model="formdata.gelar_depan"                                        
                                    filled
                                />
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    label="NAMA LENGKAP"    
                                    v-model="formdata.nama_dosen"    
                                    :rules="rule_nama_dosen"
                                    filled
                                />
                            </v-col>
                            <v-col cols="3">
                                <v-text-field
                                    label="GELAR BELAKANG"    
                                    v-model="formdata.gelar_belakang"                                        
                                    filled
                                />
                            </v-col>
                        </v-row>                        
                        <v-text-field
                            label="TEMPAT LAHIR"
                            v-model="formdata.tempat_lahir"     
                            :rules="rule_tempat_lahir"                   
                            filled
                        />
                        <v-menu
                            ref="menuTanggalLahir"
                            v-model="menuTanggalLahir"
                            :close-on-content-click="false"
                            :return-value.sync="formdata.tanggal_lahir"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    v-model="formdata.tanggal_lahir"
                                    label="TANGGAL LAHIR"                                            
                                    readonly
                                    filled
                                    v-on="on"
                                    :rules="rule_tanggal_lahir"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="formdata.tanggal_lahir"                                        
                                no-title                                
                                scrollable
                                >
                                <v-spacer></v-spacer>
                                <v-btn text color="primary" @click="menuTanggalLahir = false">Cancel</v-btn>
                                <v-btn text color="primary" @click="$refs.menuTanggalLahir.save(formdata.tanggal_lahir)">OK</v-btn>
                            </v-date-picker>
                        </v-menu>
                        <v-radio-group v-model="formdata.jk" row>
                            JENIS KELAMIN : 
                            <v-radio label="LAKI-LAKI" value="L"></v-radio>
                            <v-radio label="PEREMPUAN" value="P"></v-radio>
                        </v-radio-group>
                        <v-text-field
                            label="NOMOR HP"
                            v-model="formdata.nomor_hp"
                            filled
                            :rules="rule_nomorhp"
                        />
                        <v-text-field
                            label="EMAIL"
                            v-model="formdata.email"
                            :rules="rule_email"
                            filled
                        />                       
                    </v-card-text>
                </v-card>
                <v-card class="mb-4">
                    <v-card-title>
                        ALAMAT
                    </v-card-title>
                    <v-card-text>
                        <v-select
                            label="PROVINSI"
                            :items="daftar_provinsi"
                            v-model="provinsi_id"
                            item-text="nama"
                            item-value="id"
                            return-object
                            :loading="btnLoadingProv"
                            filled
                        />
                        <v-select
                            label="KABUPATEN/KOTA"
                            :items="daftar_kabupaten"
                            v-model="kabupaten_id"
                            item-text="nama"
                            item-value="id"
                            return-object
                            :loading="btnLoadingKab"
                            filled
                        />
                        <v-select
                            label="KECAMATAN"
                            :items="daftar_kecamatan"
                            v-model="kecamatan_id"
                            item-text="nama"
                            item-value="id" 
                            return-object                           
                            filled
                        />
                        <v-select
                            label="DESA/KELURAHAN"
                            :items="daftar_desa"
                            v-model="desa_id"
                            item-text="nama"
                            item-value="id"
                            return-object
                            :rules="rule_desa"
                            filled
                        />
                        <v-text-field
                            v-model="formdata.alamat_rumah"
                            label="ALAMAT RUMAH"
                            :rules="rule_alamat_rumah"
                            filled
                        />
                    </v-card-text>
                </v-card>               
                <v-card class="mb-4">                    
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
</template>
<script>
export default {
    name:'FormBiodataDiriDosen',
    created()
    {
        this.initialize();
    },
    data:()=>({
        btnLoading: false,
        btnLoadingProv:false,
        btnLoadingKab:false,
        btnLoadingKec:false,
        btnLoadingFakultas:false,

        //form
        kode_billing:'N.A',
        form_valid:true,

        menuTanggalLahir:false,

        daftar_provinsi: [],
        provinsi_id:0,

        daftar_kabupaten: [],
        kabupaten_id:0,

        daftar_kecamatan: [],
        kecamatan_id:0,

        daftar_desa: [],
        desa_id:0,

        daftar_fakultas: [],
        kode_fakultas: "",

        daftar_prodi: [],      
        daftar_kelas: [],
        
        formdata: {
            user_id: "",
            nidn: "",
            nipy: "",
            nama_dosen: "",
            gelar_depan: "",
            gelar_belakang: "",
            
            tempat_lahir: "", 
            tanggal_lahir: "", 

            address1_desa_id: "", 
            address1_kelurahan: "", 
            address1_kecamatan_id: "",
            address1_kecamatan: "", 
            address1_kabupaten_id: "", 
            address1_kabupaten: "", 
            address1_provinsi_id: "",
            address1_provinsi: "", 
            alamat_rumah: "", 
            
            nik: "",
            email: "",
            nomor_hp: "",
            address2_desa_id: "", 
            address2_kelurahan: "", 
            address2_kecamatan_id: "",
            address2_kecamatan: "", 
            address2_kabupaten_id: "", 
            address2_kabupaten: "", 
            address2_provinsi_id: "", 
            address2_provinsi: "", 
            alamat_ktp: "", 

            is_dw: "",
            desc: "",
            active: "",
        },
        rule_nidn: [                         
            value => /^[0-9]+$/.test(value) || 'NIDN hanya boleh angka',              
        ],       
        rule_nipy: [            
            value => /^[0-9]+$/.test(value) || 'Nomor Induk Pegawai Yayasan (NIPY) hanya boleh angka',              
        ], 
        rule_nama_dosen: [
            value => !!value || "Nama Mahasiswa mohon untuk diisi !!!",
            value => /^[A-Za-z\s\\,\\.]*$/.test(value) || 'Nama Mahasiswa hanya boleh string dan spasi',
        ],       
        rule_tempat_lahir: [
            value => !!value || "Tempat Lahir mohon untuk diisi !!!"
        ], 
        rule_tanggal_lahir: [
            value => !!value || "Tanggal Lahir mohon untuk diisi !!!"
        ], 
        rule_nomorhp: [
            value => !!value || "Nomor HP mohon untuk diisi !!!",
            value => /^\+[1-9]{1}[0-9]{1,14}$/.test(value) || 'Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388',
        ], 
        rule_email: [
            value => !!value || "Email mohon untuk diisi !!!",
            value => /.+@.+\..+/.test(value) || 'Format E-mail mohon di isi dengan benar',
        ], 
        rule_desa: [
            value => !!value || "Mohon Desa mohon untuk diisi !!!"
        ], 
        rule_alamat_rumah: [
            value => !!value || "Alamat Rumah mohon untuk diisi !!!"
        ],       
    }),
    methods: {
        initialize:async function()
        {
            this.$ajax.get('/datamaster/provinsi').then(({ data }) => {                
                this.daftar_provinsi=data.provinsi;    
            });           
            await this.$ajax.get('/system/usersdosen/biodatadiri/'+this.$store.getters['auth/AttributeUser']('id'),           
                {
                    headers: {
                        Authorization: this.$store.getters['auth/Token']
                    }
                },
                
            ).then(({ data }) => {   
                this.formdata=data.biodatadiri;           

                this.provinsi_id={
                    id:data.biodatadiri.address1_provinsi_id,
                    nama:data.biodatadiri.address1_provinsi
                };
                this.kabupaten_id={
                    id:data.biodatadiri.address1_kabupaten_id,
                    nama:data.biodatadiri.address1_kabupaten
                };
                this.kecamatan_id={
                    id:data.biodatadiri.address1_kecamatan_id,
                    nama:data.biodatadiri.address1_kecamatan
                };
                this.desa_id={
                    id:data.biodatadiri.address1_desa_id,
                    nama:data.biodatadiri.address1_kelurahan
                };    
                this.formdata.alamat_rumah=data.biodatadiri.alamat_rumah;        
                this.$refs.frmdata.resetValidation();       
            });
        },      
        save: async function()
        {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading = true;    
                await this.$ajax.post('/system/usersdosen/biodatadiri/'+this.$store.getters['auth/AttributeUser']('id'),{                    
                    _method:'put',
                    nidn: this.formdata.nidn,         
                    nipy: this.formdata.nipy,         
                    gelar_depan: this.formdata.gelar_depan,           
                    nama_dosen: this.formdata.nama_dosen,         
                    gelar_belakang: this.formdata.gelar_belakang,         

                    tempat_lahir: this.formdata.tempat_lahir,         
                    tanggal_lahir: this.formdata.tanggal_lahir,         
                    jk: this.formdata.jk,         
                    nomor_hp: this.formdata.nomor_hp,         
                    email: this.formdata.email,  
                       
                    address1_provinsi_id: this.provinsi_id.id,
                    address1_provinsi: this.provinsi_id.nama,
                    address1_kabupaten_id: this.kabupaten_id.id,
                    address1_kabupaten: this.kabupaten_id.nama,
                    address1_kecamatan_id: this.kecamatan_id.id,
                    address1_kecamatan: this.kecamatan_id.nama,
                    address1_desa_id: this.desa_id.id,
                    address1_kelurahan: this.desa_id.nama,
                    alamat_rumah: this.formdata.alamat_rumah,  

                    
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
    },
    watch: {
        provinsi_id(val)
        {
            if (val.id != null && val.id != '')
            {
                this.btnLoadingProv=true;
                this.$ajax.get('/datamaster/provinsi/'+val.id+'/kabupaten').then(({ data }) => {                
                    this.daftar_kabupaten=data.kabupaten;
                    this.btnLoadingProv=false;
                });
                this.daftar_kecamatan=[];
            }
        },
        kabupaten_id(val)
        {
            if (val.id != null && val.id != '')
            {
                this.btnLoadingKab=true;
                this.$ajax.get('/datamaster/kabupaten/'+val.id+'/kecamatan').then(({ data }) => {                                
                    this.daftar_kecamatan=data.kecamatan;
                    this.btnLoadingKab=false;
                });
            }
        },
        kecamatan_id(val)
        {
            if (val.id != null && val.id != '')
            {
                this.btnLoadingKec=true;
                this.$ajax.get('/datamaster/kecamatan/'+val.id+'/desa').then(({ data }) => {                                
                    this.daftar_desa=data.desa;
                    this.btnLoadingKec=false;
                });
            }
        },
        kode_fakultas (val)
        {
            this.btnLoadingFakultas=true;
            this.$ajax.get('/datamaster/fakultas/'+val+'/programstudi').then(({ data }) => {                                
                this.daftar_prodi=data.programstudi;
                this.btnLoadingFakultas=false;
            });
        }

    }
}
</script>