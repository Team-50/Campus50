import Vue from 'vue';
import Vuetify from 'vuetify/lib';
import VCurrencyField from 'v-currency-field'
import { VTextField } from 'vuetify/lib'  //Globally import VTextField

Vue.use(Vuetify);

Vue.component('v-text-field', VTextField);
Vue.use(VCurrencyField, { 
	decimalLength: 0,
	autoDecimalMode: true,
	min: null,
	max: null,
	valueAsInteger: true,
	defaultValue: 0
});

export default new Vuetify({

});
