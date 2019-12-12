Vue.component('developer', {
    template: document.querySelector('#developer'),
    data: function () {
        return {
            tabs: [
                {hash: 'hosting', name: 'Self hosting & Installation'},
                {hash: 'sdk', name: 'SDKs & Integrations'},
                {hash: 'api', name: 'REST API'},
                {hash: 'marketplace', name: 'Marketplace'},
            ],
            currentTab: 'hosting'
        }
    },
    mounted(){
        this.getHash();
    },
    methods:{
        getHash(){
            if(window.location.hash){
                this.setHash(window.location.hash.substring(1));
            } else {
                this.setHash('hosting')
            }
        },
        setHash(hash){
            this.currentTab = hash;
            window.location.hash = hash;
        }
    }
});
