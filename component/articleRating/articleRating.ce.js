
Vue.component('articleRating', {
    props: {
        articleId: String
    },
    data: function () {
        return {
            currentRating: 3,
            currentArticleId: this.articleId,
            total:0,
            ratedByMe: true,
            hoveredIndex:0,
            hovering:false
        }
    },
    mounted() {
        this.getRating();
    },
    methods: {
        setHover: function(index){
            this.hoveredIndex = index;
            this.hovering = true;
        },
        resetHover:function(){
            this.hoveredIndex = 0;
            this.hovering = false;
        },
        getRating: function () {
            api.get('articleRating?articleId=' + this.currentArticleId).then(res => {
                this.ratedByMe = res.data.ratedByMe;
                this.currentRating = res.data.rating.average;
                this.total = res.data.rating.total;
            })
        },
        rate:function(rating){
            if(!this.ratedByMe){
                api.post('articleRating',{
                    articleId:this.currentArticleId,
                    rating:rating
                }).then(()=>{
                    this.getRating();
                })
            }
        }
    },
    template: `<div class="user-interacting">
        <span v-for="index in 5" @mouseover="setHover(index)" @mouseleave="resetHover" class="highlight pointer" @click="rate(index)">
            <span v-if="(currentRating >= index && !hovering) || (hovering && hoveredIndex >= index)"><i class="material-icons">star</i></span>
            <span v-if="(index-1 < currentRating) && (index > currentRating) && !hovering"><i class="material-icons" >star_half</i></span>
            <span v-if="(currentRating+1 <= index && !hovering) || (hovering && hoveredIndex<index)"><i class="material-icons">star_border</i></span>
        </span>
        <div>{{total}} votes <span v-if="ratedByMe">(including YOU)</span></div>
        </div>`
});