Vue.component('contactUs',{
    data:function(){
        return {
            contact:{
                email:'',
                body:'',
                topic:'',
                contactHash:contactHash
            },
            showSent:false
        }
    },
    methods:{
        send(){
            this.showSent = true;
            api.post('contactUs',this.contact).then(res => {

            })
        }
    },
    template: document.querySelector('#contactUs')
});