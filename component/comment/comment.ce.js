Vue.component('comment', {
    props:['articleId'],
    data: function(){
        return {
            user:{},
            comment:''
        }
    },
    template: document.querySelector('#comment'),
    mounted(){
        this.setUser(typeof localStorage.user !== 'undefined');
        this.$root.$on('login', this.setUser);

    },
    methods:{
        setUser: function (loggedIn) {
            this.user = loggedIn ? JSON.parse(localStorage.user) : {};

        },
        send:function(){
            if(this.comment.length>5){
                api.post('comment',{
                    articleId:this.articleId,
                    comment:this.comment
                }).then(res=>{
                    location.reload();
                }).catch(err=>{})
            }
        }
    }
});
