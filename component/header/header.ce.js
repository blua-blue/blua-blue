new Vue({
    el:'#header',
    data:{
        text:'',
        searchResults:[]
    },
    watch:{
        text: function (newV,oldV) {
            this.debounceSearch();
        }
    },
    created:function(){
        this.debounceSearch = _.debounce(this.ajaxSearch,300);
    },
    methods:{
        ajaxSearch:function(){
            api.get('search?q='+this.text).then(res=>{
                this.searchResults = res.data;
            })
        }
    }
});
