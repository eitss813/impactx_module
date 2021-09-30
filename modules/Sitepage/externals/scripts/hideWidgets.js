function hideWidgets() {
  var globalContentElement = en4.seaocore.getDomElements('content');
  if($(globalContentElement).getElement('.layout_activity_feed')) {
		$(globalContentElement).getElement('.layout_activity_feed').style.display = 'none';
	}
	if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
		$(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'none';
	}	
	if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
		$(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'none';
	}		
	if($(globalContentElement).getElement('.layout_core_profile_links')) {
		$(globalContentElement).getElement('.layout_core_profile_links').style.display = 'none';
	}
	if($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
		$(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
	}
}
