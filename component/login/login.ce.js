Vue.component('loginForm', {
    data: function () {
        return {
            username: '',
            password: '',
            loggedIn: localStorage.token,
            valid: true,
            localUser: {},
            state:'login'
        }
    },
    mounted() {
        if (this.loggedIn) {
            // check if still valid token
            api.get('register').then(x => {
                if(!x.data.phpSession){
                    this.logout();
                } else {
                    this.localUser = x.data.user;
                    localStorage.user = JSON.stringify(x.data.user);
                }
            }).catch(e => {
                this.loggedIn = false;
                this.logout();
            })
        }
    },
    methods: {
        logout() {
            if (this.loggedIn) {
                api.delete('login').then(res => {
                    this.updateStatus(false);
                })
            } else {
                this.updateStatus(false);
            }

        },
        login() {
            this.valid = true;

            api.post(this.state, this._data).then(res => {
                this.updateStatus(res.data.token);
                localStorage.setItem('user', JSON.stringify(res.data.user));
                this.localUser = res.data.user;
            }).catch(err => {
                this.valid = false;
            })
        },
        toRegistration() {
            window.location.href = '{{base}}register'
        },
        updateStatus(token) {
            if (token) {
                localStorage.token = token;
                this.loggedIn = token;
            } else {
                api.delete('login');
                delete localStorage.user;
                delete localStorage.token;
                this.localUser = {};
                this.loggedIn = false;
            }
        }
    },
    template: document.querySelector('#login').innerHTML
});
