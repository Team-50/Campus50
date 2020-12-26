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
        </v-footer>

        <!-- <v-footer app padless fixed dark>
			<v-card class="flex" flat tile>
				<v-divider></v-divider>
				<v-card-text class="py-2 white--text text-center">
					<strong>{{NamaAPP}} (2019-2020)</strong> dikembangkan oleh TIM IT BAPELITBANG KAB. Bintan. 
					<v-btn dark icon href="https://github.com/mrizkir/simonev">
						<v-icon>mdi-github</v-icon>
					</v-btn>					
				</v-card-text>
			</v-card>			
		</v-footer> -->

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