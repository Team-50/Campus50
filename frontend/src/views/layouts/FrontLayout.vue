<template>
    <div>
        <v-app-bar app class="white" elevation="0">            
            <v-toolbar-title>
                <span class="hidden-sm-and-down">
                    <v-img
                        max-width="80"
                        :src="$api.storageURL+'/storage/images/campus_50_logo.png'"
                        ></v-img>
                </span>
            </v-toolbar-title>
            <v-spacer />
            <v-btn to="/" class="mr-2" color="green darken-2" text large>
                Home
            </v-btn>
            <v-btn to="/pmb" class="mr-2" color="green darken-2" text large>
                PENDAFTARAN
            </v-btn>
            <v-btn to="/login" color="green darken-2" text large>
                Login
            </v-btn>
        </v-app-bar>
        <v-main>
            <slot/>
        </v-main>     

        <!-- <v-footer
            app
            fixed 
            dark
            padless
        >
            <v-card class="flex" flat tile>
                
                <v-card-text class="py-2 white--text text-center">
                    Team 50 — <strong>{{namaPTAlias}}</strong>
                </v-card-text>
            </v-card>
        </v-footer> -->

        <v-footer app fixed padless>
            <v-card flat tile class="flex">
                <v-card-text class="green darken-2 white--text text-center pa-5" v-if="$route.name!='FrontPMB'">
                    <v-btn
                    v-for="icon in icons"
                    :key="icon"
                    class="mx-8 white--text"
                    icon
                    >
                    <v-icon size="48px">
                        {{ icon }}
                    </v-icon>
                    </v-btn>
                </v-card-text>

                <!-- <v-card-text class="white--text pt-0">
                    Phasellus feugiat arcu sapien, et iaculis ipsum elementum sit amet. Mauris cursus commodo interdum. Praesent ut risus eget metus luctus accumsan id ultrices nunc. Sed at orci sed massa consectetur dignissim a sit amet dui. Duis commodo vitae velit et faucibus. Morbi vehicula lacinia malesuada. Nulla placerat augue vel ipsum ultrices, cursus iaculis dui sollicitudin. Vestibulum eu ipsum vel diam elementum tempor vel ut orci. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                </v-card-text> -->

                <v-card-text class="green darken-4 py-2 white--text text-center">
                    <v-btn
                    color="white"
                    text
                    href="https://github.com/Team-50/Campus50"
                    >
                    Team 50
                </v-btn>
                    — <strong>{{namaPTAlias}}</strong>
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
    },
    data: () => ({
      icons: [
        'mdi-github',
        'mdi-twitter',
        'mdi-linkedin',
        'mdi-instagram',
      ],
    }),
}
</script>