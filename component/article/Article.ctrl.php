<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use Neoan3\Apps\Cache;
use Neoan3\Apps\Db;
use Neoan3\Apps\Ops;
use Neoan3\Apps\Session;
use Neoan3\Apps\SimpleTracker;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Neoan;
use Neoan3\Model\ArticleModel;
use Neoan3\Model\ImageModel;
use Neoan3\Model\IndexModel;
use Neoan3\Model\MetricsModel;
use Neoan3\Model\UserModel;
use Neoan3\Model\WebhookModel;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
/**
 * Class Article
 *
 * @package Neoan3\Components
 */
class Article extends Unicore
{
    /**
     * @var
     */
    private $frame;
    /**
     * @var array
     */
    private $vueElements = ['login', 'articleRating', 'comment', 'commentList', 'metric', 'article',];
    /**
     * @var
     */
    private $content;
    /**
     * @var string
     */
    private $view = 'article';

    /**
     *
     */
    function init()
    {
        $this->uni('neoan')
             ->callback($this, 'vueElements')
             ->callback($this, 'getContext')
             ->addHead('title', isset($this->content['name']) ? $this->content['name'] : 'Overview')
             ->hook('main', $this->view, $this->content)
             ->output();
    }

    /**
     * @param Neoan $uni
     */
    function vueElements($uni)
    {
        foreach ($this->vueElements as $vueElement) {
            $uni->vueComponent($vueElement);
        }

    }

    /**
     * @param $uni
     *
     * @return bool
     * @throws \Neoan3\Apps\DbException
     */
    function getContext($uni)
    {
        if (!sub(1)) {
            $this->general();
            return $uni;
        }
        $article = ArticleModel::bySlug(sub(1));
        // edge-case catch: Was author removed from DB?
        if(!isset($article['author']['id'])){
            Db::article(['delete_date'=>'.'],['id'=>'$'.$article['id']]);
            $this->general();
            return $uni;
        }

        // Is current viewer author?
        $loggedIn = false;
        if(Session::isLoggedIn()){
            $loggedIn = Session::userId();
        }

        if ((!$loggedIn || $article['author']['id'] !== $loggedIn) &&
            (empty($article) || $article['is_public'] !== 1 || empty($article['publish_date']))) {
            $this->general();
            return $uni;
        }
        // get metricscom
        $article['metrics'] = MetricsModel::visits();

        $article['renderedContent'] = '';
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $markdownConverter = new CommonMarkConverter(['html_input' => 'strip'], $environment);
        foreach ($article['content'] as $content) {
            switch ($content['content_type']){
                case 'markdown':
                    $article['renderedContent'] .= $markdownConverter->convertToHtml($content['content']);
                    break;
                case 'img':
                    $article['renderedContent'] .= '<img class="content-image" src="' . $content['content'] . '"/>';
                    break;
                default: $article['renderedContent'] .= $content['content'];
                    break;
            }

        }
        $article['imageTag'] = '';
        if (isset($article['image']['path'])) {
            $article['imageTag'] = '<img src="' . base . $article['image']['path'] . '" alt="">';
        }
        // related
        $others =
            ArticleModel::find(['category_id' => $article['category_id'], 'is_public' => '1', 'publish_date' => '!']);
        $article['related'] = '';
        foreach ($others as $other) {
            if ($other['id'] !== $article['id']) {
                if(!isset($other['image']['path'])){
                    $other['image']['path'] = '/asset/img/blua-blue-logo.png';
                }
                $article['related'] .= Ops::embraceFromFile('/component/article/related.html', $other);
            }

        }
        $article['author']['profilePicture'] = base . 'asset/img/blank-profile.png';
        if ($article['author']['image_id']) {
            $article['author']['profilePicture'] = base . ImageModel::byId($article['author']['image_id'])['path'];
        }
        // keywords
        $article['keywordString'] = '';
        if (!empty($article['keywords'])) {
            $keywords = explode(',', $article['keywords']);
            foreach ($keywords as $keyword) {
                $article['keywordString'] .= '<span class="tag is-light">' . $keyword . '</span> ';
            }
        }
        // comments
        $article['commentString'] = '';
        if (isset($article['comments'])) {
            foreach ($article['comments'] as $comment) {
                $comment['inserted'] = date('m/d/Y', strtotime($comment['insert_date']));
                $comment['author'] = UserModel::get($comment['user_id']);
                if (!$comment['author']['image_id']) {
                    $comment['author']['image'] = 'asset/img/blank-profile.png';
                }
                $article['commentString'] .= Ops::embraceFromFile('component/article/comment.html', $comment);
            }
        }


        $this->content = $article;
        $this->content['seo'] = json_encode($this->seo());

        return $uni;

    }

    /**
     * @throws \Neoan3\Apps\DbException
     */
    private function general()
    {
        $this->view = 'overview';
        $articleList = new ArticleList();
        $newest = $articleList->getArticleList(['limit' => 30]);
        $this->content['cards'] = '';
        foreach ($newest as $post) {
            $this->content['cards'] .= Ops::embraceFromFile('/component/article/card.html', $post);
        }

    }

    private function seo()
    {
        $seo =  [
            '@context'      => 'http://schema.org/',
            '@type'         => 'article',
            'name'          => $this->content['name'],
            'description'   => $this->content['teaser'],
            'author'        => $this->content['author']['user_name'],
            'keywords'      => $this->content['keywords'],
            'datePublished' => substr($this->content['insert_date'], 0, 10),
            'headline'      => $this->content['name'],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => 'blua.blue',
                'url'   => base,
                'logo'  => ['@type' => 'imageObject', 'url' => base . 'asset/img/blua-blue-logo.png']
            ]
        ];
        if(isset($this->content['image']['path'])){
            $seo['image'] = base . $this->content['image']['path'];
        }
        return $seo;
    }

    /* API */
    /**
     *
     */
    private function asApi()
    {
        $this->frame = new Neoan();
    }

    /**
     * Retrieves  an article
     * by various possible filters
     *
     * @param object $condition
     *  $condition['id'] optional
     *  $condition['publish_date'] optional
     *
     * @return array|mixed
     * @throws RouteException
     */
    function getArticle($condition)
    {
        $this->asApi();
        try{
            $jwt = Stateless::validate();
        } catch (\Exception $e){
            $jwt = ['jti' => 'no'];
        }
        $article = IndexModel::first(ArticleModel::find($condition));
        if (!empty($article)) {
            $this->content = $article;
            $article['seo'] = $this->seo();
            if ($article['author_user_id'] === $jwt['jti'] ||
                (!empty($article['publish_date']) && $article['is_public'] === 1)) {
                return $article;
            }
        }
        throw new RouteException('Not found or no permission', 404);
    }

    /**
     * Updates an article and expects the full article model.
     *
     * @param object $article
     *
     * @throws RouteException
     * @throws \Neoan3\Apps\DbException
     */
    function putArticle($article)
    {
        if (!isset($article['id'])) {
            throw new RouteException('Missing field "id"', 400);
        }

        $this->postArticle($article);
    }

    /**
     * Creates an article and expects the full article model.
     *
     * @param object $article
     *
     * @return array
     * @throws RouteException
     * @throws \Neoan3\Apps\DbException
     */
    function postArticle($article)
    {
        $this->asApi();
        $event = 'created';
        $jwt = Stateless::restrict();
        if (isset($article['id'])) {
            $event = 'updated';
            $oldArticle = ArticleModel::byId($article['id']);
            if (empty($oldArticle) || $oldArticle['author_user_id'] !== $jwt['jti']) {
                throw new RouteException('no permission', 403);
            }
            // delete possible outdated content blocks
            $this->removeDeletedContent($oldArticle['content'], $article['content']);
            // published?
            if (empty($oldArticle['publish_date']) && !$article['isDraft']) {
                Db::article(['publish_date' => '.'], ['id' => '$' . $article['id']]);
            }
            // ensure rights to update
            $articleId = $article['id'];
        } else {
            $slug = Ops::toKebabCase($article['name']);
            $exists = Db::ask('>SELECT id FROM article WHERE slug LIKE CONCAT({{slug}},"%")', ['slug' => $slug]);
            if (!empty($exists)) {
                $slug = $slug . '-' . (count($exists) + 1);
            }
            $articleId = Db::uuid()->uuid;
            $d = Db::article([
                'id'             => '$' . $articleId,
                'author_user_id' => '$' . $jwt['jti'],
                'slug'           => $slug,
                'publish_date'   => $article['isDraft'] ? '' : '.'
            ]);
        }
        $update = [
            'name'        => $article['name'],
            'teaser'      => $article['teaser'],
            'is_public'   => $article['public'],
            'category_id' => '$' . $article['category_id'],
            'keywords'    => '=' . implode(',', $article['keywords'])
        ];
        Db::article($update, ['id' => '$' . $articleId]);
        if (isset($article['image']['id'])) {
            Db::article(['image_id' => '$' . $article['image']['id']], ['id' => '$' . $articleId]);
        }
        foreach ($article['content'] as $i => $content) {
            $contentRow = [
                'article_id' => '$' . $articleId,
                'sort'       => $i + 1,
                'content_type' => $content['content_type'],
                'content'    => '=' . $content['content']
            ];
            if (isset($content['id'])) {
                Db::article_content($contentRow, ['id' => '$' . $content['id']]);
            } else {
                Db::article_content($contentRow);
            }

        }
        Cache::invalidate('article');
        $webhooks = WebhookModel::send($jwt['jti'], ArticleModel::byId($articleId), $event, $article['webhooks']);

        return ['id' => $articleId, 'webhooks' => $webhooks];
    }

    /**
     * Deletes an article
     *
     * @param object $body
     *  $body['id']
     *
     * @return bool
     * @throws RouteException
     * @throws \Neoan3\Apps\DbException
     */
    function deleteArticle($body)
    {
        $this->asApi();
        $jwt = Stateless::restrict();
        $condition = ['id' => '$' . $body['id']];
        // is admin?
        $user = UserModel::get($jwt['jti']);
        if ($user['user_type'] !== 'admin') {
            $condition['author_user_id'] = '$' . $jwt['jti'];
        }
        $find = Db::easy('article.id', $condition);
        if (empty($find)) {
            throw new RouteException('no permission', 403);
        }
        Db::delete('article', $body['id']);
        Cache::invalidate('article');
        WebhookModel::send($jwt['jti'], ['id' => $body['id']], 'deleted');
        return true;
    }
    private function removeDeletedContent($oldContent, $newContent){
        foreach ($oldContent as $oldBlock){
            $found = false;
            foreach ($newContent as $newBlock){
                if(isset($newBlock['id']) && $newBlock['id'] === $oldBlock['id']){
                    $found = true;
                }
            }
            if(!$found){
                Db::delete('article_content', $oldBlock['id']);
            }
        }
    }

}
