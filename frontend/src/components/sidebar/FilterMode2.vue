<template>
    <v-list-item>
        <v-list-item-content>            
            <v-select
                v-model="tahun_akademik"
                :items="daftar_ta"                
                label="TAHUN AKADEMIK"
                outlined/>            
            <v-select
                v-model="semester_akademik"
                :items="daftar_semester"
                item-text="text"
                item-value="id"
                label="SEMESTER"
                outlined/>            
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name:'FilterMode6',
    created()
    {
        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];  
        
        this.daftar_semester=this.$store.getters['uiadmin/getDaftarSemester'];  
        this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];  
    },
    data:()=>({
        firstloading:true,
        
        daftar_ta: [],
        tahun_akademik:null,

        daftar_semester: [],
        semester_akademik:null
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
        semester_akademik(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateSemesterAkademik',val);      
                this.$emit('changeSemesterAkademik',val);          
            }
        },
    }
}
</script>