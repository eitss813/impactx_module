var hideWidgetsForModule = function(widgetname) {
  var globalContentElement = en4.seaocore.getDomElements('content');
  if(widgetname == 'sitepageactivityfeed') {
    if($(globalContentElement).getElement('.layout_activity_feed')) {
      $(globalContentElement).getElement('.layout_activity_feed').style.display = 'block';
    }
  }
  else {
    if($(globalContentElement).getElement('.layout_activity_feed')) {
      $(globalContentElement).getElement('.layout_activity_feed').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageseaocoreactivityfeed') {
		if($(globalContentElement).getElement('.layout_seaocore_feed')) {
			$(globalContentElement).getElement('.layout_seaocore_feed').style.display = 'block';
    }
  } else {
		if($(globalContentElement).getElement('.layout_seaocore_feed')) {
			$(globalContentElement).getElement('.layout_seaocore_feed').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageadvancedactivityactivityfeed') {
    if($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
      $(globalContentElement).getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
    }
  } else {
    if($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
      $(globalContentElement).getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageinfo') {
    if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'block';
    }
  }
  else {
    if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'none';
    }
  }
  if(widgetname == 'sitepageoverview') {
    if($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'block';
    }
  }
  else {
    if($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
    }
  }
  if(widgetname == 'sitepagelocation') {
    if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'block';
    }
  }
  else {
    if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
      $(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'none';
    }
  }
  if(widgetname == 'sitepagelink') {
    if($(globalContentElement).getElement('.layout_core_profile_links')) {
      $(globalContentElement).getElement('.layout_core_profile_links').style.display = 'block';
    }
  }
  else {
    if($(globalContentElement).getElement('.layout_core_profile_links')) {
      $(globalContentElement).getElement('.layout_core_profile_links').style.display = 'none';
    }
  }

}