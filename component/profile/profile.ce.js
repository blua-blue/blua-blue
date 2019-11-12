Vue.component('profile', {
    template: document.querySelector('#profile'),
    data: function () {
        return {
            tabs: [
                {name: 'My articles', hash: 'articles'},
                {name: 'API', hash: 'api'},
                {name: 'Settings', hash: 'settings'},
            ],
            currentTab: 'articles',
            author: {}
        }
    },
    created(){
        this.author = JSON.parse(localStorage.user);
        console.log(this.author);
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