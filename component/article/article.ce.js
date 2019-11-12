Vue.component('bluaArticle', {
    props: ['authorId', 'articleId'],
    template: `
        <a :href="'{{base}}write/'+articleId" class="button" v-if="isMine">
            <i class="material-icons">edit</i>
        </a>
    `,
    data: function () {
        return {
            isMine: false
        }
    },
    update() {
        this.evaluateIfMine();
    },
    mounted() {

        this.evaluateIfMine();
        this.$root.$on('login', this.evaluateIfMine)
    },
    methods: {
        evaluateIfMine() {
            if (typeof localStorage.user !== 'undefined') {
                let user = JSON.parse(localStorage.user);

                this.isMine = this.authorId === user.id;
            } else {
                this.isMine = false;
            }

        }
    }
});