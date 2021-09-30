var colorElements = [];
var yndformSwitch = {
	progress_indicator_none_show: [],
	progress_indicator_none_hide: [
		'progress_indicator_step-wrapper',
		'progress_indicator_bar-wrapper',
		'text_color-wrapper',
		'background_color-wrapper',
		'page_names_heading-wrapper',
		'yndform_page_names-wrapper',
		'yndform_page_names',
	],
	progress_indicator_bar_show: [
		'progress_indicator_bar-wrapper',
		'text_color-wrapper',
		'background_color-wrapper',
		'page_names_heading-wrapper',
		'yndform_page_names-wrapper',
		'yndform_page_names',
	],
	progress_indicator_bar_hide: [
		'progress_indicator_none-wrapper',
		'progress_indicator_step-wrapper',
	],
	progress_indicator_step_show: [
		'progress_indicator_step-wrapper',
		'text_color-wrapper',
		'background_color-wrapper',
		'page_names_heading-wrapper',
		'yndform_page_names-wrapper',
		'yndform_page_names',
	],
	progress_indicator_step_hide: [
		'progress_indicator_none-wrapper',
		'progress_indicator_bar-wrapper',
	],
	// next button
	next_button_text_hide: [
		'next_button_image_heading-wrapper',
		'next_button_image-wrapper',
		'next_button_image_hover-wrapper',
	],
	next_button_text_show: [
		'next_button_text-wrapper',
		'next_button_text_color-wrapper',
		'next_button_text_bg_color-wrapper',
	],
	next_button_image_hide: [
		'next_button_text-wrapper',
		'next_button_text_color-wrapper',
		'next_button_text_bg_color-wrapper',
	],
	next_button_image_show: [
		'next_button_image_heading-wrapper',
		'next_button_image-wrapper',
		'next_button_image_hover-wrapper',
	],
	// pre button
	pre_button_text_hide: [
		'pre_button_image_heading-wrapper',
		'pre_button_image-wrapper',
		'pre_button_image_hover-wrapper',
	],
	pre_button_text_show: [
		'pre_button_text-wrapper',
		'pre_button_text_color-wrapper',
		'pre_button_text_bg_color-wrapper',
	],
	pre_button_image_hide: [
		'pre_button_text-wrapper',
		'pre_button_text_color-wrapper',
		'pre_button_text_bg_color-wrapper',
	],
	pre_button_image_show: [
		'pre_button_image_heading-wrapper',
		'pre_button_image-wrapper',
		'pre_button_image_hover-wrapper',
	],
};

function yndformFieldInit() {
	colorElements = $$('.yndform_color');
	colorElements.each(function(el){
		var colorId = el.get('id'),
			colorBox = $(colorId + '_box-element'),
			htmlContent = '<input value="' + el.get('value') + '" type="color" id="' + colorId + '_picker" name="' + colorId + '"/>';
		if ($(colorId + '_box-element')) $(colorId + '_box-element').inject($(colorId + '-element'), 'bottom');
		if ($(colorId + '_box-wrapper')) $(colorId + '_box-wrapper').dispose();
		colorBox.getChildren('span')[0].set('html', htmlContent);
	});
	$$('.yndform_color_picker').addEvent('change', function (ele) {
		$(ele.target.name).value = ele.target.value;
	});

	$$('.yndform_switchable:checked').each(function(el){
		yndformSwitchSection(el);
	});

	if ($('background_color_picker')) {
		$('background_color_picker').addEvent('change', function(){
			$$('.yndform_preview_bg').setStyle('background-color', this.get('value'));
			$$('.yndform_preview_text_inv').setStyle('color', this.get('value'));
		});
		$$('.yndform_preview_bg').setStyle('background-color', $('background_color_picker').get('value'));
		$$('.yndform_preview_text_inv').setStyle('color', $('background_color_picker').get('value'));
	}

	if ($('text_color_picker')) {
		$('text_color_picker').addEvent('change', function(){
			$$('.yndform_preview_text').setStyle('color', this.get('value'));
			$$('.yndform_preview_text_inv').setStyle('background-color', this.get('value'));
		});
		$$('.yndform_preview_text').setStyle('color', $('text_color_picker').get('value'));
		$$('.yndform_preview_text_inv').setStyle('background-color', $('text_color_picker').get('value'));
	}
}

function yndformSwitchSection(el) {
	var value = el.get('name') + '_' + el.get('value');
	yndformSwitch[value + '_show'].each(function(id){
		if ($(id))
			$(id).show();
	});
	yndformSwitch[value + '_hide'].each(function(id){
		if ($(id))
			$(id).hide();
	});
}