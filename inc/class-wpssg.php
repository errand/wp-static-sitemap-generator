<?php

/**
 * This plugin is based on https://github.com/gpslab/sitemap
 */

use GpsLab\Component\Sitemap\Render\PlainTextSitemapIndexRender;
use GpsLab\Component\Sitemap\Render\PlainTextSitemapRender;
use GpsLab\Component\Sitemap\Stream\WritingSplitIndexStream;
use GpsLab\Component\Sitemap\Url\ChangeFrequency;
use GpsLab\Component\Sitemap\Url\Url;
use GpsLab\Component\Sitemap\Writer\TempFileWriter;

class WPSSG {

    public function __construct()
    {
        $this->posts_count = $this->count_posts();
        $this->offset = 0;
        $this->urls = [];
    }

    public function generate(
        $index_filename,
        $part_filename,
        $part_web_path)
    {

        if( $this->offset > $this->posts_count ) {
            $index_render = new PlainTextSitemapIndexRender();
            $index_writer = new TempFileWriter();

            $part_render = new PlainTextSitemapRender();
            $part_writer = new TempFileWriter();

            $stream = new WritingSplitIndexStream(
                $index_render,
                $part_render,
                $index_writer,
                $part_writer,
                $index_filename,
                $part_filename,
                $part_web_path
            );

            // build sitemap
            $stream->open();
            foreach ($this->urls as $url) {
                $stream->push($url);
            }

            $stream->close();

        } else {

            $args = array(
                'post_type' => array('post','page'),
                'post_status' => 'publish',
                'posts_per_page' => $this->offset,
                'ignore_sticky_posts' => true,
                'fields' => 'ids',
            );

            $qry = get_posts($args);

            foreach ($qry->posts as $post) {
                $url = Url::create(
                    get_permalink($post->ID), // loc
                    new \DateTimeImmutable(date('c', get_post_timestamp($post->ID))), // lastmod
                    ChangeFrequency::always(), // changefreq
                    10 // priority
                );

                $this->urls[] = $url;
            }

            $this->offset += 1000;
        }
    }

    function count_posts()
    {
        $args = array(
            'post_type' => array('post','page'),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'ignore_sticky_posts' => true,
            'fields' => 'ids',
        );

        $qry = new WP_Query($args);
        return $qry->found_posts;
    }
}
