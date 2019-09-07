Vue.component('metric', {
    props: ['slug', 'initialValue'],
    template: document.querySelector('#metric'),
    data: function () {
        return {unique: this.initialValue || 0}
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
