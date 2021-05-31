//state
const getDefaultState = () => {
	return {
		loaded: false,
		//page
		default_dashboard: null,
		pages: [],

		daftar_ta: [],
		tahun_pendaftaran: null,
		tahun_akademik: null,

		daftar_semester: [],
		semester_pendaftaran: null,
		semester_akademik: null,

		daftar_fakultas: [],
		fakultas_id: null,

		daftar_prodi: [],
		prodi_id: null,

		daftar_kelas: [],
		idkelas: null,

		daftar_status_mhs: [],
		k_status: null,

		skala_nilai: ["A", "B", "C", "D", "E"],
		theme: null,
	};
};
const state = getDefaultState();

//mutations
const mutations = {
	setNewPage(state, page) {
		state.pages.push(page);
	},
	replacePage(state, page, index) {
		state.pages[index] = page;
	},
	removePage(state, name) {
		var i;
		for (i = 0; i < state.pages.length; i++) {
			if (state.pages[i].name == name) {
				state.pages.splice(i, 1);
				break;
			}
		}
	},
	setLoaded(state, loaded) {
		state.loaded = loaded;
	},
	setDashboard(state, name) {
		state.default_dashboard = name;
	},

	setDaftarTA(state, daftar) {
		state.daftar_ta = daftar;
	},
	setTahunPendaftaran(state, tahun) {
		state.tahun_pendaftaran = tahun;
	},
	setTahunAkademik(state, tahun) {
		state.tahun_akademik = tahun;
	},

	setDaftarSemester(state, daftar) {
		state.daftar_semester = daftar;
	},
	setSemesterPendaftaran(state, semester) {
		state.semester_pendaftaran = semester;
	},
	setSemesterAkademik(state, semester) {
		state.semester_akademik = semester;
	},

	setDaftarFakultas(state, daftar) {
		state.daftar_fakultas = daftar;
	},
	setFakultasID(state, id) {
		state.fakultas_id = id;
	},

	setDaftarProdi(state, daftar) {
		state.daftar_prodi = daftar;
	},
	setProdiID(state, id) {
		state.prodi_id = id;
	},

	setDaftarKelas(state, daftar) {
		state.daftar_kelas = daftar;
	},
	setIDKelas(state, id) {
		state.idkelas = id;
	},

	setDaftarStatusMahasiswa(state, daftar) {
		state.daftar_status_mhs = daftar;
	},
	setStatusMahasiswa(state, k_status) {
		state.k_status = k_status;
	},

	setTheme(state, theme) {
		state.theme = theme;
	},

	resetState(state) {
		Object.assign(state, getDefaultState());
	},
};
const getters = {
	Page: state => name => {
		let page = state.pages.find(halaman => halaman.name == name);
		return page;
	},
	AtributeValueOfPage: state => (name, key) => {
		let page = state.pages.find(halaman => halaman.name == name);
		return page[key];
	},

	getDefaultDashboard: state => {
		return state.default_dashboard;
	},

	getDaftarTA: state => {
		return state.daftar_ta;
	},
	getDaftarTABefore: state => ta => {
		let daftar_ta = state.daftar_ta;
		var daftar = [];
		daftar_ta.forEach(element => {
			if (element.value <= ta) {
				daftar.push(element);
			}
		});
		return daftar;
	},
	getTahunPendaftaran: state => {
		return parseInt(state.tahun_pendaftaran);
	},
	getTahunAkademik: state => {
		return parseInt(state.tahun_akademik);
	},

	getDaftarSemester: state => {
		return state.daftar_semester;
	},
	getNamaSemester: state => key => {
		var nama_semester = "";
		let found = state.daftar_semester.find(semester => semester.id == key);
		if (typeof found !== "undefined") {
			nama_semester = found.text;
		}
		return nama_semester;
	},
	getSemesterPendaftaran: state => {
		return parseInt(state.semester_pendaftaran);
	},
	getSemesterAkademik: state => {
		return parseInt(state.semester_akademik);
	},

	getDaftarProdi: state => {
		return state.daftar_prodi.filter(el => el != null);
	},
	getProdiID: state => {
		return parseInt(state.prodi_id);
	},
	getProdiName: state => key => {
		if (key == "" || key == null || key == "undefined") {
			return "N.A";
		} else {
			var prodi = state.daftar_prodi.find(el => el.id == key);
			return prodi.nama_prodi;
		}
	},

	getDaftarFakultas: state => {
		return state.daftar_fakultas.filter(el => el != null);
	},
	getFakultasID: state => {
		return state.fakultas_id;
	},
	getFakultasName: state => key => {
		var nama_fakultas = "";
		let found = state.daftar_fakultas.find(fakultas => fakultas.id == key);
		if (typeof found !== "undefined") {
			nama_fakultas = found.text;
		}
		return nama_fakultas;
	},

	getDaftarKelas: state => {
		return state.daftar_kelas;
	},
	getIDKelas: state => {
		return state.idkelas;
	},
	getNamaKelas: state => id => {
		var nama_kelas = "N.A";
		let found = state.daftar_kelas.find(kelas => kelas.id == id);
		if (typeof found !== "undefined") {
			nama_kelas = found.text;
		}
		return nama_kelas;
	},

	getDaftarStatusMahasiswa: state => {
		return state.daftar_status_mhs;
	},
	getKStatus: state => {
		return state.k_status;
	},
	getStatusMahasiswa: state => id => {
		var nama_status = "N.A";
		let found = state.daftar_status_mhs.find(status_mhs => status_mhs.id == id);
		if (typeof found !== "undefined") {
			nama_status = found.text;
		}
		return nama_status;
	},

	getSkalaNilai: state => {
		return state.skala_nilai;
	},

	getTheme: state => key => {
		return state.theme == null ? "" : state.theme[key];
	},
};
const actions = {
	init: async function({ commit, state, rootGetters }, ajax) {
		//dipindahkan kesini karena ada beberapa kasus yang melaporkan ini membuat bermasalah.
		commit("setLoaded", false);
		if (!state.loaded && rootGetters["auth/Authenticated"]) {
			commit(
				"setSemesterPendaftaran",
				rootGetters["uifront/getSemesterPendaftaran"]
			);
			let token = rootGetters["auth/Token"];
			await ajax
				.get("/system/setting/uiadmin", {
					headers: {
						Authorization: token,
					},
				})
				.then(({ data }) => {
					commit("setDaftarTA", data.daftar_ta);
					commit("setTahunPendaftaran", data.tahun_pendaftaran);
					commit("setTahunAkademik", data.tahun_akademik);
					commit("setDaftarSemester", data.daftar_semester);
					commit("setSemesterAkademik", data.semester_akademik);

					let daftar_fakultas = data.daftar_fakultas;
					var fakultas = [];
					daftar_fakultas.forEach(element => {
						fakultas.push({
							id: element.kode_fakultas,
							text: element.nama_fakultas,
							nama_fakultas: element.nama_fakultas,
						});
					});
					commit("setDaftarFakultas", fakultas);
					commit("setFakultasID", data.fakultas_id);

					let daftar_prodi = data.daftar_prodi;
					var prodi = [];
					daftar_prodi.forEach(element => {
						var nama_prodi =
							element.konsentrasi == "N.A" || element.konsentrasi == null || element.konsentrasi == ""
								? element.nama_prodi + " (" + element.nama_jenjang + ")"
								: element.nama_prodi +
									" Kons. " +
									element.konsentrasi + " (" + element.nama_jenjang + ")";
						prodi.push({
							id: element.id,
							text:
								element.nama_prodi_alias +
								" (" +
								element.nama_jenjang +
								")",
							nama_prodi: nama_prodi,
						});
					});
					commit("setDaftarProdi", prodi);
					commit("setProdiID", data.prodi_id);

					commit("setDaftarKelas", data.daftar_kelas);
					commit("setIDKelas", data.idkelas);

					commit("setDaftarStatusMahasiswa", data.daftar_status_mhs);
					commit("setStatusMahasiswa", data.k_status);

					commit("setTheme", data.theme);

					commit("setLoaded", true);
				});
		}
	},

	addToPages({ commit, state }, page) {
		let found = state.pages.find(halaman => halaman.name == page.name);
		if (!found) {
			commit("setNewPage", page);
		}
	},
	updatePage({ commit, state }, page) {
		var i;
		for (i = 0; i < state.pages.length; i++) {
			if (state.pages[i].name == page.name) {
				break;
			}
		}
		commit("replacePage", page, i);
	},
	deletePage({ commit }, name) {
		commit("removePage", name);
	},

	changeDashboard({ commit }, name) {
		commit("setDashboard", name);
	},

	updateFakultas({ commit }, id) {
		commit("setFakultasID", id);
	},
	updateProdi({ commit }, id) {
		commit("setProdiID", id);
	},

	updateTahunPendaftaran({ commit }, tahun) {
		commit("setTahunPendaftaran", tahun);
	},
	updateTahunAkademik({ commit }, tahun) {
		commit("setTahunAkademik", tahun);
	},

	updateSemesterPendaftaran({ commit }, semester) {
		commit("setSemesterPendaftaran", semester);
	},
	updateSemesterAkademik({ commit }, semester) {
		commit("setSemesterAkademik", semester);
	},

	updateIDKelas({ commit }, idkelas) {
		commit("setIDKelas", idkelas);
	},
	reinit({ commit }) {
		commit("resetState");
	},
};
export default {
	namespaced: true,
	state,
	mutations,
	getters,
	actions,
};
