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

    public function __construct($index_filename, $part_filename, $part_web_path, $increment = 500, $sleep = 1)
    {
        $this->posts_count = $this->count_posts();
        $this->increment = $increment;
        $this->sleep = $sleep;
        $this->offset = 0;
        $this->urls = [];

        $this->index_filename = $index_filename;
        $this->part_filename = $part_filename;
        $this->part_web_path = $part_web_path;
    }

    public function generate()
    {
        if( $this->offset > $this->posts_count ) {

            $this->writeSourse();

            wp_send_json_success( [
                'offset' => $this->offset,
                'posts_count' => $this->posts_count,
                'urls' => $this->urls,
            ] );

        } else {

            $args = array(
                'post_type' => array('post','page'),
                'post_status' => 'publish',
                'posts_per_page' => $this->increment,
                'offset' => $this->offset,
                'ignore_sticky_posts' => true,
                'fields' => 'ids',
            );

            $qry = get_posts($args);

            foreach ($qry as $id) {
                $url = Url::create(
                    get_permalink($id), // loc
                    new \DateTimeImmutable(date('c', get_post_timestamp($id))), // lastmod
                    ChangeFrequency::always(), // changefreq
                    10 // priority
                );

                $this->urls[] = $url;
            }

            $this->offset += $this->increment;

            sleep($this->sleep);

            $this->generate();
        }
    }

    public function writeSourse()
    {
        $index_render = new PlainTextSitemapIndexRender();
        $index_writer = new TempFileWriter();

        $part_render = new PlainTextSitemapRender();
        $part_writer = new TempFileWriter();

        $stream = new WritingSplitIndexStream(
            $index_render,
            $part_render,
            $index_writer,
            $part_writer,
            $this->index_filename,
            $this->part_filename,
            $this->part_web_path
        );

        // build sitemap
        $stream->open();
        foreach ($this->urls as $url) {
            $stream->push($url);
        }

        $stream->close();
    }

    public function count_posts(): int
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