<template>
    <FrontLayout>
        <!-- <v-container class="fill-height" fluid>
            <v-row align="center" justify="center" no-gutters>
                <v-col xs="12" sm="6" md="4">
                    <h1 class="text-center display-1 font-weight-black green--text">LOGIN</h1>
                    <v-alert
                        outlined
                        dense
                        type="error"
                        :value="form_error"
                        icon="mdi-close-octagon-outline"
                    >
                        Username atau Password tidak dikenal !.
                    </v-alert>
                    <v-form ref="frmlogin" @keyup.native.enter="doLogin" lazy-validation>
                        <v-card outlined>
                            <v-card-text>
                                <v-text-field
                                    v-model="formlogin.username"
                                    label="Username"
                                    :rules="rule_username"
                                    outlined
                                    dense />
                                <v-text-field
                                    v-model="formlogin.password"
                                    label="Password"
                                    type="password"
                                    :rules="rule_password"
                                    outlined
                                    dense />  
                            </v-card-text>
                            <v-card-actions class="justify-center">
                                 <v-btn
                                    color="success"
                                    @click="doLogin"
                                    :loading="btnLoading"
                                    :disabled="btnLoading"
                                    block>
                                        Login
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-col>
            </v-row>
        </v-container> -->
        
        <v-container class="grey lighten-5" fluid>
            <v-row
                no-gutters
                align="center"
                >
            <v-col
                sm="12"
                md="8"
                align="center"
            >
                <v-card
                class="pa-2"
                elevation="0"
                color="grey lighten-5"
                >
                    <v-img
                        max-width="400px"
                        :src="$api.url+'/storage/images/campus_50_logo.png'"
                        ></v-img>
                </v-card>
            </v-col>
            
            <v-col
                sm="12"
                md="4"
            >
                <v-card
                class="pa-2"
                elevation="0"
                tile
                >
                    <v-card height="450px">
                        <!-- <v-navigation-drawer
                        width="550px"
                        absolute
                        permanent
                        right
                        > -->
                        <template v-slot:prepend>
                            <h1 class="text-center display-1 font-weight-black green--text">LOGIN</h1>
                        </template>
                            <v-form ref="frmlogin" @keyup.native.enter="doLogin" lazy-validation>
                                <h2 class="text-center green--text pt-2">Login</h2>
                                <v-card-text>
                                    <v-text-field
                                        v-model="formlogin.username"
                                        label="Username"
                                        :rules="rule_username"
                                        outlined
                                        dense />
                                    <v-text-field
                                        v-model="formlogin.password"
                                        label="Password"
                                        type="password"
                                        :rules="rule_password"
                                        outlined
                                        dense />  
                                </v-card-text>
                                <v-card-actions class="justify-center">
                                        <v-btn
                                        height="50px"
                                        color="success"
                                        @click="doLogin"
                                        :loading="btnLoading"
                                        :disabled="btnLoading"
                                        block>
                                            Sign In
                                    </v-btn>
                                </v-card-actions>
                            </v-form>
                    </v-card>
                </v-card>
            </v-col>
            </v-row>
        </v-container>
        
        
    </FrontLayout>
</template>
<script>
import FrontLayout from '@/views/layouts/FrontLayout';
export default {
    name: 'Login',
    created()
	{
		if (this.$store.getters['auth/Authenticated'])
		{
			this.$router.push('/dashboard/'+this.$store.getters['auth/AccessToken']);
		}
	},
    data: () => ({
        btnLoading:false,
        //form
        form_error:false,
        formlogin: {
            username:'',
            password:''
        },
        rule_username:[
            value => !!value||"Kolom Username mohon untuk diisi !!!"
        ],
        rule_password:[
            value => !!value||"Kolom Password mohon untuk diisi !!!"
        ],

    }),
    methods: {
        doLogin: async function ()
        {
            if (this.$refs.frmlogin.validate())
            {
                this.btnLoading=true;
                await this.$ajax.post('/auth/login',{
                    username:this.formlogin.username,
                    password:this.formlogin.password
                }).then(({data})=>{
                    this.$ajax.get('/auth/me',{
                        headers:{
                            'Authorization': data.token_type+' '+data.access_token,
                        }
                    })
                    .then(response => {
                        var data_user = {
                            token: data,
                            user:response.data
                        }
                        this.$store.dispatch('auth/afterLoginSuccess',data_user);
                    });
                    this.btnLoading=false;
                    this.form_error=false;
                    this.$router.push('/dashboard/'+data.access_token);
                }).catch(() => {
                    this.form_error=true;
                    this.btnLoading=false;
                });
            }
        }
    },
    components: {
		FrontLayout
	}
}
</script>
