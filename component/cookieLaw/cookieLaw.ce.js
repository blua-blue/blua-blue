Vue.component('cookieLaw',{
    data:function(){
        return {
            accepted:false
        }
    },
    methods:{
        dismiss(){
            this.accepted = true;
            localStorage.acceptedCookies = true;
        }
    },
    mounted(){
        this.accepted = localStorage.getItem('acceptedCookies');
    },
    template:document.querySelector('#cookieLaw').innerHTML
});