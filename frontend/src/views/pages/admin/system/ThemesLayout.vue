<template>
		<SystemConfigLayout>
		<ModuleHeader>
						<template v-slot:icon>
								mdi-face-profile
						</template>
						<template v-slot:name>
								LAYOUT
						</template>
						<template v-slot:breadcrumbs>
								<v-breadcrumbs :items="breadcrumbs" class="pa-0">
										<template v-slot:divider>
												<v-icon>mdi-chevron-right</v-icon>
										</template>
								</v-breadcrumbs>
						</template>
						<template v-slot:desc>
								<v-alert                                        
										color="orange"
										border="left"                    
										colored-border
										type="info"
										>
										Mengatur css class untuk layout
										</v-alert>
						</template>
				</ModuleHeader> 
				<v-container fluid>  
						<v-row class="mb-4" no-gutters>
								<v-col cols="12">
										<v-form ref="frmdata" v-model="form_valid" lazy-validation>
												<v-card>
														<v-card-title>
																LAYOUT CSS CLASS
														</v-card-title>
														<v-card-text>
																<v-text-field 
																		v-model="formdata.V_SYSTEM_BAR_CSS_CLASS" 
																		label="CLASS CSS SYSTEM BAR"
																		outlined
																		:rules="rule_required">
																</v-text-field>                                                                    
																<v-text-field 
																		v-model="formdata.V_APP_BAR_NAV_ICON_CSS_CLASS" 
																		label="CLASS CSS APPLICATION BAR"
																		outlined
																		:rules="rule_required">
																</v-text-field>     
																<v-text-field 
																		v-model="formdata.V_NAVIGATION_DRAWER_CSS_CLASS" 
																		label="CLASS CSS NAVIGATION DRAWER"
																		outlined
																		:rules="rule_required">
																</v-text-field>     
																<v-text-field 
																		v-model="formdata.V_NAVIGATION_DRAWER_COLOR" 
																		label="COLOR CODE NAVIGATION DRAWER"
																		outlined
																		:rules="rule_required">
																</v-text-field>     
																<v-text-field 
																		v-model="formdata.V_LIST_ITEM_BOARD_CSS_CLASS" 
																		label="CLASS CSS LIST ITEM BOARD"
																		outlined
																		:rules="rule_required">
																</v-text-field>     
																<v-text-field 
																		v-model="formdata.V_LIST_ITEM_ACTIVE_CSS_CLASS" 
																		label="CLASS CSS LIST ITEM ACTIVE"
																		outlined
																		:rules="rule_required">
																</v-text-field>     
														</v-card-text>
														<v-card-actions>
																<v-spacer></v-spacer>     
																<v-btn 
																		color="blue darken-1" 
																		text 
																		@click.stop="save" 
																		:loading="btnLoading"
																		:disabled="!form_valid||btnLoading">SIMPAN</v-btn>
														</v-card-actions>
												</v-card>
										</v-form>
								</v-col>
						</v-row>
				</v-container>
		</SystemConfigLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import SystemConfigLayout from '@/views/layouts/SystemConfigLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
		name: 'ThemesLayout',
		created()
		{
				this.breadcrumbs = [
						{
								text:'HOME',
								disabled:false,
								href:'/dashboard/'+this.ACCESS_TOKEN
						},
						{
								text:'KONFIGURASI SISTEM',
								disabled:false,
								href:'/system-setting'
						},
						{
								text:'THEMES',
								disabled:false,
								href:'#'
						},
						{
								text:'LAYOUT',
								disabled:true,
								href:'#'
						}
				];
				this.initialize();
		},
		data: () => ({
				breadcrumbs: [],
				datatableLoading:false,
				btnLoading: false, 
				//form
				form_valid:true, 
				formdata: {
						V_SYSTEM_BAR_CSS_CLASS: "",
						V_APP_BAR_NAV_ICON_CSS_CLASS: "",
						V_NAVIGATION_DRAWER_CSS_CLASS: "",
						V_NAVIGATION_DRAWER_COLOR: "",
						V_LIST_ITEM_BOARD_CSS_CLASS: "",
						V_LIST_ITEM_ACTIVE_CSS_CLASS: "",
				},
				//form rules        
				rule_required: [
						value => !!value || "Mohon untuk diisi dengan nama class !!!",           
				], 
		}),
		methods: {
				initialize:async function() 
				{
						this.datatableLoading=true;
						await this.$ajax.get('/system/setting/variables',
						{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {  
								let setting = data.setting;               
								this.formdata.V_SYSTEM_BAR_CSS_CLASS=setting.V_SYSTEM_BAR_CSS_CLASS;
								this.formdata.V_APP_BAR_NAV_ICON_CSS_CLASS=setting.V_APP_BAR_NAV_ICON_CSS_CLASS;
								this.formdata.V_NAVIGATION_DRAWER_CSS_CLASS=setting.V_NAVIGATION_DRAWER_CSS_CLASS;
								this.formdata.V_NAVIGATION_DRAWER_COLOR=setting.V_NAVIGATION_DRAWER_COLOR;
								this.formdata.V_LIST_ITEM_BOARD_CSS_CLASS=setting.V_LIST_ITEM_BOARD_CSS_CLASS;
								this.formdata.V_LIST_ITEM_ACTIVE_CSS_CLASS=setting.V_LIST_ITEM_ACTIVE_CSS_CLASS;
						});          
						
				},
				save() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;    
								this.$ajax.post('/system/setting/variables',
										{
												'_method':'PUT', 
												'pid':'Class Css Layout',
												setting:JSON.stringify({
														801: this.formdata.V_SYSTEM_BAR_CSS_CLASS,
														802: this.formdata.V_APP_BAR_NAV_ICON_CSS_CLASS,
														803: this.formdata.V_NAVIGATION_DRAWER_CSS_CLASS,
														808: this.formdata.V_NAVIGATION_DRAWER_COLOR,
														804: this.formdata.V_LIST_ITEM_BOARD_CSS_CLASS,
														805: this.formdata.V_LIST_ITEM_BOARD_COLOR,
														806: this.formdata.V_LIST_ITEM_ACTIVE_CSS_CLASS,
												}),                                                                                                        
										},
										{
												headers: {
														Authorization: this.TOKEN
												}
										}
								).then(() => {                       
										this.btnLoading = false;
								}).catch(() => {
										this.btnLoading = false;
								});        
								this.$store.dispatch('uiadmin/init',this.$ajax); 
						}
				}
		},
		computed: { 
				...mapGetters('auth',{            
						ACCESS_TOKEN:'AccessToken',        
						TOKEN:'Token',              
				}),
		},
		components: {
		SystemConfigLayout,
				ModuleHeader,      
	}
}
</script>