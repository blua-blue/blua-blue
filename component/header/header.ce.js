new Vue({
    el:'#header',
    data:{
        text:'',
        searchResults:[],
        menuIsOpen:false,
        dropdownIsOpen: false,
        loggedIn: localStorage.token
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
        toggleMenu(){
            this.menuIsOpen = !this.menuIsOpen;
        },
        toggleDropdown(){
            this.dropdownIsOpen = !this.dropdownIsOpen;
        },
        ajaxSearch:function(){
            api.get('search?q='+this.text).then(res=>{
                this.searchResults = res.data;
            })
        }
    }
});
