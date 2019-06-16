new Vue({
    el: '#posting', data: {
        article:{
            category_id: '',
            public:true,
            isDraft:true,
            name:'',
            teaser:'',
            content:[
                {content:''}
            ],
            keywords:'',
        },
        categories: [
            {name: 'Other'}
        ]
    },
    components:{
        'editor':Editor
    },
    created(){
        console.log('{{loadedArticleId}}');
        if('{{loadedArticleId}}' !== ''){
            api.get('write?id={{loadedArticleId}}').then(res=>{
                this.article = res.data;
                this.article.isDraft = !res.data.publish_date;
                this.article.public = res.data.is_public;
            }).catch(err=>{
                // not allowed
            })
        }
        console.log('{{loadedArticleId}}');
        api.get('categories?all').then(res => {this.categories = res.data});
    },
    methods:{
        create(){
            let obj = this.article;
            api.post('write',obj).then(res=>{
                this.article.id = res.data.id;
            }).catch(err=>{

            })
        }
    }

});