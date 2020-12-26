<template>
    <div>
        <v-app-bar app>
            <v-toolbar-title>
                <span class="hidden-sm-and-down">
                    <v-img
                        max-width="80"
                        :src="$api.url+'/storage/images/campus_50_logo.png'"
                        ></v-img>
                </span>
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

        <v-footer
            dark
            padless
        >
            <v-card
            class="flex"
            flat
            tile
            >
            <v-card-title class="teal">
                <strong class="subheading">Get connected with us on social networks!</strong>

                <v-spacer></v-spacer>
            </v-card-title>

            <v-card-text class="py-2 white--text text-center">
                {{ new Date().getFullYear() }} — <strong>{{namaPTAlias}}</strong>
            </v-card-text>
            </v-card>
        </v-footer>

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