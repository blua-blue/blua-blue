Vue.component('bluaModal', {
    data: function () {
        return {
            isActive: false,
            confirmable:false,
            content: 'No content provided',
            modalClass: 'is-link',
            headline: 'Note',
            object: {}
        }
    },
    mounted() {
        this.$root.$on('toggleModal', (args) => {
            Object.keys(args).forEach(arg => {
                this[arg] = args[arg];
            });
            this.toggle();
        })
    },
    methods: {
        confirm: function () {
            this.$root.$emit('modalConfirmed',this.object);
            this.toggle();
        },
        deny: function () {
            this.$root.$emit('modalDenied',this.object);
            this.toggle();
        },
        toggle: function () {
            this.isActive = !this.isActive;
            if(!this.isActive){
                // flush/reset
                this.confirmable = false;
                this.content = 'not content provided';
                this.modalClass = 'is-link';
                this.headline = 'Note';
                this.object = {};
            }
        }
    },
    template: document.querySelector('#bluaModal')
});
