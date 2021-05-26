<template>
		<div>
				<v-system-bar 
						color="#6699FF"
						app class="white--text">
						<strong>No. Peserta :</strong> {{this.peserta.no_peserta}}
						<v-divider
								class="mx-4"
								inset
								vertical>
						</v-divider>
						<strong>Mulai Ujian :</strong> {{$date(this.peserta.mulai_ujian).format("DD/MM/YYYY HH:mm")}}
						<v-divider
								class="mx-4"
								inset
								vertical>
						</v-divider>
				</v-system-bar>	

				<v-app-bar 
						color="#4285F4"
						app class="white--text" elevation="0">  
						<v-toolbar-title>
								Ujian Online
						</v-toolbar-title>
				</v-app-bar>


				<v-main>
						<v-container
						class="white lighten-5"
						>
								<v-row no-gutters>
								<v-col>
										<v-card
										class="pa-0 ma-1"
										tile
										>
										<v-app-bar
										color="#FFCA28"
										elevation="0"
										dense
										dark
										>

										<v-toolbar-title
										class="font-weight-bold black--text">
												Soal
										</v-toolbar-title>

										<v-spacer></v-spacer>

										</v-app-bar>

										<p class="font-weight-regular pa-4">
											{{nama_soal}}
										</p>

										<v-img
											v-if="gambar"
											max-height="50%"
											max-width="auto"
											:src="$api.url+'/'+gambar"
										></v-img>
										</v-card>
								</v-col>

								<v-col order="1">
										<v-card
										class="pa-0 ma-1"
										tile
										>
										<v-app-bar
										color="#FFCA28"
										elevation="0"
										dense
										dark
										>

										<v-toolbar-title
										class="font-weight-bold black--text">
												Pilih Jawaban
										</v-toolbar-title>

										<v-spacer></v-spacer>

										</v-app-bar>

										<p class="font-weight-regular pa-4">
												<v-card-text>
														<v-row
																justify="center"
																alignment="center">
																<v-col xs="12" sm="6" md="12" v-for="(item,index) in daftar_jawaban" v-bind:key="item.id">         
																		<JawabanSoal :index="index" :item="item" v-on:selesaiJawab="selesaiJawab" />
																</v-col> 
																<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
														</v-row>
														<v-row v-if="isprosesujian">
																<v-col cols="12">
																		<v-btn 
																				@click.stop="selesaiUjian"
																				color="error">
																				Selesai
																		</v-btn>
																</v-col>
														</v-row>
												</v-card-text>
										</p>
										</v-card> 
								</v-col>
								</v-row>
						</v-container>
				</v-main>
		</div>
</template>
<script>
	import JawabanSoal from "@/components/JawabanSoal";
	export default {
		name: "UjianOnline",
		created () 
		{           
				var page = this.$store.getters["uiadmin/Page"]("ujianonline");
				this.jadwal_ujian=page.data_ujian;
				this.peserta=page.data_peserta;        
				this.initialize();
		},
		data: () => ({
				jadwal_ujian:null,
				peserta:null,
				isprosesujian:false,

				nama_soal: "",
				gambar: null,
				daftar_jawaban: []
		}),
		methods: {
				initialize:async function() 
				{
						await this.$ajax.get("/spmb/ujianonline/soal/"+this.peserta.user_id,         
						{
								headers: {
										Authorization: this.$store.getters["auth/Token"]
								}
						}).then(({ data }) => {                       
								if (data.status==0)
								{
										this.isprosesujian=true;
										this.nama_soal="UJIAN SELESAI ?";
										this.daftar_jawaban=[];
								}
								else
								{
										this.nama_soal = data.soal.soal;     
										this.gambar = data.soal.gambar;     
										this.daftar_jawaban = data.jawaban;
								}
						}).catch(() => {
								
						}); 
				},
				selesaiJawab ()
				{
						this.initialize();
				},
				selesaiUjian:async function()
				{
						this.btnLoading = true;
						await this.$ajax.post("/spmb/ujianonline/selesaiujian",  
						{
								_method: "put",
								user_id: this.peserta.user_id
						},  
						{
								headers: {
										Authorization: this.$store.getters["auth/Token"]
								}
						}).then(() => {                       
							this.btnLoading = false;
							this.$router.push("/dashboard/"+this.$store.getters["auth/AccessToken"]);
						}).catch(() => {
								this.btnLoading = false;
						}); 
				}
		},  
		components: {
				JawabanSoal,      
		},
	};
</script>
