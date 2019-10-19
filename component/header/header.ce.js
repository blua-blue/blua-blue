Vue.component('bluaHeader', {
    template: document.querySelector('#header'),
    data: function () {
        return {
            text: '',
            searchResults: [],
            menuIsOpen: false,
            dropdownIsOpen: false,
            loggedIn: localStorage.token
        }
    },
    watch: {
        text: function (newV, oldV) {
            this.debounceSearch();
        }
    },
    mounted: function () {
        this.debounceSearch = _.debounce(this.ajaxSearch, 300);
        this.$root.$on('login', () => {
            this.loggedIn = localStorage.token
        })
    },
    methods: {
        toggleMenu() {
            this.menuIsOpen = !this.menuIsOpen;
        },
        toggleDropdown() {
            this.dropdownIsOpen = !this.dropdownIsOpen;
        },
        ajaxSearch: function () {
            api.get('search?q=' + this.text).then(res => {
                this.searchResults = res.data;
            })
        },
        logOut: function () {
            api.delete('login');
            delete localStorage.user;
            delete localStorage.token;
            this.$root.$emit('login', false);
        }
    }
});
