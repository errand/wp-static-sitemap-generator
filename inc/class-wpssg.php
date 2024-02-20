<?php

/**
 * This plugin is based on https://github.com/gpslab/sitemap
 */

class WPSSG {

    public function __construct($index_filename, $increment = 1000, $sleep = 1)
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

            $this->offset += $this->increment;
            $this->iterator += 1;

            $filename = ABSPATH .'/xml-sitemap/sitemap'.$this->iterator.'.xml';
            $writer = new XMLWriter();
            $writer->openURI($filename);
            $writer->startDocument("1.0");
            $writer->startElement('urlset');
            $writer->startAttribute('xmlns:xsi');
            $writer->text('http://www.w3.org/2001/XMLSchema-instance');
            $writer->endAttribute();
            $writer->startAttribute('xsi:schemaLocation');
            $writer->text('http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
            $writer->endAttribute();
            $writer->startAttribute('xmlns');
            $writer->text('http://www.sitemaps.org/schemas/sitemap/0.9');
            $writer->endAttribute();


            foreach ($qry as $id) {
                $writer->startElement('url');
                $writer->startElement("loc");
                $writer->text(get_permalink($id));
                $writer->endElement();
                $writer->startElement("lastmod");
                $writer->text(get_the_date('c', $id));
                $writer->endElement();
                $writer->startElement("changefreq");
                $writer->text('monthly');
                $writer->endElement();
                $writer->startElement("priority");
                $writer->text('1');
                $writer->endElement();
                $writer->endElement();
            }

            $writer->endDocument();
            $writer->flush();

            $this->generate();
        }
    }
    private function writeSourse()
    {
        $filename = ABSPATH .'static-sitemap.xml';
        $writer = new XMLWriter();
        $writer->openURI($filename);

        $writer->startDocument("1.0");
        $writer->startElement('sitemapindex');
        $writer->startAttribute('xmlns:xsi');
        $writer->text('http://www.w3.org/2001/XMLSchema-instance');
        $writer->endAttribute();
        $writer->startAttribute('xsi:schemaLocation');
        $writer->text('http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $writer->endAttribute();
        $writer->startAttribute('xmlns');
        $writer->text('http://www.sitemaps.org/schemas/sitemap/0.9');
        $writer->endAttribute();


        for($i = 1; $i < $this->iterator; $i++){
            $writer->startElement("sitemap");
            $writer->startElement("loc");
            $writer->text(WP_HOME . '/sitemap'.$i.'.xml');
            $writer->endElement();
            $writer->startElement("lastmod");
            $writer->text(date('c'));
            $writer->endElement();
            $writer->endElement();
        }

        $writer->endDocument();
        $writer->flush();
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