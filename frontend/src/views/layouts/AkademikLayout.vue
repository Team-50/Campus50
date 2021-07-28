<template>
	<div>
		<v-app-bar app elevation="0" color="#f7f8fd">
			<v-app-bar-nav-icon
				@click.stop="drawer = !drawer"
				:class="
					this.$store.getters['uiadmin/getTheme'](
						'V_APP_BAR_NAV_ICON_CSS_CLASS'
					)
				"
			>
			</v-app-bar-nav-icon>
			<v-toolbar-title
				class="headline clickable"
				@click.stop="
					$router
						.push('/dashboard/' + $store.getters['auth/AccessToken'])
						.catch(err => {})
				"
			>
				<span class="headline font-weight-bold mx-1">{{ APP_NAME }}</span>
			</v-toolbar-title>
			<v-spacer></v-spacer>
			<v-menu
				:close-on-content-click="true"
				origin="center center"
				transition="scale-transition"
				:offset-y="true"
				bottom
				left
			>
				<template v-slot:activator="{ on }">
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
								{{ ATTRIBUTE_USER("username") }}
							</v-list-item-title>
							<v-list-item-subtitle>
								[{{ DEFAULT_ROLE }}]
							</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
					<v-divider />
					<v-list-item to="/system-users/profil">
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-account</v-icon>
						</v-list-item-icon>
						<v-list-item-title>Profil</v-list-item-title>
					</v-list-item>
					<v-divider />
					<v-list-item @click.prevent="logout">
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-power</v-icon>
						</v-list-item-icon>
						<v-list-item-title>Logout</v-list-item-title>
					</v-list-item>
				</v-list>
			</v-menu>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight">
				<v-icon>mdi-menu-open</v-icon>
			</v-app-bar-nav-icon>
		</v-app-bar>

		<v-navigation-drawer
			v-model="drawer"
			width="300"
			dark
			:class="
				this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_CSS_CLASS')
			"
			:color="
				this.$store.getters['uiadmin/getTheme']('V_NAVIGATION_DRAWER_COLOR')
			"
			:temporary="temporaryleftsidebar"
			app
		>
			<v-list-item>
				<v-list-item-avatar>
					<v-img :src="photoUser" @click.stop="toProfile"></v-img>
				</v-list-item-avatar>
				<v-list-item-content>
					<v-list-item-title class="title">
						<span
							class="headline font-weight-bold mx-1"
							style="color:#FFFFFF"
							dark
						>
							{{ ATTRIBUTE_USER("username") }}
						</span>
					</v-list-item-title>
					<v-list-item-subtitle>
						<span style="color:#FFFFFF" dark>[{{ DEFAULT_ROLE }}]</span>
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
			<v-divider></v-divider>
			<v-list expand dense rounded>
				<v-list-item
					:to="{ path: '/akademik' }"
					link
					:class="
						this.$store.getters['uiadmin/getTheme'](
							'V_LIST_ITEM_BOARD_CSS_CLASS'
						)
					"
					:color="
						this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')
					"
					v-if="CAN_ACCESS('AKADEMIK-GROUP')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-monitor-dashboard</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>BOARD AKADEMIK</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dosenwali"
					v-if="CAN_ACCESS('SYSTEM-USERS-DOSEN-WALI_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-teach</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							DOSEN WALI
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-subheader
					style="color:#f0935c"
					v-if="
						CAN_ACCESS('AKADEMIK-DULANG-BARU_BROWSE') ||
							CAN_ACCESS('AKADEMIK-DULANG-LAMA_BROWSE')
					"
				>
					DAFTAR ULANG
				</v-subheader>
				<v-list-item
					link
					to="/akademik/dulang/mhsbelumpunyanim"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-BARU_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-question</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							BELUM PUNYA NIM
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswabaru"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-BARU_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-star</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA BARU
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswalama"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-LAMA_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-supervisor</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA LAMA
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswaaktif"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V-LIST-ITEM-ACTIVE-CSS-CLASS'
						)
					"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-AKTIF_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-box-multiple</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA AKTIF
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswanonaktif"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V-LIST-ITEM-ACTIVE-CSS-CLASS'
						)
					"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-NON-AKTIF_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-box-multiple</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA NON-AKTIF
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswacuti"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V-LIST-ITEM-ACTIVE-CSS-CLASS'
						)
					"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-CUTI_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-box-multiple</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA CUTI
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswalulus"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V-LIST-ITEM-ACTIVE-CSS-CLASS'
						)
					"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-LULUS_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-box-multiple</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA LULUS
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswakeluar"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V-LIST-ITEM-ACTIVE-CSS-CLASS'
						)
					"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-KELUAR_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-box-multiple</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA KELUAR
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					to="/akademik/dulang/mahasiswado"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V-LIST-ITEM-ACTIVE-CSS-CLASS'
						)
					"
					v-if="CAN_ACCESS('AKADEMIK-DULANG-DO_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-box-multiple</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MAHASISWA DROPOUT / PUTUS
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-subheader style="color:#f0935c">PERKULIAHAN</v-subheader>
				<v-list-item
					link
					to="/akademik/matakuliah"
					v-if="CAN_ACCESS('AKADEMIK-MATAKULIAH_BROWSE')"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-cast-education</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							MATAKULIAH
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-group
					group="/akademik/perkuliahan/penyelenggaraan"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V_LIST_ITEM_BOARD_CSS_CLASS'
						)
					"
					no-action
					v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_BROWSE')"
					:color="
						this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')
					"
				>
					<template v-slot:activator>
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-chart-timeline</v-icon>
						</v-list-item-icon>
						<v-list-item-content>
							<v-list-item-title>PENYELENGGARAAN</v-list-item-title>
						</v-list-item-content>
					</template>
					<div>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_BROWSE')"
							to="/akademik/perkuliahan/penyelenggaraan/daftar"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									DAFTAR
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							\v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_STORE')"
							disabled
							to="/akademik/perkuliahan/penyelenggaraan/tambah"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									TAMBAH MATKUL
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_STORE')"
							disabled
							:to="{
								path:
									'/akademik/perkuliahan/penyelenggaraan/' +
									paramid +
									'/dosenpengampu',
							}"
						>
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
				<v-list-group
					group="/akademik/perkuliahan/krs"
					no-action
					v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_BROWSE')"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V_LIST_ITEM_BOARD_CSS_CLASS'
						)
					"
					:color="
						this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')
					"
				>
					<template v-slot:activator>
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-view-grid-plus</v-icon>
						</v-list-item-icon>
						<v-list-item-content>
							<v-list-item-title>KRS</v-list-item-title>
						</v-list-item-content>
					</template>
					<div>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_BROWSE')"
							to="/akademik/perkuliahan/krs/waktupengisiankrs"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									WAKTU PENGISIAN KRS
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_BROWSE')"
							to="/akademik/perkuliahan/krs/daftar"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									DAFTAR
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_STORE')"
							disabled
							to="/akademik/perkuliahan/krs/tambah"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									TAMBAH KRS
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_SHOW')"
							disabled
							:to="{ path: '/akademik/perkuliahan/krs/' + paramid + '/detail' }"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									DETAIL KRS
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-KRS_STORE')"
							disabled
							:to="{
								path: '/akademik/perkuliahan/krs/' + paramid + '/tambahmatkul',
							}"
						>
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
				<v-list-group
					group="/akademik/perkuliahan/pembagiankelas"
					no-action
					v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_BROWSE')"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V_LIST_ITEM_BOARD_CSS_CLASS'
						)
					"
					:color="
						this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')
					"
				>
					<template v-slot:activator>
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-google-classroom</v-icon>
						</v-list-item-icon>
						<v-list-item-content>
							<v-list-item-title>PEMBAGIAN KELAS</v-list-item-title>
						</v-list-item-content>
					</template>
					<div>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_BROWSE')"
							to="/akademik/perkuliahan/pembagiankelas/daftar"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									DAFTAR
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_STORE')"
							disabled
							to="/akademik/perkuliahan/pembagiankelas/tambah"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									TAMBAH KELAS
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMBAGIAN-KELAS_STORE')"
							disabled
							:to="{
								path:
									'/akademik/perkuliahan/pembagiankelas/' +
									paramid +
									'/peserta',
							}"
						>
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
				<v-subheader style="color:#f0935c">NILAI</v-subheader>
				<v-list-group
					group="/akademik/nilai/matakuliah"
					no-action
					v-if="
						CAN_ACCESS('AKADEMIK-NILAI-MATAKULIAH_BROWSE') &&
							dashboard == 'puslahta'
					"
					:active-class="
						this.$store.getters['uiadmin/getTheme'](
							'V_LIST_ITEM_BOARD_CSS_CLASS'
						)
					"
					:color="
						this.$store.getters['uiadmin/getTheme']('V_LIST_ITEM_BOARD_COLOR')
					"
				>
					<template v-slot:activator>
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-format-columns</v-icon>
						</v-list-item-icon>
						<v-list-item-content>
							<v-list-item-title>ISI NILAI</v-list-item-title>
						</v-list-item-content>
					</template>
					<div>
						<v-list-item
							link
							v-if="
								CAN_ACCESS('AKADEMIK-NILAI-MATAKULIAH_STORE') &&
									dashboard == 'puslahta'
							"
							to="/akademik/nilai/matakuliah/isiperkelasmhs"
						>
							<v-list-item-icon class="mr-2">
								<v-icon>mdi-arrow-right-bold-hexagon-outline</v-icon>
							</v-list-item-icon>
							<v-list-item-content>
								<v-list-item-title>
									PER KELAS MHS
								</v-list-item-title>
							</v-list-item-content>
						</v-list-item>
						<v-list-item
							link
							v-if="
								CAN_ACCESS('AKADEMIK-NILAI-MATAKULIAH_STORE') &&
									dashboard == 'puslahta'
							"
							to="/akademik/nilai/matakuliah/isiperkrs"
						>
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
				<v-list-item
					link
					v-if="CAN_ACCESS('AKADEMIK-NILAI-KHS_BROWSE')"
					:to="{ path: '/akademik/nilai/khs' }"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-view-quilt</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							KHS
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item
					link
					v-if="CAN_ACCESS('AKADEMIK-NILAI-TRANSKRIP-KURIKULUM_BROWSE')"
					:to="{ path: '/akademik/nilai/transkripkurikulum' }"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-format-columns</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							TRANSKRIP KURIKULUM
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
			</v-list>
		</v-navigation-drawer>
		<v-navigation-drawer
			v-model="drawerRight"
			width="300"
			app
			fixed
			right
			temporary
			v-if="showrightsidebar"
		>
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
				<v-list-item class="teal lighten-5 mb-5">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-filter</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>FILTER</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<slot name="filtersidebar" />
			</v-list>
		</v-navigation-drawer>
		<v-main class="mx-4 mb-4">
			<slot />
		</v-main>
	</div>
</template>
<script>
	import { mapGetters } from "vuex";
	export default {
		name: "AkademikLayout",
		created() {
			this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
		},
		props: {
			showrightsidebar: {
				type: Boolean,
				default: true,
			},
			temporaryleftsidebar: {
				type: Boolean,
				default: false,
			},
		},
		data: () => ({
			loginTime: 0,
			drawer: null,
			drawerRight: null,
			dashboard: null,
		}),
		methods: {
			logout() {
				this.loginTime = 0;
				this.$ajax
					.post(
						"/auth/logout",
						{},
						{
							headers: {
								Authorization: this.TOKEN,
							},
						}
					)
					.then(() => {
						this.$store.dispatch("auth/logout");
						this.$store.dispatch("uifront/reinit");
						this.$store.dispatch("uiadmin/reinit");
						this.$router.push("/");
					})
					.catch(() => {
						this.$store.dispatch("auth/logout");
						this.$store.dispatch("uifront/reinit");
						this.$store.dispatch("uiadmin/reinit");
						this.$router.push("/");
					});
			},
			isBentukPT(bentuk_pt) {
				return this.$store.getters["uifront/getBentukPT"] == bentuk_pt
					? true
					: false;
			},
		},
		computed: {
			...mapGetters("auth", {
				AUTHENTICATED: "Authenticated",
				ACCESS_TOKEN: "AccessToken",
				TOKEN: "Token",
				DEFAULT_ROLE: "DefaultRole",
				ROLE: "Role",
				CAN_ACCESS: "can",
				ATTRIBUTE_USER: "AttributeUser",
			}),
			APP_NAME() {
				return process.env.VUE_APP_NAME;
			},
			photoUser() {
				let img = this.ATTRIBUTE_USER("foto");
				var photo;
				if (img == "") {
					photo = this.$api.storageURL + "/storage/images/users/no_photo.png";
				} else {
					photo = this.$api.storageURL + "/" + img;
				}
				return photo;
			},
			paramid() {
				var id = "empty";
				switch (this.$route.name) {
					case "PerkuliahanPenyelenggaraanDosenPengampu":
						id = this.$route.params.idpenyelenggaraan;
						break;
					case "PerkuliahanKRSDetail":
						id = this.$route.params.krsid;
						break;
					case "PerkuliahanKRSTambahMatkul":
						id = this.$route.params.krsid;
						break;
					case "PerkuliahanPembagianKelasPeserta":
						id = this.$route.params.kelas_mhs_id;
						break;
				}
				return id;
			},
		},
		watch: {
			loginTime: {
				handler(value) {
					if (value >= 0) {
						setTimeout(() => {
							this.loginTime =
								this.AUTHENTICATED == true ? this.loginTime + 1 : -1;
						}, 1000);
					} else {
						this.$store.dispatch("auth/logout");
						this.$router.replace("/login");
					}
				},
				immediate: true,
			},
		},
	};
</script>
