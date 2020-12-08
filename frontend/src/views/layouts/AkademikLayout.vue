<template>
    <div>
        <v-system-bar app dark :class="this.$store.getters['uiadmin/getTheme']('V-SYSTEM-BAR-CSS-CLASS')">
            
		</v-system-bar>	
        <v-app-bar app>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" :class="this.$store.getters['uiadmin/getTheme']('V-APP-BAR-NAV-ICON-CSS-CLASS')"></v-app-bar-nav-icon>
            <v-toolbar-title class="headline clickable" @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken']).catch(err => {})">
				<span class="hidden-sm-and-down">{{APP_NAME}}</span>
			</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu 
                :close-on-content-click="true"
                origin="center center"
                transition="scale-transition"
                :offset-y="true"
                bottom 
                left>
                <template v-slot:activator="{on}">
                    <v-avatar size="30">
                        <v-img :src="photoUser" v-on="on" />
                    </v-avatar>                    
                </template>
                <v-list>
                    <v-list-item>
                        <v-list-item-avatar>
                            <v-img :src="photoUser"></v-img>
                        </v-list-item-avatar>
                        <v-list-item-content>					
                            <v-list-item-title class="title">
                                {{ATTRIBUTE_USER('username')}}
                            </v-list-item-title>
                            <v-list-item-subtitle>                                
                                {{ROLE}}
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>                    
                    <v-divider/>
                    <v-list-item to="/system-users/profil">
                        <v-list-item-icon class="mr-2">
							<v-icon>mdi-account</v-icon>
						</v-list-item-icon>
                        <v-list-item-title>Profil</v-list-item-title>
                    </v-list-item>
                    <v-divider/>
                    <v-list-item @click.prevent="logout">
                        <v-list-item-icon class="mr-2">
							<v-icon>mdi-power</v-icon>
						</v-list-item-icon>
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
            <v-divider
                class="mx-4"
                inset
                vertical
            ></v-divider>
			<v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight">
                <v-icon>mdi-menu-open</v-icon>
			</v-app-bar-nav-icon>            
        </v-app-bar>    
        <v-navigation-drawer v-model="drawer" width="300" dark :class="this.$store.getters['uiadmin/getTheme']('V-NAVIGATION-DRAWER-CSS-CLASS')" :temporary="temporaryleftsidebar" app>
			<v-list-item>
				<v-list-item-avatar>
					<v-img :src="photoUser" @click.stop="toProfile"></v-img>
				</v-list-item-avatar>
				<v-list-item-content>					
					<v-list-item-title class="title">
						{{ATTRIBUTE_USER('username')}}
					</v-list-item-title>
					<v-list-item-subtitle>
						{{ROLE}}
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
			<v-divider></v-divider>
            <v-list expand>
                <v-list-item :to="{path:'/akademik'}" link :class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')" :color="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-BOARD-COLOR')" v-if="CAN_ACCESS('AKADEMIK-GROUP')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-monitor-dashboard</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>BOARD AKADEMIK</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>                   
                <v-list-item link to="/akademik/dosenwali" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" v-if="CAN_ACCESS('SYSTEM-USERS-DOSEN-WALI_BROWSE')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-teach</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            DOSEN WALI
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/akademik/matakuliah" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" v-if="CAN_ACCESS('AKADEMIK-MATAKULIAH_BROWSE')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-book</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            MATAKULIAH
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-subheader v-if="CAN_ACCESS('AKADEMIK-DULANG-BARU_BROWSE')||CAN_ACCESS('AKADEMIK-DULANG-LAMA_BROWSE')">DAFTAR ULANG</v-subheader>
                <v-list-item link to="/akademik/dulang/mhsbelumpunyanim" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" v-if="CAN_ACCESS('AKADEMIK-DULANG-BARU_BROWSE')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-book</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            BELUM PUNYA NIM
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/akademik/dulang/mahasiswabaru" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" v-if="CAN_ACCESS('AKADEMIK-DULANG-BARU_BROWSE')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-book</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            MAHASISWA BARU
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>                
                <v-list-item link to="/akademik/dulang/mahasiswalama" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" v-if="CAN_ACCESS('AKADEMIK-DULANG-LAMA_BROWSE')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-book</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            MAHASISWA LAMA
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>                
                <v-subheader>KEMAHASISWAAN</v-subheader>
                <v-list-item link to="/akademik/kemahasiswaan/daftarmahasiswa" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" v-if="CAN_ACCESS('AKADEMIK-KEMAHASISWAAN-DAFTAR-MAHASISWA_BROWSE')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-book</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            DAFTAR MAHASISWA
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>              
                <v-subheader>PERKULIAHAN</v-subheader>                         
                <v-list-group group="/akademik/perkuliahan/penyelenggaraan" active-class="yellow" no-action v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_BROWSE')" color="green">
                    <template v-slot:activator>
                        <v-list-item-icon class="mr-2">
                            <v-icon>mdi-home-floor-b</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>								
                            <v-list-item-title>PENYELENGGARAAN</v-list-item-title>
                        </v-list-item-content>							
                    </template>
					<div>
						<v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_BROWSE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" to="/akademik/perkuliahan/penyelenggaraan/daftar" color="white">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    DAFTAR
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 
						<v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_STORE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" disabled to="/akademik/perkuliahan/penyelenggaraan/tambah">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    TAMBAH MATKUL
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 
						<v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_STORE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" disabled :to="{path:'/akademik/perkuliahan/penyelenggaraan/'+paramid+'/dosenpengampu'}">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    DOSEN PENGAMPU
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 
					</div>
                </v-list-group>                
                <v-list-group group="/akademik/perkuliahan/krs" active-class="yellow" no-action v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_BROWSE')" color="green">
                    <template v-slot:activator>
                        <v-list-item-icon class="mr-2">
                            <v-icon>mdi-format-columns</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>								
                            <v-list-item-title>KRS</v-list-item-title>
                        </v-list-item-content>							
                    </template>
					<div>
						<v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_BROWSE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" to="/akademik/perkuliahan/krs/daftar" color="white">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    DAFTAR
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   				
                        <v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_STORE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" disabled to="/akademik/perkuliahan/krs/tambah">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    TAMBAH KRS
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 					 
						<v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_SHOW')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" disabled :to="{path:'/akademik/perkuliahan/krs/'+paramid+'/detail'}">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    DETAIL KRS
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 												
                        <v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_STORE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" disabled :to="{path:'/akademik/perkuliahan/krs/'+paramid+'/tambahmatkul'}">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    TAMBAH MATKUL
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 									
					</div>
                </v-list-group>
                <v-list-group group="/akademik/perkuliahan/pembagiankelas" active-class="yellow" no-action v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_BROWSE')" color="green">
                    <template v-slot:activator>
                        <v-list-item-icon class="mr-2">
                            <v-icon>mdi-google-classroom</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>								
                            <v-list-item-title>PEMBAGIAN KELAS</v-list-item-title>
                        </v-list-item-content>							
                    </template>
					<div>
                        <v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_BROWSE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" to="/akademik/perkuliahan/pembagiankelas/daftar" color="white">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    DAFTAR
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   	
                        <v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_STORE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" disabled to="/akademik/perkuliahan/pembagiankelas/tambah">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    TAMBAH KELAS
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 
                        <v-list-item link v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_STORE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" disabled :to="{path:'/akademik/perkuliahan/pembagiankelas/'+paramid+'/peserta'}">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    PESERTA
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 
					</div>
                </v-list-group>
                <v-subheader>NILAI</v-subheader> 
                <v-list-group group="/akademik/nilai/matakuliah" active-class="yellow" no-action v-if="CAN_ACCESS('AKADEMIK-NILAI-MATAKULIAH_BROWSE') && dashboard=='puslahta'" color="green">
                    <template v-slot:activator>
                        <v-list-item-icon class="mr-2">
                            <v-icon>mdi-format-columns</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>								
                            <v-list-item-title>ISI NILAI</v-list-item-title>
                        </v-list-item-content>							
                    </template>
					<div>
						<v-list-item link v-if="CAN_ACCESS('AKADEMIK-NILAI-MATAKULIAH_STORE') && dashboard=='puslahta'" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" to="/akademik/nilai/matakuliah/isiperkelasmhs" color="white">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    PER KELAS MHS
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 						
						<v-list-item link v-if="CAN_ACCESS('AKADEMIK-NILAI-MATAKULIAH_STORE') && dashboard=='puslahta'" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" to="/akademik/nilai/matakuliah/isiperkrs" color="white">
                            <v-list-item-icon class="mr-2">
                                <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    PER KRS
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>   						 						
					</div>                    
                </v-list-group>                        
                <v-list-item link v-if="CAN_ACCESS('AKADEMIK-NILAI-KHS_BROWSE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" :to="{path:'/akademik/nilai/khs'}">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            KHS
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>   
                <v-list-item link v-if="CAN_ACCESS('AKADEMIK-NILAI-TRANSKRIP-KURIKULUM_BROWSE')" :active-class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')" :to="{path:'/akademik/nilai/transkripkurikulum'}">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            TRANSKRIP KURIKULUM
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>   
            </v-list>
        </v-navigation-drawer>
        <v-navigation-drawer v-model="drawerRight" width="300" app fixed right temporary v-if="showrightsidebar">
            <v-list dense>
                <v-list-item>		
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-menu-open</v-icon>
                    </v-list-item-icon>			
                    <v-list-item-content>									
                        <v-list-item-title class="title">
                            OPTIONS
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item :class="this.$store.getters['uiadmin/getTheme']('V-LIST-ITEM-ACTIVE-CSS-CLASS')">
                    <v-list-item-icon class="mr-2">
                        <v-icon>mdi-filter</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>								
                        <v-list-item-title>FILTER</v-list-item-title>
                    </v-list-item-content>		
                </v-list-item>
                <slot name="filtersidebar"/>		                	
            </v-list>
		</v-navigation-drawer>
        <v-main class="mx-4 mb-4">			
			<slot />
		</v-main>
    </div>    
</template>
<script>
import {mapGetters} from 'vuex';
export default {
    name:'AkademikLayout',     
    created()
    {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];          
    },
    props:{
        showrightsidebar:{
            type:Boolean,
            default:true
        },
        temporaryleftsidebar:{
            type:Boolean,
            default:false
        },
    },
    data:()=>({
        loginTime:0,
        drawer:null,
        drawerRight:null, 
        
        dashboard:null,
    }),       
    methods: {        
        logout ()
        {
            this.loginTime=0;
            this.$ajax.post('/auth/logout',
                {},
                {
                    headers:{
                        'Authorization': this.TOKEN,
                    }
                }
            ).then(()=> {     
                this.$store.dispatch('auth/logout');	
                this.$store.dispatch('uifront/reinit');	
                this.$store.dispatch('uiadmin/reinit');	
                this.$router.push('/');
            })
            .catch(() => {
                this.$store.dispatch('auth/logout');	
                this.$store.dispatch('uifront/reinit');	
                this.$store.dispatch('uiadmin/reinit');	
                this.$router.push('/');
            });
        },
        isBentukPT (bentuk_pt)
        {
            return this.$store.getters['uifront/getBentukPT']==bentuk_pt?true:false;
        }
	},
    computed:{
        ...mapGetters('auth',{
            AUTHENTICATED:'Authenticated',  
            ACCESS_TOKEN:'AccessToken',          
            TOKEN:'Token',          
            ROLE:'Role',
            CAN_ACCESS:'can',         
            ATTRIBUTE_USER:'AttributeUser',               
        }),
        APP_NAME ()
        {
            return process.env.VUE_APP_NAME;
        },
        photoUser()
		{
			let img=this.ATTRIBUTE_USER('foto');
			var photo;
			if (img == '')
			{
				photo = this.$api.url+'/storage/images/users/no_photo.png';	
			}
			else
			{
				photo = this.$api.url+'/'+img;	
			}
			return photo;
        },        
        paramid ()
        {
            var id='empty';                        
            switch (this.$route.name)
            {
                case 'PerkuliahanPenyelenggaraanDosenPengampu':
                    id=this.$route.params.idpenyelenggaraan;
                break;
                case 'PerkuliahanKRSDetail':
                    id=this.$route.params.krsid;
                break;
                case 'PerkuliahanKRSTambahMatkul':
                    id=this.$route.params.krsid;
                break;
                case 'PerkuliahanPembagianKelasPeserta':
                    id=this.$route.params.kelas_mhs_id;
                break;
            }            
            return id;
        }
    },
    watch: {
        loginTime:{
            handler(value)
            {
                
                if (value >= 0)
                {
                    setTimeout(() => { 
                        this.loginTime=this.AUTHENTICATED==true?this.loginTime+1:-1;                                                                     
					}, 1000);
                }
                else
                {
                    this.$store.dispatch('auth/logout');
                    this.$router.replace('/login');
                }
            },
            immediate:true
        },        
    }
}
</script>