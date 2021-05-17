<template>
	<KemahasiswaanLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-monitor-dashboard
			</template>
			<template v-slot:name>
				KEMAHASISWAAN
			</template>
			<template v-slot:subtitle>
				TAHUN KEMAHASISWAAN {{ tahun_akademik }}
			</template>
			<template v-slot:breadcrumbs>
				<v-breadcrumbs :items="breadcrumbs" class="pa-0">
					<template v-slot:divider>
						<v-icon>mdi-chevron-right</v-icon>
					</template>
				</v-breadcrumbs>
			</template>
			<template v-slot:desc>
				<v-alert color="cyan" border="left" colored-border type="info">
					dashboard untuk memperoleh ringkasan informasi kemahasiswaan perguruan
					tinggi.
				</v-alert>
			</template>
		</ModuleHeader>
		<template v-slot:filtersidebar>
			<Filter1 v-on:changeTahunAkademik="changeTahunAkademik" ref="filter1" />
		</template>
		<v-container fluid>
			<v-row>
				<v-col cols="12">
					<v-card outlined>
						<v-list-item three-line>
							<v-list-item-content>
								<div class="overline mb-1">
									PROFIL MAHASISWA (MASUKAN NIM)
								</div>
								<v-list-item-subtitle>
									<v-autocomplete
										v-model="data_mhs"
										:items="entries"
										:loading="isLoading"
										:search-input.sync="search"
										cache-items
										dense
										item-text="nama_mhs_alias"
										item-value="user_id"
										hide-no-data
										hide-details
										prepend-icon="mdi-database-search"
										return-object
										ref="ref_data_mhs"
									></v-autocomplete>
								</v-list-item-subtitle>
							</v-list-item-content>
							<v-list-item-avatar tile size="80" color="grey">
								<v-icon>mdi-account</v-icon>
							</v-list-item-avatar>
						</v-list-item>
						<v-divider></v-divider>
						<v-expand-transition>
							<v-list v-if="data_mhs">
								<template v-for="(field, i) in fields">
									<v-list-item
										:key="i"
										v-if="field.key != 'foto' && field.key != 'nama_mhs_alias'"
									>
										<v-list-item-content>
											<v-list-item-title>
												{{ field.value }}
											</v-list-item-title>
											<v-list-item-subtitle>
												<strong>{{ field_alias(field.key) }}</strong>
											</v-list-item-subtitle>
										</v-list-item-content>
									</v-list-item>
								</template>
							</v-list>
						</v-expand-transition>
						<v-card-actions>
							<v-spacer></v-spacer>
							<v-btn :disabled="!data_mhs" @click="goProfilMhs">
								Detail
								<v-icon right>
									mdi-forward
								</v-icon>
							</v-btn>
							<v-btn :disabled="!data_mhs" @click="clearDataMhs">
								Clear
								<v-icon right>
									mdi-close-circle
								</v-icon>
							</v-btn>
						</v-card-actions>
					</v-card>
				</v-col>
			</v-row>
		</v-container>
	</KemahasiswaanLayout>
</template>
<script>
	import KemahasiswaanLayout from "@/views/layouts/KemahasiswaanLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	import Filter1 from "@/components/sidebar/FilterMode1";
	export default {
		name: "Kemahasiswaan",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "KEMAHASISWAAN",
					disabled: true,
					href: "#",
				},
			];
			this.tahun_akademik = this.$store.getters["uiadmin/getTahunAkademik"];
		},
		mounted() {
			this.initialize();
		},
		data: () => ({
			firstloading: true,
			breadcrumbs: [],
			tahun_akademik: 0,
			//profil mahasiswa
			entries: [],
			isLoading: false,
			data_mhs: null,
			search: null,
		}),
		methods: {
			changeTahunAkademik(tahun) {
				this.tahun_akademik = tahun;
			},
			initialize: async function() {
				this.firstloading = false;
				this.$refs.filter1.setFirstTimeLoading(this.firstloading);
			},
			field_alias(atr) {
				var alias;
				switch (atr) {
					case "user_id":
						alias = "USER ID";
						break;
					case "nim":
						alias = "NIM";
						break;
					case "nama_mhs":
						alias = "NAMA MAHASIWA";
						break;
					case "nama_prodi":
						alias = "PROGRAM STUDI";
						break;
				}
				return alias;
			},
			goProfilMhs() {
				this.$router.push("/kemahasiswaan/profil/" + this.data_mhs.user_id);
			},
			clearDataMhs() {
				this.data_mhs = null;
				this.$refs.ref_data_mhs.cachedItems = [];
			},
		},
		computed: {
			fields() {
				if (!this.data_mhs) return [];
				return Object.keys(this.data_mhs).map(key => {
					return {
						key,
						value: this.data_mhs[key] || "n/a",
					};
				});
			},
		},
		watch: {
			tahun_akademik() {
				if (!this.firstloading) {
					this.initialize();
				}
			},
			search(val) {
				if (this.isLoading) return;
				if (val && val !== this.data_mhs && val.length > 1) {
					setTimeout(async () => {
						this.isLoading = true;
						await this.$ajax
							.post(
								"/kemahasiswaan/profil/search",
								{
									search: val,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(({ data }) => {
								const { jumlah, daftar_mhs } = data;
								this.count = jumlah;
								this.entries = daftar_mhs;
								this.isLoading = false;
							})
							.catch(() => {
								this.isLoading = false;
							});
					}, 1000);
				}
			},
		},
		components: {
			KemahasiswaanLayout,
			ModuleHeader,
			Filter1,
		},
	};
</script>
