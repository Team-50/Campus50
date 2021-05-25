<template>
		<div>
				<v-app-bar app class="white" elevation="0">            
						<v-toolbar-title>
								<span>
									<v-img
											max-width="400"
											:src="$api.storageURL+'/storage/images/applogo.png'"
											>
										</v-img>
								</span>
						</v-toolbar-title>
						<v-spacer />
						<v-toolbar-items class="hidden-sm-and-down">
							<v-btn to="/" class="mr-2" color="indigo darken-4" text large>
									Home
							</v-btn>
							<v-btn to="/blog/cara-pendaftaran" class="mr-2" color="indigo darken-4" text large>
									PETUNJUK PENDAFTARAN
							</v-btn>
							<v-btn to="/pmb" class="mr-2" color="indigo darken-4" text large>
									PENDAFTARAN
							</v-btn>
							<v-btn to="/login" color="indigo darken-4" text large>
									Login
							</v-btn>
						</v-toolbar-items>
						<v-menu class="hidden-lg-and-up" v-if="$vuetify.breakpoint.xsOnly">
							<template v-slot:activator="{ on }">
								<v-btn icon v-on="on">
									<v-icon>mdi-dots-vertical</v-icon>
								</v-btn>
							</template>
							<v-list>
								<v-list-item to="/">
									<v-list-item-title>HOME</v-list-item-title>
								</v-list-item>
								<v-list-item to="/blog/cara-pendaftaran">
									<v-list-item-title>PETUNJUK PENDAFTARAN</v-list-item-title>
								</v-list-item>
								<v-list-item to="/pmb">
									<v-list-item-title>PENDAFTARAN</v-list-item-title>
								</v-list-item>
								<v-list-item to="/login">
									<v-list-item-title>LOGIN</v-list-item-title>
								</v-list-item>
							</v-list>
						</v-menu>
				</v-app-bar>
				<v-main>
						<slot/>
				</v-main>    
				<v-footer app absolute padless class="mt-0">
						<v-card flat tile class="flex">
								<v-card-text class="indigo darken-3 white--text text-center pa-5 hidden-sm-and-down" v-if="$route.name!='FrontPMB'">
										<v-btn
										v-for="icon in icons"
										:key="icon"
										class="mx-8 white--text"
										icon
										>
										<v-icon size="45px">
												{{ icon }}
										</v-icon>
										</v-btn>
								</v-card-text>
								<v-card-text class="indigo darken-3 white--text text-center pa-2 hidden-lg-and-up" v-if="$route.name!='FrontPMB'">
										<v-btn
										v-for="icon in icons"
										:key="icon"
										class="mx-4 white--text"
										icon
										>
										<v-icon size="30px">
												{{ icon }}
										</v-icon>
										</v-btn>
								</v-card-text>
								<v-card-text class="indigo darken-4 py-2 white--text text-center">
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
		computed : {
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