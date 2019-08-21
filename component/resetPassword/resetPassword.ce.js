Vue.component('reset-password', {
    mixins:[common],
    data: function () {
        return {
            password:'',
            passwordRepeat:'',
            confirmCode:'{{hash}}',
            userId:'{{userId}}',
            passwordStrength:0,
            wasSuccess:false
        }
    },

    watch: {
        password: function(value){
            this.passwordStrength = this.testPassword(value);
        }
    },
    methods:{
        updatePassword:function () {
            api.put('register',this._data).then(res =>{
                this.wasSuccess = true;
            }).catch(()=>{
                alert('Something went wrong. Please contact support "contact@blua.blue"')
            })
        }
    },
    template: document.querySelector('#resetPassword').innerHTML
});
