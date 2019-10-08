Vue.component('profileSettings',{
    data:function(){
        return {
            user:{},
            savedEmail:'',
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
            disabled:true
        }
    },
    watch:{
        'user.emails':{
            handler: function(newVal){
                if(newVal[0].email !== this.savedEmail || this.user.emails[0].confirm_date === null){
                    this.disabled = false;
                } else {
                    this.disabled = true;
                }
            },
            deep: true
        }
    },
    methods:{
        updateEmail:function(){
            this.disabled = true;
            api.put('profile',this.user).then(res=>{
                this.warnings.email = true;
                this.disabled = false;
            }).catch(err=>alert('We were unable to send to this email address'));
        },
        updateProfilePicture:function(uploadId){
            if(uploadId){
                this.user.image_id = uploadId;
                this.disabled = true;
                api.put('profile',this.user).then(res=>{
                    api.get('register').then(x=>{
                        localStorage.user = JSON.stringify(x.data.user);
                        this.user = x.data.user;
                    });
                    this.disabled = false;
                }).catch(err=>alert('We were unable to update your profile'));
            }

        }
    },
    mounted(){
        this.user = JSON.parse(localStorage.user);
        this.savedEmail = this.user.emails[0].email;
    },
    template:document.querySelector('#profileSettings').innerHTML
});
