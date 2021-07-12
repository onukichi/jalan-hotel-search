<?php
namespace App\Helpers;

class SeoHelper
{
    public function __construct(
    ) {
    }

    public static function setIndexSeo()
    {
        return self::setDefaultSeo();
    }

    public static function setHotelsIndexSeo()
    {
        $appName     = config('app.name');
        $title       = trans('seo.hotels.index.title');
        $description = trans('seo.hotels.index.description');

        $keyWords        = trans('seo.hotels.index.keywords');
        $imageTwitter    = config('app.ogp_image');
        $imageFacebook   = config('app.ogp_image');
        $twitterCardType = 'summary_large_image';

        empty($twitterCardType) ?: \Twitter::setType($twitterCardType);

        empty($title) ?: \SEOMeta::setTitle($title, false);
        empty($title) ?: \OpenGraph::setTitle($title);
        empty($title) ?: \Twitter::setTitle($title);

        empty($keyWords) ?: \SEOMeta::setKeywords($keyWords);

        empty($description) ?: \SEOMeta::setDescription($description);
        empty($description) ?: \OpenGraph::setDescription($description);
        empty($description) ?: \Twitter::setDescription($description);

        empty($imageFacebook) ?: \OpenGraph::addImage($imageFacebook);
        empty($imageTwitter) ?: \Twitter::setImage($imageTwitter);
    }

    public static function setHotelsShowSeo($model)
    {
        $appName      = config('app.name');
        $title        = $model->name.' | '.config('app.name');
        $keyWords     = trans('seo.index.keywords');
        $description  = $model->caption;

        $imgUrl        = \ImageHelper::getMainImage($model->id);
        $imageTwitter  = is_null($imgUrl) ? config('app.ogp_image') : $imgUrl;
        $imageFacebook = is_null($imgUrl) ? config('app.ogp_image') : $imgUrl;

        return self::setSeo($title, $keyWords, $description, $imageFacebook, $imageTwitter);
    }

    public static function setShowSeo($model, $title, $description = null, $imgUrl = null)
    {
        $appName      = config('app.name');
        $title        = trans('seo.show.title').'  |  '.$title;
        $keyWords     = trans('seo.index.keywords');

        $imageTwitter  = is_null($imgUrl) ? config('app.ogp_image') : $imgUrl;
        $imageFacebook = is_null($imgUrl) ? config('app.ogp_image') : $imgUrl;

        return self::setSeo($title, $keyWords, $description, $imageFacebook, $imageTwitter);
    }

    public static function setDefaultSeo()
    {
        $appName     = config('app.name');
        $title       = trans('seo.index.title');
        $description = trans('seo.index.description');

        $keyWords        = trans('seo.index.keywords');
        $imageTwitter    = config('app.ogp_image');
        $imageFacebook   = config('app.ogp_image');
        $twitterCardType = 'summary_large_image';

        empty($twitterCardType) ?: \Twitter::setType($twitterCardType);

        empty($title) ?: \SEOMeta::setTitle($title, false);
        empty($title) ?: \OpenGraph::setTitle($title);
        empty($title) ?: \Twitter::setTitle($title);

        empty($keyWords) ?: \SEOMeta::setKeywords($keyWords);

        empty($description) ?: \SEOMeta::setDescription($description);
        empty($description) ?: \OpenGraph::setDescription($description);
        empty($description) ?: \Twitter::setDescription($description);

        empty($imageFacebook) ?: \OpenGraph::addImage($imageFacebook);
        empty($imageTwitter) ?: \Twitter::setImage($imageTwitter);
    }

    public static function setSeo($title, $keyWords, $description, $imageFacebook, $imageTwitter)
    {
        self::setSeoText($title, $keyWords, $description);
        empty($imageFacebook) ?: \OpenGraph::addImage($imageFacebook);
        empty($imageTwitter) ?: \Twitter::setImage($imageTwitter);
    }

    public static function setSeoText($title, $keyWords, $description)
    {
        $twitterCardType = 'summary_large_image';
        empty($twitterCardType) ?: \Twitter::setType($twitterCardType);

        empty($title) ?: \SEOMeta::setTitle($title, false);
        empty($title) ?: \OpenGraph::setTitle($title);
        empty($title) ?: \Twitter::setTitle($title);

        empty($keyWords) ?: \SEOMeta::setKeywords($keyWords);

        empty($description) ?: \SEOMeta::setDescription($description);
        empty($description) ?: \OpenGraph::setDescription($description);
        empty($description) ?: \Twitter::setDescription($description);
    }

    public static function setAboutSeo($title)
    {
        $appName     = config('app.name');
        $title       = trans('seo.about.title', [
            'title' => $title,
        ]);
        $description = trans('seo.about.description');

        $keyWords        = trans('seo.about.keywords');
        $imageTwitter    = config('app.ogp_image');
        $imageFacebook   = config('app.ogp_image');
        $twitterCardType = 'summary_large_image';

        empty($twitterCardType) ?: \Twitter::setType($twitterCardType);

        empty($title) ?: \SEOMeta::setTitle($title, false);
        empty($title) ?: \OpenGraph::setTitle($title);
        empty($title) ?: \Twitter::setTitle($title);

        empty($keyWords) ?: \SEOMeta::setKeywords($keyWords);

        empty($description) ?: \SEOMeta::setDescription($description);
        empty($description) ?: \OpenGraph::setDescription($description);
        empty($description) ?: \Twitter::setDescription($description);

        empty($imageFacebook) ?: \OpenGraph::addImage($imageFacebook);
        empty($imageTwitter) ?: \Twitter::setImage($imageTwitter);
    }
}
