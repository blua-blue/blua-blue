Vue.component('comment', {
    props:['articleId'],
    data: function(){
        return {
            user:{},
            comment: '',
            valid: true
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
                this.valid = false;
                api.post('comment',{
                    articleId:this.articleId,
                    comment:this.comment
                }).then(res=>{
                    this.$root.$emit('comment');
                    this.comment = '';
                    this.valid = true;
                }).catch(err=>{})
            }
        }
    }
});
