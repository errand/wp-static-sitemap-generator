<?php

use GpsLab\Component\Sitemap\Builder\Url\UrlBuilder;
use GpsLab\Component\Sitemap\Url\ChangeFrequency;
use GpsLab\Component\Sitemap\Url\Url;

class WPSiteUrlBuilder implements UrlBuilder
{
    public function getIterator(): \Traversable
    {
        $args = array(
            'post_type' => array('post','page'),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'ignore_sticky_posts' => true,
        );
        $qry = new WP_Query($args);

        $urls = [];

        foreach ($qry->posts as $post) {
            $url = Url::create(
                get_permalink($post->ID), // loc
                new \DateTimeImmutable($post->post_date), // lastmod
                ChangeFrequency::always(), // changefreq
                10 // priority
            );

            $urls[] = $url;
        }

        // add URLs on your site
        return new \ArrayIterator($urls);
    }
}