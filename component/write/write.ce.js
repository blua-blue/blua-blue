new Vue({
    el: '#posting', data: {
        article:{
            category_id: '',
            public:true,
            isDraft:true,
            name:'',
            teaser:'',
            image:{
                path:''
            },
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
            this.loadArticle('{{loadedArticleId}}');
        }
        console.log('{{loadedArticleId}}');
        api.get('categories?all').then(res => {this.categories = res.data});
    },
    methods:{
        loadArticle(id){
            api.get('write?id='+id).then(res=>{
                this.article = res.data;
                if(Array.isArray(res.data.image)){
                    this.article.image = {path:''}
                } else if(res.data.image.path.substring(0,4) !== 'http'){
                    this.article.image.path = '{{base}}'+res.data.image.path;
                }
                this.article.isDraft = !res.data.publish_date;
                this.article.public = res.data.is_public;
                console.log(this.article);
            }).catch(err=>{
                // not allowed
            })
        },
        changePic($event){
            // this.article.picture = $event.target.files[0];
            let reader = new FileReader();
            reader.readAsDataURL($event.target.files[0]);
            reader.onload = (function (data) {
                this.article.image = {};
                this.article.image.path = data.target.result;
                console.log(this.article.path);
            }).bind(this);

        },
        create(){
            let obj = this.article;
            console.log(this.article);
            api.post('article',obj).then(res=>{
                this.loadArticle(res.data.id);
            }).catch(err=>{

            })
        }
    }

});
