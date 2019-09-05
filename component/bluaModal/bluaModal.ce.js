Vue.component('bluaModal', {
    data: function(){
        return {
            isActive:false,
            content:'No content provided',
            modalClass:'is-link',
            headline:'Note'
        }
    },
    mounted(){
        this.$root.$on('toggleModal',(args)=>{
            Object.keys(args).forEach(arg =>{
                this[arg] = args[arg];
            });
            this.toggle();
        })
    },
    methods:{
        toggle:function(){
            this.isActive = !this.isActive;
        }
    },
    template: document.querySelector('#bluaModal')
});
