//state
const getDefaultState = () => 
{
    return {      
        loaded:false,  
        captcha_site_key:'',
        tahun_pendaftaran:new Date().getFullYear(),
        semester_pendaftaran:1,
        identitas:{
            nama_pt:'',
            nama_pt_alias:''
        },        
    }
}
const state = getDefaultState();

//mutations
const mutations = {
    setLoaded(state,loaded)
    {
        state.loaded=loaded;
    },
    setCaptchaSiteKey(state,key)
    {
        state.captcha_site_key = key;
    },    
    setTahunPendaftaran(state,tahun)
    {
        state.tahun_pendaftaran = tahun;
    },    
    setSemesterPendaftaran(state,semester)
    {
        state.semester_pendaftaran = semester;
    },    
    setIdentitas(state,identitas)
    {
        state.identitas = identitas;
    },    
    resetState (state) {
        Object.assign(state, getDefaultState())
    }
}
const getters= {
    isLoaded : state => {
        return state.loaded;
    },
    getCaptchaKey: state => 
    {   
        return state.captcha_site_key;
    },
    getTahunPendaftaran: state => 
    {             
        return state.tahun_pendaftaran;
    },
    getSemesterPendaftaran: state => 
    {             
        return parseInt(state.semester_pendaftaran);
    },
    getNamaPT: state => 
    {             
        return state.identitas.nama_pt;
    },    
    getNamaPTAlias: state => 
    {
        return state.identitas.nama_pt_alias;
    },
    getBentukPT: state => 
    {             
        return state.identitas.bentuk_pt;
    },
}
const actions = {
    init: async function ({commit,state},ajax)
    {        
        //dipindahkan kesini karena ada beberapa kasus yang melaporkan ini membuat bermasalah.
        commit('setLoaded',false);              
        if (!state.loaded)
        {            
            await ajax.get('/system/setting/uifront').then(({data})=>{                  
                commit('setCaptchaSiteKey',data.captcha_site_key);                                         
                commit('setTahunPendaftaran',data.tahun_pendaftaran);                                         
                commit('setSemesterPendaftaran',data.semester_pendaftaran);                                         
                commit('setIdentitas',data.identitas);                                                         
                commit('setLoaded',true);
            })
        }
    },
    reinit ({ commit }) 
    {
        commit('resetState');
    },
}
export default {
    namespaced: true,
    state,        
    mutations,
    getters,
    actions
}