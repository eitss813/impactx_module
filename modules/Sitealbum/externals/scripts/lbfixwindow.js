var locationHref = window.location.href, defaultLoad = true, defaultLBAlbumPhotoContent = '', fullmode_photo = false, addAgainscrollFalg = true, rightSidePhotoContent, canClosePhotoLB = true, siteablum_loading_image = true, scrollPosition = {
  left: 0,
  top: 0
}, loadedAllPhotos = '', contentPhotoSize = {
  width: 0,
  height: 0
};
var createDefaultContentAdvLBSA = function(element) {
  new Element('input', {
    'id': 'canReloadSitealbum',
    'type': 'hidden',
    'value': 0
  }).inject(element);
  new Element('div', {
    'class': 'photo_lightbox_overlay'
  }).inject(element);
  new Element('div', {
    'id': 'photo_lightbox_close',
    'class': 'photo_lightbox_close',
    'onclick': "closeLightBoxAlbum()",
    'title': en4.core.language.translate("Press Esc to Close")
  }).inject(element);

  var photoContentDiv = new Element('div', {
    'id': 'white_content_default_album',
    'class': 'photo_lightbox_content_wrapper'
  });
  var photolbCont = new Element('div', {
    'class': 'photo_lightbox_cont'
  }).inject(photoContentDiv);

  if (en4.orientation == 'ltr') {
    var photolbLeft = new Element('div', {
      'id': 'photo_lightbox_left',
      'class': 'photo_lightbox_left',
      'styles': {
        'right': '1px'
      }
    }).inject(photolbCont);
  } else {
    var photolbLeft = new Element('div', {
      'id': 'photo_lightbox_left',
      'class': 'photo_lightbox_left',
      'styles': {
        'left': '1px'
      }
    }).inject(photolbCont);
  }

  var photolbLeftTable = new Element('table', {
    'width': '100%',
    'height': '100%'
  }).inject(photolbLeft);
  var photolbLeftTableTr = new Element('tr', {
  }).inject(photolbLeftTable);

  var photolbLeftTableTrTd = new Element('td', {
    'width': '100%',
    'height': '100%',
    'valign': 'middle'
  }).inject(photolbLeftTableTr);

  new Element('div', {
    'id': 'media_image_div_sitealbum',
    'class': 'photo_lightbox_image'
  }).inject(photolbLeftTableTrTd);
  new Element('div', {
    'class': 'lightbox_btm_bl'
  }).inject(photoContentDiv);
  photoContentDiv.inject(element);
  photoContentDiv.addEvent('click', function(event) {
    event.stopPropagation();
  });

};

// Open the Advanced light Box
var openLightBoxAlbum = function(imagepath, url) {
  if (!$("white_content_default_album")) {
    createDefaultContentAdvLBSA($("album_light"));
  }
  //$("photo_lightbox_close").title="";
  scrollPosition['top'] = window.getScrollTop();
  scrollPosition['left'] = window.getScrollLeft();

  if($$('.layout_page_sitealbum_album_view'))
    { 
      $$('.layout_page_sitealbum_album_view').each(function(ele,index) { 
       document.getElementById('album_light').inject(ele,'top'); 
      })
    } 
  document.getElementById('album_light').style.display = 'block'; 
  var loading_image_path = en4.core.staticBaseUrl + 'application/modules/Seaocore/externals/images/icons/loader-large.gif';

  if (siteablum_loading_image == 0) {
    document.getElementById('media_image_div_sitealbum').innerHTML = "&nbsp;<img class='photo_lightbox_loader' src='" + loading_image_path + "'  />";
  } else {
    var remove_extra = 0;
    contentPhotoSize['height'] = $("photo_lightbox_left").getCoordinates().height - remove_extra;
    remove_extra = remove_extra + 289;
    contentPhotoSize['width'] = $("photo_lightbox_left").getCoordinates().width - remove_extra;
    document.getElementById('media_image_div_sitealbum').innerHTML = "&nbsp;<img class='lightbox_photo' src=" + imagepath + " style='max-width: " + contentPhotoSize['width'] + "px; max-height: " + contentPhotoSize['height'] + "px;'  />";
    $$(".lightbox_btm_bl").each(function(el) {
      el.innerHTML = "<center><img src='" + loading_image_path + "' style='height:30px;' /> </center>";
    });
  }
  if ($('arrowchat_base'))
    $('arrowchat_base').style.display = 'none';
  if ($('wibiyaToolbar'))
    $('wibiyaToolbar').style.display = 'none';
  setHtmlScroll("hidden");
  getSitealbumPhoto(url, 0, imagepath);
},
// Cloase the Advanced Light Box
        closeLightBoxAlbum = function()
{
  if (fullScreenApi.isFullScreen()) {
    fullScreenApi.cancelFullScreen()
  } else {
    defaultLoad = true;
    document.getElementById('album_light').style.display = 'none';
    setHtmlScroll("auto");
    if ($('arrowchat_base'))
      $('arrowchat_base').style.display = 'block';
    if ($('wibiyaToolbar'))
      $('wibiyaToolbar').style.display = 'block';
    window.scroll(scrollPosition['left'], scrollPosition['top']); // horizontal and vertical scroll targets

    if (history.replaceState)
      history.replaceState({}, document.title, locationHref);
    else {
      window.location.hash = "";
    }
    if ($type(keyDownEventsSitealbumPhoto))
      document.removeEvent("keydown", keyDownEventsSitealbumPhoto);
    if ($type(keyUpLikeEventSitealbumPhoto))
      document.removeEvent("keyup", keyUpLikeEventSitealbumPhoto);
    if (document.getElementById('canReloadSitealbum').value == 1) {
      window.location.reload(true);
    }
    document.getElementById('album_light').empty();
    loadedAllPhotos = '';
    //   $("white_content_default_album").innerHTML = defaultLBAlbumPhotoContent;
    fullmode_photo = false;
    getTaggerInstanceSitealbum();
  }
};

var adsinnerCommHTML = "";
// Get the photo Content
var getSitealbumPhoto = function(url, isajax, imagepath) {
  var photoUrl = url.replace("/sitealbums/", "/albums/");
  photoUrl = photoUrl.replace("/light-box-view/album_id/", "/view/album_id/");
  if (history.replaceState) {
    history.replaceState({}, document.title, photoUrl);
  } else {
    window.location.hash = photoUrl;
  }
  $$(".lightbox_btm_bl").each(function(el) {
    if (isajax)
      el.innerHTML = "<center><img src='" + en4.core.staticBaseUrl + "application/modules/Seaocore/externals/images/icons/loader-large.gif' style='height:30px;' /> </center>";
  });
  
  if (isajax)
  document.getElementById('media_image_div_sitealbum').innerHTML = "&nbsp;<img class='lightbox_photo' src=" + imagepath + " style='max-width: " + contentPhotoSize['width'] + "px; max-height: " + contentPhotoSize['height'] + "px;'  />";

  var remove_extra = 0;
  contentPhotoSize['height'] = $("photo_lightbox_left").getCoordinates().height - remove_extra;
  if (isajax == 0)
    remove_extra = remove_extra + 289;
  contentPhotoSize['width'] = $("photo_lightbox_left").getCoordinates().width - remove_extra;

  addAgainscrollFalg = true;
  en4.core.request.send(new Request.HTML({
    url: url,
    data: {
      format: 'html',
      isajax: isajax
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if ($('white_content_default_album')) {
        $('white_content_default_album').innerHTML = responseHTML;
        switchFullModePhoto(fullmode_photo);
      }
    }
  }), true);
},
        setHtmlScroll = function(cssCode) {
  $$('html').setStyle('overflow', cssCode);

},
// Close the All Photo Contener
        closeAllPhotoContener = function() {
  $("all_photos").style.height = "0px";
  $("close_all_photos").style.height = "0px";
  $("close_all_photos_btm").style.height = "0px";
},
// View all photos of the album in bottom
        showAllPhotoContener = function(album_id, photo_id, count_photo) {
  var onePhotoSizeW = 144, onePhotoSizeH = 112;
  heightContent = onePhotoSizeH + 60;
  var inOneRow = Math.ceil((window.getSize().x / (onePhotoSizeW + 40)));
  if (count_photo > inOneRow) {
    heightContent = heightContent + onePhotoSizeH - 2;
  }

  $("all_photos").style.height = heightContent + "px";
  $("close_all_photos").style.height = "100%";
  $("close_all_photos_btm").style.height = "60px";
  $("photos_contener").setStyle("max-height", (heightContent - 40) + "px")
  if (loadedAllPhotos != '') {
    $("photos_contener").empty();
    Elements.from(loadedAllPhotos).inject($("photos_contener"));
    onclickPhotoThumb($("lb-all-thumbs-photo-" + photo_id));
    if (addAgainscrollFalg) {
      new SEAOMooVerticalScroll('main_photos_contener', 'photos_contener', {});
      addAgainscrollFalg = false;
    }
  } else {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'sitealbum/photo/get-all-photos',
      data: {
        format: 'html',
        album_id: album_id,
        photo_id: photo_id
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $("photos_contener").empty();
        loadedAllPhotos = responseHTML;
        Elements.from(responseHTML).inject($("photos_contener"));
        onclickPhotoThumb($("lb-all-thumbs-photo-" + photo_id));
        new SEAOMooVerticalScroll('main_photos_contener', 'photos_contener', {});
        addAgainscrollFalg = false;
      }
    }), {
      "force": true
    });
  }
},
// Selected the Thumb
        onclickPhotoThumb = function(element) {
  if (element.tagName.toLowerCase() == 'a') {
    element = element.getParent('li');
  }
  var myContainer = element.getParent('.lb_photos_contener').getParent();
  myContainer.getElements('ul > li').removeClass('sea_val_photos_thumbs_selected');
  element.addClass('sea_val_photos_thumbs_selected');

},
        showPhotoToggleContent = function(element_id) {
  var el = $(element_id);
  el.toggleClass('sea_photo_box_open');
  el.toggleClass('sea_photo_box_closed');
},
// Hide and Show Right Side Box
        switchFullModePhoto = function(fullmode) {
  if (!$("full_screen_display_captions_on_image"))
    return;
  if (fullmode) {
    fullScreenApi.requestFullScreen(document.body);
    if ($("photo_owner_lb_fullscreen"))
      $("photo_owner_lb_fullscreen").style.display = 'block';
    if ($("photo_owner_titile_lb_fullscreen"))
      $("photo_owner_titile_lb_fullscreen").style.display = 'block';
    if ($("photo_owner_titile_lb_fullscreen_sep"))
      $("photo_owner_titile_lb_fullscreen_sep").style.display = 'block';
    if ($("full_screen_display_captions_on_image"))
      $("full_screen_display_captions_on_image").style.display = 'block';
    $("photo_lightbox_right_content").style.width = '1px';
    $("photo_lightbox_right_content").style.visibility = 'hidden';
    if (en4.orientation == 'ltr') {
      $("photo_lightbox_left").style.right = '1px';
    } else {
      $("photo_lightbox_left").style.left = '1px';
    }
    $("full_mode_photo_button").style.display = 'none';
    $("comment_count_photo_button").style.display = 'block';
    if ($("full_screen_display_captions_on_image_dis")) {
      (function() {
        if (!$("media_photo"))
          return;
        var width_ln = $("media_photo").getCoordinates().width;
        var total_char = 2 * (width_ln / 6).toInt();
        if (total_char <= 100)
          total_char = 100;
        var str = $("full_screen_display_captions_on_image_dis").innerHTML;
        if (str.length > total_char) {
          $("full_screen_display_captions_on_image_dis").innerHTML = str.substr(0, (total_char - 3)) + "...";
        }
      }).delay(50);
    }
  } else {
    if ($("photo_owner_lb_fullscreen"))
      $("photo_owner_lb_fullscreen").style.display = 'none';
    if ($("photo_owner_titile_lb_fullscreen"))
      $("photo_owner_titile_lb_fullscreen").style.display = 'none';
    if ($("photo_owner_titile_lb_fullscreen_sep"))
      $("photo_owner_titile_lb_fullscreen_sep").style.display = 'none';
    if ($("full_screen_display_captions_on_image"))
      $("full_screen_display_captions_on_image").style.display = 'none';
    $("photo_lightbox_right_content").style.width = '300px';
    $("photo_lightbox_right_content").style.visibility = 'visible';
    if (en4.orientation == 'ltr') {
      $("photo_lightbox_left").style.right = '300px';
    } else {
      $("photo_lightbox_left").style.left = '300px';
    }
    $("full_mode_photo_button").style.display = 'block';
    $("comment_count_photo_button").style.display = 'none';
  }

  fullmode_photo = fullmode;
  contentPhotoSize['height'] = $("photo_lightbox_left").getCoordinates().height;
  contentPhotoSize['width'] = $("photo_lightbox_left").getCoordinates().width;
  setPhotoContent();
},
        setPhotoContent = function() {
  if ($("media_photo")) {

    $("media_photo").setStyle("max-width", contentPhotoSize['width'] + "px");
    $("media_photo").setStyle("max-height", contentPhotoSize['height'] + "px");
    $("media_photo_next").setStyle("max-width", contentPhotoSize['width'] + "px");
    $("media_photo_next").setStyle("max-height", contentPhotoSize['height'] + "px");
    setTimeout("getTaggerInstanceSitealbum()", 1250);
  }
};
// ADD Fullscreen api
(function() {
  var api = {
    supportsFullScreen: false,
    isFullScreen: function() {
      return false;
    },
    requestFullScreen: function() {
    },
    cancelFullScreen: function() {
    },
    fullScreenEventName: '',
    prefix: ''
  },
  browserPrefixes = 'webkit moz o ms khtml'.split(' ');

  // Check for native support.  
  if (typeof document.cancelFullScreen != 'undefined') {
    api.supportsFullScreen = true;
  } else {
    // Check for fullscreen support by browser prefix.  
    for (var i = 0, il = browserPrefixes.length; i < il; i++) {
      api.prefix = browserPrefixes[i];
      functionName = api.prefix + 'CancelFullScreen';

      if (typeof document[functionName] != 'undefined') {
        api.supportsFullScreen = true;
        break;
      }
    }
  }

  // Update methods.  
  if (api.supportsFullScreen) {
    api.fullScreenEventName = api.prefix + 'fullscreenchange';

    api.isFullScreen = function() {
      switch (this.prefix) {
        case '':
          return document.fullScreen;
        case 'webkit':
          return document.webkitIsFullScreen;
        default:
          return document[this.prefix + 'FullScreen'];
      }
    }
    api.requestFullScreen = function(el) {
      switch (this.prefix) {
        case '':
          return el.requestFullScreen();
        case 'webkit':
          /* @TODO:: INPUT KEYS (A-I) NOT WORKING*/
          return /*el.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT)*/;
        default:
          return el[this.prefix + 'RequestFullScreen']();
      }

    }
    api.cancelFullScreen = function(el) {
      if (this.prefix === '') {
        return document.cancelFullScreen();
      } else {
        return document[this.prefix + 'CancelFullScreen']();
      }
    }
  }

  // Export api.  
  window.fullScreenApi = api;
})();

if (fullScreenApi.supportsFullScreen === true) {
  document.addEventListener(fullScreenApi.fullScreenEventName, function(e) {
    if (document.getElementById('album_light').style.display != 'block')
      return;
    switchFullModePhoto(fullScreenApi.isFullScreen());
    var html_titile = en4.core.language.translate("Press Esc to Close");
    if (fullScreenApi.isFullScreen()) {
      html_titile = en4.core.language.translate("Press Esc to exit Full-screen");
    }
    $("photo_lightbox_close").title = html_titile;
    resetPhotoContent();
    if (typeof rightSidePhotoContent != 'undefined')
      rightSidePhotoContent.update();
  }, true);
}

var resetPhotoContent = function() {
  if ($('ads')) {
    if ($('ads_hidden')) {
      if ($('ads').getCoordinates().height < 30) {
        $('ads').empty();
      }
      adsinnerHTML = $('ads').innerHTML;
    } else {
      $('ads').innerHTML = adsinnerHTML;
    }
    (function() {
      if (!$('ads'))
        return;
      $('ads').style.bottom = "0px";
      if ($('photo_lightbox_right_content').getCoordinates().height < ($('photo_right_content').getCoordinates().height + $('ads').getCoordinates().height + 10)) {
        $('ads').empty();
        $('main_right_content_area').style.height = $('photo_lightbox_right_content').getCoordinates().height - 2 + "px";
        $('main_right_content').style.height = $('photo_lightbox_right_content').getCoordinates().height - 2 + "px";
      } else {
        $('main_right_content_area').style.height = $('photo_lightbox_right_content').getCoordinates().height - ($('ads').getCoordinates().height + 10) + "px";
        $('main_right_content').style.height = $('photo_lightbox_right_content').getCoordinates().height - ($('ads').getCoordinates().height + 10) + "px";
      }
    }).delay(1000);
  }
};

/* Photo/light-box-view-fix-window */
var showeditDescriptionSitealbum = function() {
  if (document.getElementById('edit_sitealbum_description')) {
    if (document.getElementById('link_sitealbum_description').style.display == "block") {
      document.getElementById('link_sitealbum_description').style.display = "none";
      document.getElementById('edit_sitealbum_description').style.display = "block";
      $('editor_sitealbum_description').focus();
    } else {
      document.getElementById('link_sitealbum_description').style.display = "block";
      document.getElementById('edit_sitealbum_description').style.display = "none";
    }

    if (document.getElementById('sitealbum_description_loading'))
      document.getElementById('sitealbum_description_loading').style.display = "none";
  }
};

var featuredPhoto = function(subject_guid)
{
  en4.core.request.send(new Request.HTML({
    method: 'post',
    'url': en4.core.baseUrl + 'sitealbum/photo/featured',
    'data': {
      format: 'html',
      'subject': subject_guid
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if ($('featured_sitealbum_photo').style.display == 'none') {
        $('featured_sitealbum_photo').style.display = "";
        $('un_featured_sitealbum_photo').style.display = "none";
      } else {
        $('un_featured_sitealbum_photo').style.display = "";
        $('featured_sitealbum_photo').style.display = "none";
      }
    }
  }), {
    "force": true
  });
  return false;

};
function saveeditPhotoDescriptionSA(photo_id)
{
  // var photo_id = '<?php echo $this->photo->getIdentity(); ?>';
  var str = document.getElementById('editor_sitealbum_description').value.replace('/\n/g', '<br />');
  var str_temp = document.getElementById('editor_sitealbum_description').value;

  if (document.getElementById('sitealbum_description_loading'))
    document.getElementById('sitealbum_description_loading').style.display = "";

  document.getElementById('edit_sitealbum_description').style.display = "none";
  en4.core.request.send(new Request.HTML({
    url: en4.core.baseUrl + 'sitealbum/photo/edit-description',
    data: {
      format: 'html',
      text_string: str_temp,
      photo_id: photo_id
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

      if (str == '')
        str_temp = en4.core.language.translate('Add a caption');
      document.getElementById('sitealbum_description').innerHTML = str_temp.replace(/\n/gi, "<br /> \n");
      showeditDescriptionSitealbum();
      var descEls = $$('.lightbox_photo_description');
      if (descEls.length > 0) {
        descEls[0].enableLinks();
      }
    }
  }), {
    "force": true
  });

}

/*  
 EDIT THE TITLE
 */
var showeditPhotoTitleSA = function() {
  if (document.getElementById('edit_seaocore_title')) {
    if (document.getElementById('link_seaocore_title').style.display == "block") {
      document.getElementById('link_seaocore_title').style.display = "none";
      document.getElementById('edit_seaocore_title').style.display = "block";
      $('editor_seaocore_title').focus();
    } else {
      document.getElementById('link_seaocore_title').style.display = "block";
      document.getElementById('edit_seaocore_title').style.display = "none";
    }

    if (document.getElementById('seaocore_title_loading'))
      document.getElementById('seaocore_title_loading').style.display = "none";
  }
};

var saveeditPhotoTitleSA = function(photo_id, resourcetype)
{

  var str = document.getElementById('editor_seaocore_title').value.replace('/\n/g', '<br />');
  var str_temp = document.getElementById('editor_seaocore_title').value;

  if (document.getElementById('seaocore_title_loading'))
    document.getElementById('seaocore_title_loading').style.display = "";
  document.getElementById('edit_seaocore_title').style.display = "none";
  en4.core.request.send(new Request.HTML({
    url: en4.core.baseUrl + 'sitealbum/photo/edit-title',
    data: {
      format: 'html',
      text_string: str_temp,
      photo_id: photo_id,
      resource_type: resourcetype
    },
    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if (str == '')
        str_temp = en4.core.language.translate('Add a title');
      document.getElementById('seaocore_title').innerHTML = str_temp;
      showeditPhotoTitleSA();
    }
  }), {
    "force": true
  });
};
  