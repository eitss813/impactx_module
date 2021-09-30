<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css');
  
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');  
?>

<?php
// Starting work for "Slide Show".
$image_text_var = '';
$title_link_var = '';

$title_link_var = "new Element('h4').set('html',";
if ($this->show_link == 'true')
  $title_link_var .= "'<a href=" . '"' . "'+currentItem.link+'" . '"' . ">link</a>'";
if ($this->title == 'true')
  $title_link_var .= "+currentItem.title";
$title_link_var .= ").inject(im_info);";

$image_count = 1;

foreach ($this->show_slideshow_object as $type => $album) {
  $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
  $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $album->page_id, $layout);
  if($album->photo_id != 0) {
  $albumPhoto = $this->htmlLink($album->getHref(array('tab' => $tab_id)), $this->itemPhoto($album, 'thumb.profile'));
  }
  else {
    $albumPhoto = $this->htmlLink($album->getHref(array('tab' => $tab_id)), $this->itemPhoto($album, 'thumb.normal'));
  }
  $content_info = null;
  if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):
  $content_info .= $this->timestamp(strtotime($album->creation_date)) . $this->translate(' - created by ') . $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(),array('title' => $album->getOwner()->getTitle()));
  endif;
  //$content_info = '<div class="sitepage_sidebar_list_details"></div>';
  $content_info.='<p>';
  $content_info.=$this->translate(array('%s photo', '%s photos', $album->count()), $this->locale()->toNumber($album->count())) . ', ';
  $content_info.=$this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count)) . ', ';
  $content_info.=$this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)) . ', ';
  $content_info.=$this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count));
  $content_info.='</p>';

  $description = $album->description;

  $content_link = $this->htmlLink($album->getHref(array('tab' => $tab_id)), $this->translate('View Album &raquo;'), array('class' => 'featured_slideshow_view_link'));

  $image_text_var .= "<div class='featured_slidebox'>";
  $image_text_var .= "<div class='featured_slidshow_img'>" . $albumPhoto . "</div>";


  if (!empty($content_info)) {
    $image_text_var .= "<div class='featured_slidshow_content'>";
  }
  if (!empty($album->title)) {

    $title = $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10);

    $image_text_var .='<h5>' . $this->htmlLink($album->getHref(array('tab' => $tab_id)), $album->title,array('title' => $album->getTitle())) . '</h5>';
    $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $album->page_id);
		$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
		$tmpBody = strip_tags($sitepage_object->title);
		$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
    $image_text_var .= "<div class='featured_slidshow_info'>";
    $image_text_var .= $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($album->page_id, $album->owner_id, $album->getSlug()),  $page_title,array('title' => $sitepage_object->title)); 
    $image_text_var .= "</div>";

  }

  if (!empty($content_link)) {
    $image_text_var .= "<h3 style='display:none'><span>" . $image_count++ . '_caption_title:' . $title . '_caption_link:' . $content_link . '</span>' . "</h3>";
  }

  if (!empty($content_info)) {
    $image_text_var .= "<span class='featured_slidshow_info'>" . $content_info . "</span>";
  }

  if (!empty($description)) {
    $truncate_description = ( Engine_String::strlen($description) > 253 ? Engine_String::substr($description, 0, 250) . '...' : $description );
    $image_text_var .= "<p>" . $truncate_description . " " . $this->htmlLink($album->getHref(array('tab' => $tab_id)), $this->translate('More &raquo;')) . "</p>";
  }

  $image_text_var .= "</div></div>";
}
if (!empty($this->num_of_slideshow)) {
?>
  <script type="text/javascript">
    window.addEvent('domready',function(){
      
      if (document.getElementsByClassName == undefined) {
        document.getElementsByClassName = function(className)
        {
          var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
          var allElements = document.getElementsByTagName("*");
          var results = [];

          var element;
          for (var i = 0; (element = allElements[i]) != null; i++) {
            var elementClass = element.className;
            if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
              results.push(element);
          }

          return results;
        }
      }

      var globalContentElement = en4.seaocore.getDomElements('content');
      var width=$(globalContentElement).getElement(".featured_slideshow_wrapper").clientWidth;
      $(globalContentElement).getElement(".featured_slideshow_mask").style.width= (width-10)+"px";
      var divElements=document.getElementsByClassName('featured_slidebox');   
     for(var i=0;i < divElements.length;i++)
      divElements[i].style.width= (width-10)+"px";
  
      var handles8_more = $$('#handles8_more span');
      var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
      var nS8 = new noobSlide({
        box: $('sitepagealbum_featured_album_im_te_advanced_box'),
        items: $$('#sitepagealbum_featured_album_im_te_advanced_box h3'),
        size: (width-10),
        handles: $$('#handles8 span'),
        addButtons: {previous: $('sitepagealbum_featured_album_prev8'), stop: $('sitepagealbum_featured_album_stop8'), play: $('sitepagealbum_featured_album_play8'), next: $('sitepagealbum_featured_album_next8') },
        interval: 5000,
        fxOptions: {
          duration: 500,
          transition: '',
          wait: false
        },
        autoPlay: true,
        mode: 'horizontal',
        onWalk: function(currentItem,currentHandle){

          //		// Finding the current number of index.
          var current_index = this.items[this.currentIndex].innerHTML;
          var current_start_title_index = current_index.indexOf(">");
          var current_last_title_index = current_index.indexOf("</span>");
          // This variable containe "Index number" and "Title" and we are finding index.
          var current_title = current_index.slice(current_start_title_index + 1, current_last_title_index);
          // Find out the current index id.
          var current_index = current_title.indexOf("_");
          // "current_index" is the current index.
          current_index = current_title.substr(0, current_index);

          // Find out the caption title.
          var current_caption_title = current_title.indexOf("_caption_title:") + 15;
          var current_caption_link = current_title.indexOf("_caption_link:");
          // "current_caption_title" is the caption title.
          current_caption_title = current_title.slice(current_caption_title, current_caption_link);
          var caption_title = current_caption_title;
          // "current_caption_link" is the caption title.
          current_caption_link = current_title.slice(current_caption_link + 14);


          var caption_title_lenght = current_caption_title.length;
          if( caption_title_lenght > 30 )
          {
            current_caption_title = current_caption_title.substr(0, 30) + '..';
          }

          if( current_caption_title != null && current_caption_link!= null )
          {
            $('sitepagealbum_featured_album_caption').innerHTML =   current_caption_link;
          }
          else {
            $('sitepagealbum_featured_album_caption').innerHTML =  '';
          }


          $('sitepagealbum_featured_album_current_numbering').innerHTML =  current_index + '/' + num_of_slidehsow ;
        }
      });

      //more handle buttons
      nS8.addHandleButtons(handles8_more);
      //walk to item 3 witouth fx
      nS8.walk(0,false,true);
    });
  </script>
<?php } ?>

<div class="featured_slideshow_wrapper">
  <div class="featured_slideshow_mask">
    <div id="sitepagealbum_featured_album_im_te_advanced_box" class="featured_slideshow_advanced_box">
      <?php echo $image_text_var ?>
    </div>
  </div>

  <div class="featured_slideshow_option_bar">
    <div>
      <p class="buttons">
        <span id="sitepagealbum_featured_album_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
        <span id="sitepagealbum_featured_album_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title=<?php echo $this->translate("Stop") ?> ></span>
        <span id="sitepagealbum_featured_album_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title=<?php echo $this->translate("Play") ?> ></span>
        <span id="sitepagealbum_featured_album_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
      </p>
    </div>
    <span id="sitepagealbum_featured_album_caption"></span>
    <span id="sitepagealbum_featured_album_current_numbering" class="featured_slideshow_pagination"></span>
  </div>
</div>  
