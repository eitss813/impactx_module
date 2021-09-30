var locationHref=window.location.href;
var defaultLoad=true,defaultLBAlbumPhotoContent="";
var scrollPosition={left:0,top:0};
en4.core.runonce.add(function() {
  defaultLBAlbumPhotoContent = $("white_content_default_album").innerHTML;
    
  $('white_content_default_album').addEvent('click', function(event) {
    event.stopPropagation();
  });
});
function openLightBoxAlbum(imagepath, url){
  scrollPosition['top']=window.getScrollTop();
  scrollPosition['left']=window.getScrollLeft(); 
  document.getElementById('album_light').style.display='block';
   if(siteablum_loading_image ==0){    
		 document.getElementById('media_image_div_sitealbum').innerHTML= "&nbsp;<img  class='lightbox_loader_img' src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/icons/loader.gif'  />";
   }else{
    document.getElementById('media_image_div_sitealbum').innerHTML= "&nbsp;<img class='lightbox_photo' src="+imagepath+"  />";
   }
    setHtmlScroll("hidden");
  photopaginationSitealbum(url,0);
}


var closeLightBoxAlbum = function()
{
  defaultLoad=true;
  document.getElementById('album_light').style.display='none';
  setHtmlScroll("auto");
  window.scroll(scrollPosition['left'],scrollPosition['top']); // horizontal and vertical scroll targets
  //    if($('album_lightbox'))
  //      $('album_lightbox').innerHTML ="";
  //
  //    if(document.getElementById('photo_lightbox_text')){
  //      document.getElementById('photo_lightbox_text').innerHTML="";
  //      document.getElementById('photo_lightbox_text').style.display="none";
  //    }
  //    if(document.getElementById('photo_lightbox_user_options'))
  //      document.getElementById('photo_lightbox_user_options').style.display="none";
  //    if(document.getElementById('sitealbum_photo_scroll'))
  //      document.getElementById('sitealbum_photo_scroll').style.display="none";
  //    if(document.getElementById('photo_lightbox_user_right_options'))
  //      document.getElementById('photo_lightbox_user_right_options').style.display="none";
  //    if(document.getElementById('photo_view_comment'))
  //      $('photo_view_comment').innerHTML="";
  //    if(document.getElementById('album_lightbox'))
  //      $('album_lightbox').innerHTML="";


  if (history.replaceState)
    history.replaceState( {}, document.title, locationHref );
  else{  
    window.location.hash="0";
  }

  if(document.getElementById('canReloadSitealbum').value==1){
    window.location.reload(true);
  }
  $("white_content_default_album").innerHTML = defaultLBAlbumPhotoContent;
};

var photopaginationSitealbum = function(url,isajax)
{ 
  if($('ads') && $('ads_hidden')){
    $('ads_hidden').innerHTML = $('ads').innerHTML;
  }
  var photoUrl=url.replace("/sitealbums/", "/albums/");
  photoUrl=photoUrl.replace("/light-box-view/album_id/", "/view/album_id/");
  if (history.replaceState)
    history.replaceState( {}, document.title, photoUrl );
  else{
    window.location.hash =photoUrl;
  }
  $$(".photo_lightbox_photo_detail").each(function(el){ 
    el.empty();  
  });    
  $$(".photo_lightbox_content").each(function(el){ 
    el.empty();  
  });
  $$(".photo_lightbox_user_right_options").each(function(el){
    el.empty();
  });
  if(isajax){
    setHtmlScroll("auto");
    setImageScrollAlbum("hidden");
    if(document.getElementById('media_image_div_sitealbum'))
			document.getElementById('media_image_div_sitealbum').innerHTML="<img src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/icons/loader.gif'  class='lightbox_loader_img' />";
  }    
  en4.core.request.send(new Request.HTML({
    url : url,
    data : {
      format : 'html',
      isajax : isajax
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(isajax){   
        setHtmlScroll("hidden");
        setImageScrollAlbum("auto");
        $('white_content_default_album').innerHTML = responseHTML;
      } else{       
        $('image_div_album').innerHTML = responseHTML;
      }
      if($('ads') && $('ads_hidden')){
        $('ads').innerHTML =  $('ads_hidden').innerHTML;
        $('ads_hidden').innerHTML='';
      }
    }
   }), {"force":true});
};

function setHtmlScroll(cssCode) {
  $$('html').setStyle('overflow',cssCode);
    
}

function setImageScrollAlbum(cssCode) {
  $$('.photo_lightbox_white_content_wrapper').setStyle('overflow',cssCode);

}
  
function showeditDescriptionSitealbum(){
  if(document.getElementById('edit_sitealbum_description')){
    if(document.getElementById('link_sitealbum_description').style.display=="block"){
      document.getElementById('link_sitealbum_description').style.display="none";
      document.getElementById('edit_sitealbum_description').style.display="block";
      $('editor_sitealbum_description').focus();
    }else{
      document.getElementById('link_sitealbum_description').style.display="block";
      document.getElementById('edit_sitealbum_description').style.display="none";
    }

    if(document.getElementById('sitealbum_description_loading'))
      document.getElementById('sitealbum_description_loading').style.display="none";
  }
}

function saveeditPhotoDescriptionSA(photo_id)
{
  var str =document.getElementById('editor_sitealbum_description').value.replace('/\n/g','<br />');
  var str_temp =document.getElementById('editor_sitealbum_description').value;
  
  if(document.getElementById('sitealbum_description_loading'))
    document.getElementById('sitealbum_description_loading').style.display="";

  document.getElementById('edit_sitealbum_description').style.display="none";
  en4.core.request.send(new Request.HTML({
    url :en4.core.baseUrl + 'sitealbum/photo/edit-description',
    data : {
      format : 'html',
      text_string : str_temp,
      photo_id : photo_id
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {

      if(str=='')
        str_temp='<a href="javascript:void(0);" onclick="showeditDescriptionSitealbum()" > '+en4.core.language.translate('Add a caption')+'</a>';
      document.getElementById('sitealbum_description').innerHTML=str_temp.replace(/\n/gi, "<br /> \n");
      showeditDescriptionSitealbum();
    }
    }), {"force":true});
}

var loadingImageSitealbum= function(){
  if(document.getElementById('media_image_div_sitealbum'))
		$('media_photo').src = en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/icons/loader.gif";
  $('media_photo').style.marginTop='245px';
   
};

function featuredPhoto(subject_guid)
{
  en4.core.request.send(new Request.HTML({
    method : 'post',
    'url' : en4.core.baseUrl + 'sitealbum/photo/featured',
    'data' : {
      format : 'html',
      'subject' : subject_guid
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($('featured_sitealbum_photo').style.display=='none'){
        $('featured_sitealbum_photo').style.display="";
        $('un_featured_sitealbum_photo').style.display="none";
      }else{
        $('un_featured_sitealbum_photo').style.display="";
        $('featured_sitealbum_photo').style.display="none";
      }
    }
   }), {"force":true});

  return false;

}

/*  
 EDIT THE TITLE
 */
function showeditPhotoTitleSA(){
  if(document.getElementById('edit_seaocore_title')){
    if(document.getElementById('link_seaocore_title').style.display=="block"){
      document.getElementById('link_seaocore_title').style.display="none";
      document.getElementById('edit_seaocore_title').style.display="block";
      $('editor_seaocore_title').focus();
    } else{
      document.getElementById('link_seaocore_title').style.display="block";
      document.getElementById('edit_seaocore_title').style.display="none";
    }

    if(document.getElementById('seaocore_title_loading'))
      document.getElementById('seaocore_title_loading').style.display="none";
  }
}  

function saveeditPhotoTitleSA(photo_id,resourcetype)
{

  var str = document.getElementById('editor_seaocore_title').value.replace('/\n/g','<br />');
  var str_temp = document.getElementById('editor_seaocore_title').value;

  if(document.getElementById('seaocore_title_loading'))
    document.getElementById('seaocore_title_loading').style.display="";
  document.getElementById('edit_seaocore_title').style.display="none";
  en4.core.request.send(new Request.HTML({
    url :en4.core.baseUrl + 'sitealbum/photo/edit-title',
    data : {
      format : 'html',
      text_string : str_temp,
      photo_id : photo_id,
      resource_type : resourcetype
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if(str=='')
        str_temp='<a href="javascript:void(0);" onclick="showeditPhotoTitleSA()" >'+en4.core.language.translate('Add a title')+' </a>';
      document.getElementById('seaocore_title').innerHTML=str_temp;
      showeditPhotoTitleSA();
    }
   }), {"force":true});
}  
function showSmoothBox(url)
{
  Smoothbox.open(url);
  parent.Smoothbox.close;
} 