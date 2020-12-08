<template>
    <div>
        <v-app-bar app>
            <v-toolbar-title>
                <span class="hidden-sm-and-down">{{namaPTAlias}}</span>
            </v-toolbar-title>
            <v-spacer />
            <v-btn to="/" class="mr-2" color="primary" text large>
                Home
            </v-btn>
            <v-btn to="/pmb" class="mr-2" color="primary" text large>
                PENDAFTARAN
            </v-btn>
            <v-btn to="/login" color="primary" text large>
                Login
            </v-btn>
        </v-app-bar>
        <v-main>
            <slot/>
        </v-main>        
    </div>
</template>
<script>
import { mapGetters } from 'vuex';

export default {
    name:'FrontLayout',
    created ()
    {
        this.$store.dispatch('uifront/init',this.$ajax);           
    },
    computed :{
        ...mapGetters('uifront',{
            namaPTAlias: 'getNamaPTAlias'
        })
    }
}
</script>