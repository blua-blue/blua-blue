new Vue({
    el: '#admin',
    data:{
        tabs:[
            {name:'Users',hash:'users'},
            {name:'Categories',hash:'categories'},
            {name:'Articles',hash:'articles'},
            {name:'Templates',hash:'templates'},
        ],
        currentTab:'users',
    },
    created(){
        this.getHash();
    },
    methods:{
        getHash(){
            if(window.location.hash){
                this.setHash(window.location.hash.substring(1));
            } else {
                this.setHash('articles')
            }
        },
        setHash(hash){
            this.currentTab = hash;
            window.location.hash = hash;
        }
    }
});