Vue.component('uploadImage', {
    props:['maxSize'],
    data: function () {
        return {
            sizeWarning:false,
            image:{}
        }
    },
    methods: {
        upload($event) {
            if($event.target.files[0].size>Number(this.maxSize)*1024){
                this.sizeWarning = true;
            } else {
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


        }
    },
    template:document.querySelector('#uploadImage').innerHTML
});