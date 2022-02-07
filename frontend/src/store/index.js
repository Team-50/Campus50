import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
import SecureLS from "secure-ls";
import Uifront from "./modules/uifront";
import Uiadmin from "./modules/uiadmin";
import Auth from "./modules/auth";

const ls = new SecureLS({ isCompression: false });

Vue.use(Vuex);

export default new Vuex.Store({
	modules: {
		uifront: Uifront,
		auth: Auth,
		uiadmin: Uiadmin,
	},
	plugins: [
    createPersistedState({
      "key": "campus50",
      storage: {
        getItem: key => ls.get(key),
        setItem: (key, value) => ls.set(key, value),
        removeItem: key => ls.remove(key)
      }
    }),
  ],
});
