Vue.component('loginForm', {
    data: function () {
        return {
            username: '',
            password: '',
            loggedIn: localStorage.token,
            valid: true,
            localUser: {}
        }
    },
    mounted() {
        if (this.loggedIn) {
            // check if still valid token
            api.get('register').then(x => {
                this.localUser = x.data;
                localStorage.user = JSON.stringify(x.data);
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
            api.post('login', this._data).then(res => {
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
