Vue.component('commentList', {
    props: ['articleId'],
    template: document.querySelector('#commentList'),
    data: function () {
        return {
            comments: []
        }
    },
    mounted() {
        this.getComments();
        this.$root.$on('comment', () => {
            this.getComments();
        })
    },
    methods: {
        convertTime: function (timeStamp) {
            return moment(parseInt(timeStamp)).fromNow();
        },
        getComments: function () {
            api.get('comment?articleId=' + this.articleId).then(d => {
                this.comments = d.data;
            }).catch(er => {
                console.log('error retrieving comments');
            })
        }
    }
});
