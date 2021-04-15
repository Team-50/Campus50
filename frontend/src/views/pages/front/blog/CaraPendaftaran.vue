<template>
	<FrontLayout>
		<v-container fluid>
			<v-row no-gutters>
				<v-col cols="12">
					<h2 class="title text-center">ALUR PROSES PENDAFTARAN DAN SELEKSI</h2>
					<v-divider />
				</v-col>
				<v-col cols="12">
					<v-timeline
						align-top
						:dense="$vuetify.breakpoint.smAndDown"
					>
					<v-timeline-item
					color="orange"
					fill-dot
					right
					>
					<v-card>
						<v-card-title class="orange">
						<v-icon
							dark
							size="30"
							class="mr-4"
						>
							mdi-account-plus
						</v-icon>
						<h3 class="white--text">
							Membuat Akun
						</h3>
						</v-card-title>
						<v-container>
						<v-row>
							<v-col
							cols="12"
							class="font-weight-light"
							style="font-size:14px"
							>
							Klik menu PENDAFTARAN maka muncul halaman pendaftaran, yang terdiri dari beberapa bagian yang wajib untuk diisi, yaitu :
							<ol type="a">
								<li>Nama Lengkap</li>
								<li>Nomor HP / WA dengan format seperti contoh +6281234567</li>
								<li>Email yang valid dan aktif</li>
								<li>Program studi yang di pilih Sistem Informasi atau Teknik Informatika</li>
								<li>Tentukan Username dan Password</li>
								<li>Ceklis Recaptcha / I'm Not Robot</li>                        
							</ol>
							Setelah tekan tombol "Daftar" maka akan muncul konfirmasi pendaftaran yang menyatakan proses pengisian form telah berhasil;
							Sistem akan mengirimkan kode aktivasi ke alamat Email, bila sudah tersedia kode tersebut tekan tombol "Konfirmasi".                                                                                                                                                       
							</v-col>
						</v-row>
						</v-container>
					</v-card>
					</v-timeline-item>
					<v-timeline-item
					color="green"
					fill-dot
					left
					small
					>
					<v-card>
						<v-card-title class="green justify-end">
						<h3 class="white--text mr-4 font-weight-light">
							Lengkapi Formulir Pendaftaran
						</h3>
						<v-icon
							dark
							size="30"
						>
							mdi-card-account-details
						</v-icon>
						</v-card-title>
						<v-container>
						<v-row>
							<v-col
							cols="12"
							class="font-weight-light"
							style="font-size:14px"
							>
							Klik tombol Login di menu utama, Setelah berhasil login ke dalam SIMAK, klik tombol "Isi Formulir" yang terdiri dari beberapa bagian yang wajib untuk diisi, yaitu :
									<ol>
										<li>Identitas Diri</li>
										<li>Alamat Domisili</li>
										<li>Rencana Studi dan Menentukan Kelas (Pagi, Sore, Karyawan)</li>                        
									</ol>
									Setelah tekan tombol "Simpan" maka lanjutkan langkah 3.
									Selanjutnya lakukan pembayaran dengan cara ditransfer ke :
									<v-alert type="tkinfo">
										BRI (BANK RAKYAT INDONESIA) <br />
										NOMOR REKENING : 2147.01.000 129 307 <br />
										A.N : SEKOLAH TINGGI TEKNOLOGI INDONESIA TANJUNGPINANG <br />
									</v-alert>
							</v-col>
						</v-row>
						</v-container>
					</v-card>
						</v-timeline-item>
					<v-timeline-item color="blue" fill-dot right>
					<v-card>
								<v-card-title class="blue">
									<v-icon dark size="30" class="mr-4">
										mdi-clipboard-check-multiple
									</v-icon>
									<h3 class="white--text">
										Konfirmasi Pembayaran
									</h3>
								</v-card-title>
								<v-container>
									<v-row>
										<v-col
											cols="12"
											class="font-weight-light"
											style="font-size:14px"
										>
											Klik menu KONFIRMASI PEMBAYARAN maka muncul halaman
											Konfirmasi Pembayaran
											<ol type="a">
												<li>Klik tombol konfirmasi</li>
												<li>
													Pilih data pembayaran yang tampil pada Daftar
													Transaksi
												</li>
												<li>Pilih Upload Pembayaran</li>
											</ol>
											belum selesai.
										</v-col>
									</v-row>
								</v-container>
							</v-card>
						</v-timeline-item>
					</v-timeline>
				</v-col>
			</v-row>
			<v-row align="center" justify="center" no-gutters>
				<v-col xs="12" sm="6" md="4">
					<v-card outlined>
						<v-card-title class="justify-center">
							<h3 class="title">INFORMASI PENDAFTARAN</h3>
						</v-card-title>
						<v-card-subtitle class="justify-center">
							<p>
								Silahkan hubungi kami, bila ada pertanyaan atau hal yang belum
								jelas di nomor kontak WA berikut.
							</p>
						</v-card-subtitle>
						<v-card-text class="justify-center">
							<div>
								<h3 class="headline pink--text text--accent-2">
									TIM MARKETING
								</h3>
								<div>
									<v-icon>mdi-card-account-phone-outline</v-icon>08117002638
								</div>
							</div>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
		</v-container>
	</FrontLayout>
</template>
<script>
	import FrontLayout from "@/views/layouts/FrontLayout";
	export default {
		name: "CaraPendaftaran",
		data: () => ({}),
		components: {
			FrontLayout,
		},
	};
</script>
