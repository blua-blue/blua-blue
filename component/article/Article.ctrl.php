<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Db;
use Neoan3\Apps\Ops;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Neoan;
use Neoan3\Model\ArticleModel;
use Neoan3\Model\IndexModel;

class Article extends Unicore {
    private $vueElements = ['login'];
    private $content;
    private $view = 'article';

    function init() {
        $this->uni('neoan')
             ->callback($this, 'vueElements')
             ->callback($this, 'getContext')
             ->hook('main', $this->view,$this->content)
             ->output();
    }

    /**
     * @param Neoan $uni
     */
    function vueElements($uni) {
        foreach ($this->vueElements as $vueElement) {
            $uni->vueComponent($vueElement);
        }

    }
    function getContext(){
        if(!sub(1)){
            $this->general();
            return true;
        }
        $article = ArticleModel::bySlug(sub(1));
        if(empty($article) || $article['is_public'] !== 1 || empty($article['publish_date'])){
            $this->general();
        }
        $article['renderedContent'] = '';
        foreach ($article['content'] as $content){
            $article['renderedContent'] .= $content['content'];
        }
        $this->content = $article;
        return true;

    }
    private function general(){
        $this->view = 'overview';
        $articleList = new ArticleList();
        $newest = $articleList->getArticleList(['limit'=>30]);
        $this->content['cards'] = '';
        foreach($newest as $post){
            $this->content['cards'] .= Ops::embraceFromFile('/component/article/card.html',$post);
        }

    }
    /* API */
    private function asApi() {
        new Neoan();
    }

    /**
     * @param $condition
     *
     * @return array|mixed
     * @throws RouteException
     */
    function getArticle($condition){
        $this->asApi();
        $jwt = Stateless::restrict();
        $article = IndexModel::first(ArticleModel::find($condition));
        if(!empty($article) && $article['author_user_id'] === $jwt['jti'] || (!empty($article['publish_date'])&&$article['is_public']===1)){
            return $article;
        }
        throw new RouteException('Not found or no permission',404);
    }
    function postArticle($article){
        $this->asApi();
        $jwt = Stateless::restrict();
        if (isset($article['id'])) {
            $oldArticle = ArticleModel::byId($article['id']);
            if(empty($oldArticle) || $oldArticle['author_user_id'] !== $jwt['jti']){
                throw new RouteException('no permission',403);
            }
            // ensure rights to update
            $articleId = $article['id'];
        } else {
            $slug = Ops::toKebabCase($article['name']);
            $exists = Db::ask('>SELECT id FROM article WHERE slug LIKE CONCAT({{slug}},"%")',['slug'=>$slug]);
            if(!empty($exists)){
                $slug = $slug . '-'.(count($exists)+1);
            }
            $articleId = Db::uuid()->uuid;
            Db::article([
                            'id' => '$' . $articleId,
                            'author_user_id' => '$' . $jwt['jti'],
                            'name' => $article['name'],
                            'slug' =>$slug,
                            'teaser' => $article['teaser'],
                            'category_id' => '$' . $article['category_id'],
                            'is_public' => $article['public'],
                            'publish_date' => $article['isDraft'] ? '' : '.'
                        ]);
        }
        foreach ($article['content'] as $i =>$content) {
            $contentRow = [
                'article_id' => '$' . $articleId,
                'sort' => $i+1,
                'content' => '=' . $content['content']
            ];
            if(isset($content['id'])){
                Db::article_content($contentRow,['id'=>'$'.$content['id']]);
            } else {
                Db::article_content($contentRow);
            }

        }


        return ['id'=>$articleId];
    }

}
