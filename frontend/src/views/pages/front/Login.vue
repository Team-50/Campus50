<template>
	<FrontLayout>
		<v-container class="white pa-0" fluid>
			<v-row no-gutters align="center">
				<v-col class="hidden-sm-and-down" sm="12" md="8" align="center">
					<v-card class="pa-0 rounded-0" elevation="0" color="white">
						<v-img
							max-width="100%"
							max-height="auto"
							:src="$api.url + '/storage/images/gambar_login.jpg'"
						>
						</v-img>
					</v-card>
				</v-col>
				<v-col sm="12" md="4">
					<v-card class="pa-0" elevation="0" tile>
						<v-card height="480px" elevation="0">
							<template v-slot:prepend>
								<h1 class="text-center display-1 font-weight-black green--text">
									LOGIN
								</h1>
							</template>
							<v-form
								ref="frmlogin"
								@keyup.native.enter="doLogin"
								lazy-validation
							>
								<h1
									class="text-center display-1 font-weight-black pa-3"
									style="color:#1A237E"
								>
									LOGIN
								</h1>
								<v-card-text>
									<v-text-field
										v-model="formlogin.username"
										label="Username"
										:rules="rule_username"
										outlined
										dense
									/>
									<v-text-field
										v-model="formlogin.password"
										label="Password"
										type="password"
										:rules="rule_password"
										outlined
										dense
									/>
									<v-alert
										outlined
										dense
										type="error"
										:value="form_error"
										icon="mdi-close-octagon-outline"
									>
										Username atau Password tidak dikenal.
									</v-alert>
								</v-card-text>
								<v-card-actions class="justify-center">
									<v-btn
										height="50px"
										color="#1A237E"
										class="white--text"
										@click="doLogin"
										:loading="btnLoading"
										:disabled="btnLoading"
										block
									>
										Sign In
									</v-btn>
								</v-card-actions>
								<v-card-actions class="justify-center">
									<v-btn
										height="50px"
										color="#1A237E"
										class="white--text"
										@click="showDialogVerifikasi"
										:disabled="btnLoading"
										block
									>
										Verifikasi Email
									</v-btn>
								</v-card-actions>
							</v-form>
						</v-card>
					</v-card>
					<v-dialog
						v-model="dialogkonfirmasiemail"
						max-width="500px"
						persistent
					>
						<v-form v-model="form_valid" ref="frmkonfirmasi" lazy-validation>
							<v-card>
								<v-card-title>
									<span class="headline">Konfirmasi Email</span>
								</v-card-title>
								<v-card-text>
									<v-alert type="success">
										Silahkan isikan email dan kode OTP yang telah kami kirim di
										isian berikut.
									</v-alert>
									<v-text-field
										v-model="formkonfirmasi.email"
										label="EMAIL"
										:rules="rule_email"
										outlined
										dense
									/>
									<v-text-field
										v-model="formkonfirmasi.code"
										label="CODE"
										outlined
										:rules="rule_code"
									>
									</v-text-field>
								</v-card-text>
								<v-card-actions>
									<v-spacer></v-spacer>
									<v-btn
										color="blue darken-1"
										text
										@click.stop="closedialogfrm"
									>
										KELUAR
									</v-btn>
									<v-btn
										color="blue darken-1"
										text
										@click.stop="konfirmasi"
										:loading="btnLoading"
										:disabled="btnLoading"
									>
										KONFIRMASI
									</v-btn>
								</v-card-actions>
							</v-card>
						</v-form>
					</v-dialog>
				</v-col>
			</v-row>
		</v-container>
	</FrontLayout>
</template>
<script>
	import FrontLayout from "@/views/layouts/FrontLayout";
	export default {
		name: "Login",
		created() {
			if (this.$store.getters["auth/Authenticated"]) {
				this.$router.push(
					"/dashboard/" + this.$store.getters["auth/AccessToken"]
				);
			}
		},
		data: () => ({
			btnLoading: false,
			//form
			form_valid: true,
			form_error: false,
			dialogkonfirmasiemail: false,
			formlogin: {
				username: "",
				password: "",
			},
			formkonfirmasi: {
				email: "",
				code: "",
			},
			rule_username: [
				value => !!value || "Kolom Username mohon untuk diisi !!!",
			],
			rule_password: [
				value => !!value || "Kolom Password mohon untuk diisi !!!",
			],
			rule_email: [
				value => !!value || "Email mohon untuk diisi !!!",
				v => /.+@.+\..+/.test(v) || "Format E-mail mohon di isi dengan benar",
			],
			rule_code: [value => /^[0-9]+$/.test(value) || "Code hanya boleh angka"],
		}),
		methods: {
			doLogin: async function() {
				if (this.$refs.frmlogin.validate()) {
					this.btnLoading = true;
					await this.$ajax
						.post("/auth/login", {
							username: this.formlogin.username,
							password: this.formlogin.password,
						})
						.then(({ data }) => {
							this.$ajax
								.get("/auth/me", {
									headers: {
										Authorization: data.token_type + " " + data.access_token,
									},
								})
								.then(response => {
									var data_user = {
										token: data,
										user: response.data,
									};
									this.$store.dispatch("auth/afterLoginSuccess", data_user);
								});
							this.btnLoading = false;
							this.form_error = false;
							this.$router.push("/dashboard/" + data.access_token);
						})
						.catch(() => {
							this.form_error = true;
							this.btnLoading = false;
						});
				}
			},
			konfirmasi: async function() {
				if (this.$refs.frmkonfirmasi.validate()) {
					this.btnLoading = true;
					await this.$ajax
						.post("/spmb/pmb/konfirmasi", {
							email: this.formkonfirmasi.email,
							code: this.formkonfirmasi.code,
						})
						.then(() => {
							this.dialogkonfirmasiemail = false;
							this.btnLoading = false;
						})
						.catch(() => {
							this.btnLoading = false;
						});
					this.$refs.frmkonfirmasi.reset();
					this.frmkonfirmasi = Object.assign({}, { email: "", code: "" });
					this.$router.replace("/login");
				}
			},
			showDialogVerifikasi() {
				this.dialogkonfirmasiemail = true;
			},
			closedialogfrm() {
				this.dialogkonfirmasiemail = false;
				setTimeout(() => {
					this.frmkonfirmasi = Object.assign({}, { email: "", code: "" });
					this.$refs.frmkonfirmasi.resetValidation();
				}, 300);
			},
		},
		components: {
			FrontLayout,
		},
	};
</script>
