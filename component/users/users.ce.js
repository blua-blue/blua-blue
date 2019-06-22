Vue.component('userList',{
    data:function(){
        return {
            users:[],
            search:'',
            currentPage:1,
            pagination:{
                top:20,
                skip:0,
                total:0,
            }
        }
    },
    computed:{
        totalPages(){
            return this.pagination.total>0 ? this.pagination.total / this.pagination.top : 1;
        },

    },
    mounted(){
        this.loadPage();
    },
    methods:{
        loadPage(){
            api.get('users',{params:this.pagination}).then(res=>{
                this.users = res.data.users;
                this.pagination.total = res.total;
            })
        },
        calcPagination(){

        }
    },
    template:document.querySelector('#users').innerHTML
});