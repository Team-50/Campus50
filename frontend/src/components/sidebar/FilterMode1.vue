<template>
    <v-list-item>
        <v-list-item-content>                     
            <v-select
                v-model="tahun_akademik"
                :items="daftar_ta"                
                label="TAHUN AKADEMIK"
                outlined/> 
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name:'FilterMode1',
    created()
    {
        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];  
    },
    data:()=>({
        firstloading:true,
        
        daftar_ta: [],
        tahun_akademik:null
    }),
    methods: {
        setFirstTimeLoading (bool)
        {
            this.firstloading=bool;
        }
    },
    watch: {
        tahun_akademik(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateTahunAkademik',val);  
                this.$emit('changeTahunAkademik',val);          
            }            
        },
    }
}
</script>