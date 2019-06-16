Vue.component('articleList',{
    props:{
        author:Object
    },
    data: function(){
        return {
            articles:[],
            filter:{orderBy:'date,desc'}
        }
    },
    mounted(){
        this.setFilters();
        this.updateList();
    },

    methods:{
        setFilters:function(){
            if(this.author){
                this.filter.author = this.author.user_name;
            }
        },
        updateList:function(){
            api.get('articleList',{params:this.filter}).then(res=>{
                this.articles = res.data;
            })
        }
    },
    template:document.querySelector('#articleList').innerHTML
});