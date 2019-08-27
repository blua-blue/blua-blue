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
        this.user = JSON.parse(localStorage.user);
        console.log(this.user);
        console.log(this.articleId);
    },
    methods:{
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
