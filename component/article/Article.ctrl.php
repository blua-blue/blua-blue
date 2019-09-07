<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

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
    private $vueElements = ['login', 'articleRating', 'comment', 'commentList', 'article',];
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
     * @return bool
     * @throws \Neoan3\Apps\DbException
     */
    function getContext()
    {
        if (!sub(1)) {
            $this->general();
            return true;
        }
        $article = ArticleModel::bySlug(sub(1));
        if (empty($article) || $article['is_public'] !== 1 || empty($article['publish_date'])) {
            $this->general();
        }
        // get metrics
        $article['metrics'] = MetricsModel::visits();

        $article['renderedContent'] = '';
        foreach ($article['content'] as $content) {
            $article['renderedContent'] .= $content['content'];
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
                $comment['author'] = UserModel::byId($comment['user_id']);
                if (!$comment['author']['image_id']) {
                    $comment['author']['image'] = 'asset/img/blank-profile.png';
                }
                $article['commentString'] .= Ops::embraceFromFile('component/article/comment.html', $comment);
            }
        }


        $this->content = $article;
        $this->content['seo'] = json_encode($this->seo());

        return true;

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
        return [
            '@context'      => 'http://schema.org/',
            '@type'         => 'article',
            'name'          => $this->content['name'],
            'description'   => $this->content['teaser'],
            'author'        => $this->content['author']['user_name'],
            'keywords'      => $this->content['keywords'],
            'datePublished' => substr($this->content['insert_date'], 0, 10),
            'headline'      => $this->content['name'],
            'image'         => base . $this->content['image']['path'],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => 'blua.blue',
                'url'   => base,
                'logo'  => ['@type' => 'imageObject', 'url' => base . 'asset/img/blua-blue-logo.png']
            ]
        ];
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
     * Retrieves  an article with
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
        $jwt = Stateless::restrict();
        $article = IndexModel::first(ArticleModel::find($condition));
        if(!empty($article)){
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
        $jwt = Stateless::restrict();
        if (isset($article['id'])) {
            $oldArticle = ArticleModel::byId($article['id']);
            if (empty($oldArticle) || $oldArticle['author_user_id'] !== $jwt['jti']) {
                throw new RouteException('no permission', 403);
            }
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
            Db::article([
                'id'             => '$' . $articleId,
                'author_user_id' => '$' . $jwt['jti'],
                'slug'           => $slug,
                'publish_date'   => $article['isDraft'] ? '' : '.'
            ]);
        }
        $update = [
            'name'        => $article['name'],
            'teaser'      => $article['teaser'],
            'is_public'      => $article['public'],
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
                'content'    => '=' . $content['content']
            ];
            if (isset($content['id'])) {
                Db::article_content($contentRow, ['id' => '$' . $content['id']]);
            } else {
                Db::article_content($contentRow);
            }

        }
        Cache::invalidate('article');

        return ['id' => $articleId];
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
        $user = UserModel::byId($jwt['jti']);
        if ($user['user_type'] !== 'admin') {
            $condition['author_user_id'] = $jwt['jti'];
        }
        $find = Db::easy('article.id', $condition);
        if (empty($find)) {
            throw new RouteException('no permission', 403);
        }
        Db::delete('article', $body['id']);
        Cache::invalidate('article');
        return true;
    }

}
