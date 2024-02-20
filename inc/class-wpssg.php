<?php

/**
 * This plugin is based on https://github.com/gpslab/sitemap
 */

use GpsLab\Component\Sitemap\Render\PlainTextSitemapIndexRender;
use GpsLab\Component\Sitemap\Render\PlainTextSitemapRender;
use GpsLab\Component\Sitemap\Sitemap\Sitemap;
use GpsLab\Component\Sitemap\Stream\WritingIndexStream;
use GpsLab\Component\Sitemap\Stream\WritingStream;
use GpsLab\Component\Sitemap\Url\ChangeFrequency;
use GpsLab\Component\Sitemap\Url\Url;
use GpsLab\Component\Sitemap\Writer\TempFileWriter;

class WPSSG {

    public function __construct($index_filename, $increment = 500, $sleep = 1)
    {
        $this->posts_count = $this->count_posts();
        $this->increment = $increment;
        $this->iterator = 0;
        $this->sleep = $sleep;
        $this->offset = 0;

        $this->index_filename = $index_filename;
    }

    public function generate()
    {
        $urls = [];

        if( $this->offset > $this->posts_count ) {

            $this->writeSourse();

            wp_send_json_success();

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

                $urls[] = $url;
            }

            $this->offset += $this->increment;
            $this->iterator += 1;
            $this->simpleWriter($urls, $this->iterator);

            sleep($this->sleep);

            $this->generate();
        }
    }

    private function writeSourse()
    {
        // configure stream
        $render = new PlainTextSitemapIndexRender();
        $writer = new TempFileWriter();
        $stream = new WritingIndexStream($render, $writer, $this->index_filename);

        // build sitemap.xml index
        $stream->open();
        for($i = 1; $i < $this->iterator; $i++){
            $stream->pushSitemap(new Sitemap(WP_HOME . '/sitemap'.$i.'.xml', new \DateTimeImmutable('-1 hour')));

        }
        $stream->close();
    }

    private function simpleWriter($urls, $iteration)
    {
        // file into which we will write a sitemap
        $filename = ABSPATH .'/xml-sitemap/sitemap'.$iteration.'.xml';

        // configure stream
        $render = new PlainTextSitemapRender();
        $writer = new TempFileWriter();
        $stream = new WritingStream($render, $writer, $filename);

        // build sitemap.xml
        $stream->open();
        foreach ($urls as $url) {
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