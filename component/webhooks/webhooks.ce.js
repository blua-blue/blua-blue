Vue.component('webhooks', {
    data: function(){
        return {
            webhooks:[],
            newWebhook:{
                target_url:'',
                token:''
            }
        }
    },
    template: document.querySelector('#webhooks'),
    mounted(){
        this.loadWebhooks();
    },
    methods:{
        loadWebhooks:function(){
            api.get('webhooks').then(webhooks =>{
                this.webhooks = webhooks.data;
            })
        },
        deleteWebhook:function(id){
            api.delete('webhooks?id='+id).then(_ =>{
                this.loadWebhooks();
            })
        },
        addWebhook: function(){
            api.post('webhooks',this.newWebhook).then(()=>{
                this.loadWebhooks();
                this.newWebhook = { target_url: '', token: ''}
            })
        }
    }
});
