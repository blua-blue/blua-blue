const common = {
    methods: {
        testPassword: function (password) {
            let strength = Math.floor(password.length / 15 * 100);
            if(password.match(/\d/g)){
                strength += 8;
            } else {
                strength -= (5*password.length);
            }
            if(password.match(/[^A-Za-z0-9]/g)){
                strength += 4;
            }
            if(password.match(/^[0-9]*$/g)){
                strength -= (5*password.length);
            }
            return strength;
        },
        isLoggedIn: async function(){
            return api.get('register');
        }
    }
};
