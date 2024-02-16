<div class="wrapper wpssg-description_wrapper">
	<h1>Generate static XML sitemap</h1>
	<div class="static-sitemap-about-text">
		This plugin is born because of struggle for memory limits.
	</div>
    <div class="static-sitemap-success-msg" style="display: none;"></div>
    <div class="static-sitemap-error-msg" style="display: none;"></div>
    <form action="" method="post" id="static-sitemap_GenerateForm">
        <input type="hidden" name="action" value="wpssg_ajax_generate" />
        <input type="hidden" name="remaining_posts" class="remaining_posts" value="" />
        <input type="hidden" name="nonce" value="<?=wp_create_nonce('wpssg-ajax-nonce')?>" />
        <button id="generate_button" type="button" name="static_sitemap_GenerateButton">Generate sitemap</button>
    </form>
</div>
