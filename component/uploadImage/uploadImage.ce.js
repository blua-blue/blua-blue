Vue.component('uploadImage', {
    props:['maxSize','identifier'],
    data: function () {
        return {
            sizeWarning:false,
            image:{}
        }
    },
    mounted(){
        console.log(this.identifier);
    },
    methods: {
        upload($event, identifier) {
            if(identifier !== this.identifier){
                return;
            }
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
                        this.$emit('uploaded', {
                            imgId: res.data.uploadId,
                            identifier: this.identifier
                        });
                    })
                }).bind(this);
            }

        }
    },
    template:document.querySelector('#uploadImage').innerHTML
});