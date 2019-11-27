Vue.component('tinyMce', {
    template: document.querySelector('#tinyMce'),
    props:['content'],
    components: {
        'editor': Editor
    }
});
