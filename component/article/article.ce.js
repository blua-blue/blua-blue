new Vue({
    el:'#article',
    data:{
        isMine:false
    },
    update(){
        this.evaluateIfMine();
    },
    mounted(){

        this.evaluateIfMine();
    },
    methods:{
        evaluateIfMine(){
            if(typeof localStorage.user !== 'undefined'){
                let user = JSON.parse(localStorage.user);

                let author = document.querySelector('#author-id');
                if (author) {
                    this.isMine = author.value === user.id;
                }
            }

        }
    }
});