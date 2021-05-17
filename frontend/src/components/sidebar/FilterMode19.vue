<template>
    <v-list-item>
        <v-list-item-content>                     
            <v-select
                v-model="tahun_pendaftaran"
                :items="daftar_ta"                
                label="TAHUN PENDAFTARAN"
                outlined/>   
            <v-select
                v-model="semester_pendaftaran"
                :items="daftar_semester"                
                item-text="text"
                item-value="id"
                label="SEMESTER PENDAFTARAN"
                outlined/>   
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name:'FilterMode19',
    created()
    {
        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];  

        this.daftar_semester=this.$store.getters['uiadmin/getDaftarSemester'];  
        this.semester_pendaftaran=this.$store.getters['uiadmin/getSemesterPendaftaran'];                                
    },
    data:()=>({
        firstloading:true,
        
        daftar_semester: [],
        semester_pendaftaran:null,

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
        semester_pendaftaran(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateSemesterPendaftaran',val);  
                this.$emit('changeSemesterPendaftaran',val);          
            }
        },
    }
}
</script>