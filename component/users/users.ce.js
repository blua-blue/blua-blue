Vue.component('userList',{
    data:function(){
        return {
            users:[],
            search:'',
            currentPage:1,
            previousButton:false,
            nextButton:false,
            pagination:{
                nameFilter:'',
                top:20,
                skip:0,
                total:0,
                links:[],
            }
        }
    },
    created(){
        this.debounceSearch = _.debounce(this.userSearch,300);
    },
    mounted(){
        this.loadPage(this.currentPage);
    },
    watch:{
        search:function(newV,oldV){
            this.debounceSearch();
        }
    },
    methods:{
        userSearch(){
            this.pagination.nameFilter = this.search;
            this.loadPage(1);
        },
        loadPage(page){
            this.currentPage = page;
            this.pagination.skip = (page-1) * this.pagination.top;
            api.get('users',{params:this.pagination}).then(res=>{
                this.users = res.data.users;
                this.pagination.total = res.total;
                this.calcPagination();
                console.log(this.pagination);
            })
        },
        calcPagination(){
            this.pagination.links = [];
            let buffer = [1,2,3];
            buffer.forEach(x=>{
                if(this.currentPage - x > 0){
                    this.previousButton = true;
                    this.pagination.links.push({page:this.currentPage - x,active:false});
                }
            });
            this.pagination.links.push({page:this.currentPage,active:true});
            buffer.forEach(x=>{
                if( (this.currentPage + x) * this.pagination.top < ( this.pagination.total + this.pagination.top ) ){
                    this.pagination.links.push({page:this.currentPage + x,active:false});
                    this.nextButton = true;
                }
            })
        }
    },
    template:document.querySelector('#users').innerHTML
});