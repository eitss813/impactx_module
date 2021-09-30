<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headLink()
     ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage_featured_carousel.css');

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagevideo/externals/styles/style_sitepagevideo.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/sitepageslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">

  var module = 'Sitepagevideo';
</script>
<script language="javascript" type="text/javascript">
  var slideshowvideo;
  window.addEvents ({
    'domready': function() {
      slideshowvideo = new SocialengineSlideItMoo({
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        in_one_row:<?php echo  $this->inOneRow_video?>,
        no_of_row:<?php echo  $this->noOfRow_video?>,
        curnt_limit:<?php echo $this->totalItemShowvideo;?>,
        category_id:<?php echo $this->category_id;?>,
        total:<?php echo $this->totalCount_video; ?>,
        limit:<?php echo $this->totalItemShowvideo*2;?>,
        module : 'Sitepagevideo',
        call_count:1,
        foward:'Sitepagevideo_SlideItMoo_forward',
        bck:'Sitepagevideo_SlideItMoo_back',
        overallContainer: 'Sitepagevideo_SlideItMoo_outer',
        elementScrolled: 'Sitepagevideo_SlideItMoo_inner',
        thumbsContainer: 'Sitepagevideo_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        duration:<?php echo  $this->interval;?>,
        itemsSelector: '.Sitepagevideo_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_video?>,
        itemHeight:<?php echo 146 * $this->noOfRow_video?>,
        showControls:1,
        startIndex:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitepagevideo_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitepagevideo_SlideItMoo_back' /* back button CSS selector */
				},
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowvideo.options.call_count = 1;
        }
      });

      $('Sitepagevideo_SlideItMoo_back').addEvent('click', function () {slideshowvideo.sendajax(-1,slideshowvideo,'Sitepagevideo',"<?php echo $this->url(array('module' => 'sitepagevideo','controller' => 'index','action'=>'featured-videos-carousel'),'default',true); ?>");
        slideshowvideo.options.call_count = 1;

      });

      $('Sitepagevideo_SlideItMoo_forward').addEvent('click', function () { slideshowvideo.sendajax(1,slideshowvideo,'Sitepagevideo',"<?php echo $this->url(array('module' => 'sitepagevideo','controller' => 'index','action'=>'featured-videos-carousel'),'default',true); ?>");
        slideshowvideo.options.call_count = 1;
      });
     
      if((slideshowvideo.options.total -slideshowvideo.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitepagevideo_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitepagevideo_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$videoSettings=  array();
$videoSettings['class'] = 'thumb';

?>
<ul class="Sitepagecontent_featured_slider">
  <li>
		<?php
    $module = 'Sitepagevideo';
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_video > $this->totalItemShowvideo):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_video > $this->totalItemShowvideo):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitepagevideo_SlideItMoo_outer" class="Sitepagecontent_SlideItMoo_outer Sitepagecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_video)+$extra_width;?>px;">
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagevideo_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagevideo_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitepagecontent_SlideItMoo_back_disable" id="Sitepagevideo_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitepagevideo_SlideItMoo_inner" class="Sitepagecontent_SlideItMoo_inner">
        <div id="Sitepagevideo_SlideItMoo_items" class="Sitepagecontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitepagecontent_SlideItMoo_element Sitepagevideo_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_video;?>px;">
              <div class="Sitepagecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredVideos as $video):?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $video->page_id, $layout);?>
                       <div class="featured_thumb_content">
                          <a href="<?php echo $this->url(array('user_id' => $video->owner_id, 'video_id' =>  $video->video_id,'tab' => $tab_id,'slug' => $video->getSlug()),'sitepagevideo_view', true)?>" class="thumb_video">
														<div class="sitepagevideo_thumb_wrapper">
															<?php if ($video->duration): ?>
																<span class="sitepagevideo_length">
																	<?php
																		if ($video->duration > 360)
																			$duration = gmdate("H:i:s", $video->duration); else
																			$duration = gmdate("i:s", $video->duration);
																		if ($duration[0] == '0')
																			$duration = substr($duration, 1); echo $duration;
																	?>
																</span>
															<?php endif; ?>
															<?php  if ($video->photo_id): ?>
																<?php echo   $this->itemPhoto($video, 'thumb.normal'); ?>
															<?php else: ?>
																<img src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagevideo/externals/images/video.png" class="thumb_normal item_photo_video  thumb_normal" />
															<?php endif;?>
														</div>
												  </a>
                          <span class="show_content_des">
                            <?php
								              $owner = $video->getOwner();
								              echo $this->htmlLink($video->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($video->getTitle(), 45), 10),array('title' => $video->getTitle(),'class'=>'sitepagevideo_title'));
														?>
														<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $video->page_id);?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
														$tmpBody = strip_tags($sitepage_object->title);
														$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($video->page_id, $video->owner_id, $video->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>      
                            <?php echo $this->translate('by ').
								                  $this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                            <?php endif;?>
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_video);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <?php $module = 'Sitepagevideo';?>
      <div class="Sitepagecontent_SlideItMoo_forward" id ="Sitepagevideo_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id="Sitepagevideo_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward_disable" id="Sitepagevideo_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
