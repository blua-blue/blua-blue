Vue.component('uploadImage', {
    data: function () {
        return {
            image:{}
        }
    },
    methods: {
        upload($event) {
            let reader = new FileReader();
            reader.readAsDataURL($event.target.files[0]);
            reader.onload = (function (data) {
                this.image = data.target.result;
                api.post('uploadImage',{
                    image:this.image
                }).then(res=> {
                    this.$emit('uploaded',res.data.uploadId);
                })
            }).bind(this);
        }
    },
    template:document.querySelector('#uploadImage').innerHTML
});