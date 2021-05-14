import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify';
import VNumeric from "vuetify-numeric/vuetify-numeric.umd.min";
import api from './plugins/api';

Vue.use(api);
Vue.use(VNumeric);
import '@/plugins/Dayjs';

Vue.config.productionTip = false

//filter output
Vue.filter('formatTA', function(value) 
{
	value = parseInt(value);
	return value+'/'+(value+1);
});
Vue.filter('formatUang', function(value) 
{
	var num = new Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1.');
	var pos = num.lastIndexOf('.');
	num = num.substring(0,pos) + ',' + num.substring(pos+1)	
	return num;
});

//mixin
Vue.mixin({
	methods: {
		
	}
})
new Vue({
	router,
	store,
	vuetify,
	render: h => h(App)
}).$mount('#app')
