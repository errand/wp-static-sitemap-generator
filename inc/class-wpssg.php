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

    /**
     * Constructor
     * @param array $args
     * @throws Exception
     */
    public function generate(
        $index_filename,
        $part_filename,
        $part_web_path,
        $args = array(
                'post_type' => array('post','page'),
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'ignore_sticky_posts' => true,
            ))
    {
        $qry = new WP_Query($args);

        $urls = [];

        foreach ($qry->posts as $post) {
            $url = Url::create(
                get_permalink($post->ID), // loc
                new \DateTimeImmutable(date('c', get_post_timestamp($post->ID))), // lastmod
                ChangeFrequency::always(), // changefreq
                10 // priority
            );

            $urls[] = $url;
        }

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
        foreach ($urls as $url) {
            $stream->push($url);
        }

        $stream->close();
    }
}
