<div class="wrapper static-site-description_wrapper">
	<h1>Generate static XML sitemap</h1>
	<div class="static-sitemap-about-text">
		This plugin is born because of struggle for memory limits.
	</div>
    <div class="static-sitemap-success-msg" style="display: none;"></div>
    <div class="static-sitemap-error-msg" style="display: none;"></div>
    <form action="" method="post" id="static-sitemap_GenerateForm">
        <input type="hidden" name="action" value="static_sitemapAjaxGenerate" />
        <input type="hidden" name="remaining_posts" class="remaining_posts" value="" />
        <input type="hidden" name="nonce" value="<?=wp_create_nonce('wp-static-sitemap-generator-ajax-nonce')?>" />
        <input type="submit" name="static_sitemap_GenerateButton" value="Generate sitemap">
    </form>
</div>