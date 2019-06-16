Vue.directive('article-search',{
    update: function(el, binding){
        console.log(binding.value);
        // api.get()
    }
});
