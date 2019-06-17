Vue.component('profileSettings',{
    data:function(){
        return {
            user:{},
            warnings:{
                email:false
            },
            currentNavigation:'email',
            navigation:{
                general:[
                    {slug:'email',name:'Email'},
                    {slug:'picture',name:'Profile Picture'},

                    ]
            },
            disabled:false
        }
    },
    methods:{
        updateEmail:function(){
            this.disabled = true;
            api.put('profile',this.user).then(res=>{
                this.warnings.email = true;
                this.disabled = false;
            }).catch(err=>alert('We were unable to send to this email address'));
        }
    },
    mounted(){
        this.user = JSON.parse(localStorage.user);

    },
    template:document.querySelector('#profileSettings').innerHTML
});
