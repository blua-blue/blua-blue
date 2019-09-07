Vue.component('register', {
    mixins:[common],
    data:()=>{
        return {
            username:'',
            password:'',
            email:'',
            acceptTAC:false,
            duplicate:false,
            loggedIn:localStorage.token,
            passwordStrength:0,
            processing:false
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
    watch:{
        password:function(value){
            this.passwordStrength = this.testPassword(value);
        }
    },
    methods:{
        showModal() {
            fetch('{{base}}component/termsConditions/termsConditions.view.html').then(res => {
                res.text().then(html => {
                    this.$root.$emit('toggleModal', {content: html, headline: 'Terms & Conditions'})
                })
            });
        },
        logout(){
            delete localStorage.token;
            this.loggedIn = false;
        },
        doRegister() {
            this.processing = true;
            this.duplicate = false;
            api.post('register',this._data).then((res)=>{
                localStorage.setItem('token',res.data.token);
                this.loggedIn = res.data.token;
                this.processing = false;
                this.$root.$emit('toggleModal', {
                    content: 'You know the deal: you have just received an email to confirm your account.',
                    modalClass: 'is-link'
                });
            }).catch((err)=>{
                this.duplicate = true;
                this.processing = false;
            })

        },

    },
    template:document.querySelector('#register').innerHTML
});
