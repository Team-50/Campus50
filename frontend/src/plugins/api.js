import axios from "axios";
export default {
    
    install(Vue)
    {
        let ajax = axios.create({
            baseURL:process.env.VUE_APP_APIUrlV1
        });
        
        Vue.prototype.$api = {
            url : process.env.VUE_APP_API_HOST,
            storageURL : process.env.VUE_APP_STORAGE_URL,
            post : async function (path)
            {
                return await ajax.post(path);
            },          
        };        
        Vue.prototype.$ajax = ajax;
    }
} 