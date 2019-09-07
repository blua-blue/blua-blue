Vue.component('metric', {
    props: ['slug'],
    template: document.querySelector('#metric'),
    data: function () {
        return {unique: 0}
    },
    mounted() {
        this.load();
    },
    methods: {
        load: function () {
            api.get('metric?slug=' + this.slug).then((d) => {
                this.unique = d.data.unique;
                console.log(this.unique);
            }).catch(err => {
            })
        }

    }
});
