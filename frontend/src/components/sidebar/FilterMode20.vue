<template>
    <v-list-item>
        <v-list-item-content>
            <v-select
                v-model="fakultas_id"
                :items="daftar_fakultas"                
                item-text="text"
                item-value="id"
                label="FAKULTAS"
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
    name:'FilterMode20',
    created()
    {
        this.daftar_fakultas=this.$store.getters['uiadmin/getDaftarFakultas'];  
        this.fakultas_id=this.$store.getters['uiadmin/getFakultasID'];                        
        
        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran']; 
    },
    data:()=>({
        firstloading:true,
        daftar_fakultas: [],
        fakultas_id:null,

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
        fakultas_id(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateFakultas',val);  
                this.$emit('changeFakultas',val);          
            }
        },
    }
}
</script>