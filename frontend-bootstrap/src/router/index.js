import Vue from "vue";
import VueRouter from "vue-router";
import store from '../store/index'
//import Home from "../views/Home.vue";

Vue.use(VueRouter);
const routes = [
  /*{
    path: "/",
    name: "Home",
    component: Home
  },
  {
    path: "/about",
    name: "About",
    component: () =>
      import( "../views/About.vue")
  }*/
  {
		path: '/',
		name: 'FrontPage',
		meta:{
			title: "HALAMAN DEPAN"
		},
		component: () => import('../views/pages/front/Home.vue')
	}
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes
});

export default router;
