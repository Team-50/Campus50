<template>
    <v-list-item>
        <v-list-item-content>
            <v-select
                v-model="prodi_id"
                :items="daftar_prodi"                
                item-text="text"
                item-value="id"
                label="PROGRAM STUDI"
                outlined/>            
            <v-select
                v-model="tahun_pendaftaran"
                :items="daftar_ta"                
                label="TAHUN PENDAFTARAN"
                outlined/>            
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name:'FilterMode7',
    created()
    {
        this.daftar_prodi=this.$store.getters['uiadmin/getDaftarProdi'];  
        this.prodi_id=this.$store.getters['uiadmin/getProdiID'];                        

        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];  
    },
    data:()=>({
        firstloading:true,
        daftar_prodi: [],
        prodi_id:null,

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
        prodi_id(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateProdi',val);  
                this.$emit('changeProdi',val);          
            }
        },
    }
}
</script>