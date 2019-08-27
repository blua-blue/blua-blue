Vue.component('categoryList', {
    data: function () {
        return {
            loadedCategories: [],
            newC:{
                name:''
            }
        }
    },
    methods:{
        addCategory(){
            api.post('categories',this.newC).then(()=>{
                this.loadCategories();
                this.newC.name = '';
            })
        },
        deleteCategory(id){
            api.delete('categories?id='+id).then(()=>{
                this.loadCategories();
            })
        },
        loadCategories(){
            api.get('categories').then(categories => {
                this.loadedCategories = categories.data;
                console.log(this.loadedCategories);
            }).catch(err=>{
                console.log(err);
            });
        }
    },
    mounted() {
        this.loadCategories();
    },
    template: document.querySelector('#categories').innerHTML
});