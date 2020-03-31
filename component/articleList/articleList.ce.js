Vue.component('articleList', {
    props: {
        author: Object,
        filter: {
            type: Object,
            default: function () {
                return {orderBy: 'date,desc'}
            }
        }
    },
    data: function () {
        return {
            articles: []
        }
    },
    mounted() {
        this.setFilters();
        this.updateList();
    },
    watch: {
        filter: {
            deep: true,
            handler(newV, oldV) {
                console.log(this.filter.orderBy)
                if (this.articles.length > 0) {
                    this.applySort();
                }
            }
        }
    },

    methods: {
        setFilters: function () {
            if (this.author) {
                this.filter.author = this.author.userName;
            }
        },
        applySort: function () {
            let activeFilter = this.filter.orderBy;
            this.articles.sort(function (a, b) {
                if(activeFilter === 'inserted'){
                    return b.inserted - a.inserted;
                }
                if(activeFilter === 'name'){
                    return b.name < a.name ? 1 : -1;
                }
                if(activeFilter === 'unique'){
                    return b.metrics.unique - a.metrics.unique;
                }
                return 0;
            });
        },
        updateList: function () {
            api.get('articleList', {params: this.filter}).then(res => {
                this.articles = res.data;
                this.applySort();
            })
        }
    },
    template: document.querySelector('#articleList').innerHTML
});
Vue.filter('formatDate', function (value) {
    if (value) {
        return moment(value).format('MM/DD/YYYY')
    }
});