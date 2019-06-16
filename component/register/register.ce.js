
const registerForm = {
    name: 'register-form',
    data:()=>{
        return {
            username:'',
            password:'',
            email:'',
            acceptTAC:false,
            duplicate:false,
            showModal:false,
            loggedIn:localStorage.token
        }
    },
    mounted(){
        if(this.loggedIn){
            // check if still valid token
            api.get('register').then(x=>{
                console.log(x.data);
            }).catch(e=>{
                console.log(e);
                this.logout();
            })
        }
    },
    methods:{
        logout(){
            console.log('test');
            delete localStorage.token;
            this.loggedIn = false;
        },
        register(){
            this.duplicate = false;
            api.post('register',this._data).then((res)=>{
                localStorage.setItem('token',res.data.token);
                this.loggedIn = res.data.token;
            }).catch((err)=>{
                this.duplicate = true;
            })

        },

    },
    template:document.querySelector('#register').innerHTML
};
