<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 

include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagevideo/externals/styles/style_sitepagevideo.css');
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>

<?php
// Starting work for "Slide Show".
$image_text_var = '';

$image_count = 1;

foreach ($this->show_slideshow_object as $type => $member) { //echo $member->JOINP_COUNT;die;

  $memberPhoto = $this->htmlLink($member->getHref(), $this->itemPhoto($member->getOwner(), 'thumb.profile'));

  $content_link = $this->htmlLink($member->getHref(array()), $this->translate('View Member &raquo;'), array('class' => 'featured_slideshow_view_link'));

  $image_text_var .= "<div class='featured_slidebox'>";
  $image_text_var .= "<div class='featured_slidshow_img'>" . $memberPhoto . "</div>";

  if (!empty($member->displayname)) {

    $title =  $this->htmlLink($this->item('user', $member->user_id)->getHref(), $this->user($member->user_id)->displayname, array('title' => $member->displayname, 'target' => '_blank'));

    $image_text_var .='<h5>' . $this->htmlLink($this->item('user', $member->user_id)->getHref(), $this->user($member->user_id)->displayname, array('title' => $member->displayname, 'target' => '_parent')) . '</h5>';

    $count = $member->JOINP_COUNT; //Engine_Api::_()->getDbtable('membership', 'sitepage')->countPages($member->user_id);

    $image_text_var .= "<div class='featured_slidshow_info'>";
    $image_text_var .= $this->htmlLink(array('route' => 'sitepagemember_approve', 'action' => 'page-join', 'user_id' => $member->user_id), $this->translate(array('%s Page Joined', '%s Pages Joined', $count), $this->locale()->toNumber($count)), array('class' => 'smoothbox')); 
    $image_text_var .= "</div>";
  }

  if (!empty($content_link)) {
    $image_text_var .= "<h3 style='display:none'><span>" . $image_count++ . '_caption_title:' . $title . '_caption_link:' . $content_link . '</span>' . "</h3>";
  }

  $image_text_var .= "</div>";
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
        box: $('sitepagemember_featured_member_im_te_advanced_box'),
        items: $$('#sitepagemember_featured_member_im_te_advanced_box h3'),
        size: (width-10),
        handles: $$('#handles8 span'),
        addButtons: {previous: $('sitepagemember_featured_member_prev8'), stop: $('sitepagemember_featured_member_stop8'), play: $('sitepagemember_featured_member_play8'), next: $('sitepagemember_featured_member_next8') },
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
            $('sitepagemember_featured_member_caption').innerHTML =   current_caption_link;
          }
          else {
            $('sitepagemember_featured_member_caption').innerHTML =  '';
          }


          $('sitepagemember_featured_member_current_numbering').innerHTML =  current_index + '/' + num_of_slidehsow ;
        }
      });

      //more handle buttons
      nS8.addHandleButtons(handles8_more);
      //walk to item 3 witouth fx
      nS8.walk(0,false,true);
    });
  </script>
<?php } ?>
<div id="sp_m_f_<?php echo $this->identity ?>">
<div class="featured_slideshow_wrapper">
  <div class="featured_slideshow_mask">
    <div id="sitepagemember_featured_member_im_te_advanced_box" class="featured_slideshow_advanced_box">
      <?php echo $image_text_var ?>
    </div>
  </div>

  <div class="featured_slideshow_option_bar">
    <div>
      <p class="buttons">
        <span id="sitepagemember_featured_member_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
        <span id="sitepagemember_featured_member_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title=<?php echo $this->translate("Stop") ?> ></span>
        <span id="sitepagemember_featured_member_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title=<?php echo $this->translate("Play") ?> ></span>
        <span id="sitepagemember_featured_member_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
      </p>
    </div>
    <span id="sitepagemember_featured_member_caption"></span>
    <span id="sitepagemember_featured_member_current_numbering" class="featured_slideshow_pagination"></span>
  </div>
</div>
</div>
<script type="text/javascript">
 if($("sp_m_f_<?php echo $this->identity ?>").getParent('.layout_left') || $("sp_m_f_<?php echo $this->identity ?>").getParent('.layout_right')){
     $("sp_m_f_<?php echo $this->identity ?>").getElement(".featured_slideshow_mask").addClass('featured_slideshow_mask_down');
     $("sp_m_f_<?php echo $this->identity ?>").getElements(".featured_slidshow_img").addClass('featured_slidshow_img_down');
     
   }
</script>
<style type="text/css">
  .featured_slideshow_mask_down {
    height: 220px;
  }
  .featured_slidshow_img_down{
    padding-bottom: 5px;
    max-width: none;
    min-width: inherit;
    width: 100%;
    text-align: left;
  }
</style>