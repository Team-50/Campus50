<template>
    <v-list-item>
        <v-list-item-content>                     
            <v-select
                v-model="tahun_pendaftaran"
                :items="daftar_ta"                
                label="TAHUN PENDAFTARAN"
                outlined/>   
            <v-select
                v-model="idkelas"
                :items="daftar_kelas"                
                item-text="text"
                item-value="id"
                label="KELAS"
                outlined/>   
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name:'FilterMode10',
    created()
    {
        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];  

        this.daftar_kelas=this.$store.getters['uiadmin/getDaftarKelas'];  
        this.idkelas=this.$store.getters['uiadmin/getIDKelas'];                                
    },
    data:()=>({
        firstloading:true,
        
        daftar_kelas: [],
        idkelas:null,

        daftar_ta: [],
        tahun_pendaftaran:null
    }),
    methods: {
        setFirstTimeLoading (bool)
        {
            this.firstloading=bool;
        }
    },
    watch: {
        tahun_pendaftaran(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateTahunPendaftaran',val);  
                this.$emit('changeTahunPendaftaran',val);          
            }            
        },
        idkelas(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateIDKelas',val);  
                this.$emit('changeIDKelas',val);          
            }
        },
    }
}
</script>