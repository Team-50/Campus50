import Vue from "vue";
import Vuex from "vuex";
import VuexPersistence from "vuex-persist";
import Uifront from "./modules/uifront";
import Uiadmin from "./modules/uiadmin";
import Auth from "./modules/auth";

const vuexStorage = new VuexPersistence({
	key: "campus50",
	storage: localStorage,
});

Vue.use(Vuex);

export default new Vuex.Store({
	modules: {
		uifront: Uifront,
		auth: Auth,
		uiadmin: Uiadmin,
	},
	plugins: [vuexStorage.plugin],
});
