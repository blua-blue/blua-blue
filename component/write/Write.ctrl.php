<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Db;
use Neoan3\Apps\Ops;
use Neoan3\Apps\Session;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Neoan;
use Neoan3\Model\ArticleModel;

class Write extends Unicore {
    private $vueElements = ['uploadImage','bluaModal', 'write'];
    function init() {

        $this->uni('neoan')->callback($this,'vueElements')
//             ->includeElement('write', ['loadedArticleId' => sub(1)])
             ->includeJs('node_modules/@tinymce/tinymce-vue/lib/browser/tinymce-vue.min.js')
             ->hook('main', 'write')
             ->callback($this, 'secure')
             ->output();
    }

    /**
     * @param Neoan $uni
     */
    function vueElements($uni) {
        foreach ($this->vueElements as $vueElement) {
            $uni->vueComponent($vueElement, ['loadedArticleId' => sub(1)]);
        }

    }

    function secure($uni) {
        Session::restricted();
    }

    private function asApi() {
        new Neoan();
    }

    /**
     * @param $obj
     *
     * @return array|mixed
     * @throws RouteException
     */
    function getWrite($obj) {
        $this->asApi();
        $jwt = Stateless::restrict();
        $article = ArticleModel::byId($obj['id']);
        if (empty($article) || $article['author_user_id'] !== $jwt['jti'] || !empty($article['delete_date'])) {
            throw new RouteException('no permission', 403);
        }
        return $article;
    }

}
