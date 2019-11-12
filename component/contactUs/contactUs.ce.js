Vue.component('contactUs',{
    data:function(){
        return {
            contact:{
                email:'',
                body:'',
                topic:'',
                'h-captcha-response':'',
                contactHash:contactHash
            },
            showSent:false
        }
    },
    mounted() {
        if (localStorage.user) {
            this.contact.email = JSON.parse(localStorage.user).emails[0].email
        }
    },
    methods:{
        send(){
            this.contact['h-captcha-response'] = hcaptcha.getResponse();
            this.showSent = true;
            api.post('contactUs',this.contact).then(res => {

            })
        },
    },
    template: document.querySelector('#contactUs')
});