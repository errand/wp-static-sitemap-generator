<div class="wrapper static-site-description_wrapper">
	<h1>Generate static XML sitemap</h1>
	<div class="static-sitemap-about-text">
		This plugin is born because of struggle for memory limits.
	</div>
    <div class="static-sitemap-success-msg" style="display: none;"></div>
    <div class="static-sitemap-error-msg" style="display: none;"></div>
    <form method="post" id="static-sitemap_GenerateForm">
        <input type="hidden" name="action" value="static_sitemapAjaxGenerate" />
        <input type="hidden" name="remaining_posts" class="remaining_posts" value="" />
        <input type="hidden" name="nonce" value="<?=wp_create_nonce('static-sitemap-ajax-nonce')?>" />
        <input type="submit" name="static_sitemap_GenerateButton" value="Generate sitemap">
    </form>
    <div class="wrapper dcsLoader" style="display: none;">
        <div class="wp_dummy_content_generatorLoaderWrpper c100 p0 blue">
            <span class="wp_dummy_content_generatorLoaderPer">0%</span>
            <div class="slice">
                <div class="bar"></div>
                <div class="fill"></div>
            </div>
        </div>
    </div>
</div>