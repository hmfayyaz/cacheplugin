(function($) {

	$(function() {

		new FLBuilderTabs({
			id: '<?php echo $id; ?>',
			tabsOnMobile: '<?php echo $settings->tabs_on_mobile; ?>',
			activeTab: '<?php echo intval( $settings->active_tab ); ?>',
		});
	});

})(jQuery);
