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
     ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage_featured_carousel.css');

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/sitepageslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor1" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">
  var module = 'Sitepagealbum';
</script>
<script language="javascript" type="text/javascript">
  var slideshowalbum;
  window.addEvents ({
    'domready': function() { 
      slideshowalbum = new SocialengineSlideItMoo({
        module : 'Sitepagealbum',
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        in_one_row:<?php echo  $this->inOneRow_photo?>,
        no_of_row:<?php echo  $this->noOfRow_photo?>,
        curnt_limit:<?php echo $this->totalItemShow_photo;?>,
        category_id : <?php echo $this->category_id;?>,
        limit:<?php echo $this->totalItemShow_photo*2;?>,
        total:<?php echo $this->totalCount_photo; ?>,
        overallContainer: 'Sitepagealbum_SlideItMoo_outer',
        elementScrolled: 'Sitepagealbum_SlideItMoo_inner',
        thumbsContainer: 'Sitepagealbum_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        call_count:1,
        duration:<?php echo  $this->interval;?>,
        foward:'Sitepagealbum_SlideItMoo_forward',
        bck:'Sitepagealbum_SlideItMoo_back',
        itemsSelector: '.' +'Sitepagealbum_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_photo?>,
        itemHeight:<?php echo 146 * $this->noOfRow_photo?>,
        showControls:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitepagealbum_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitepagealbum_SlideItMoo_back' /* back button CSS selector */
				},
        startIndex:1,
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowalbum.options.call_count = 1;
        }
      });

      $('Sitepagealbum_SlideItMoo_back').addEvent('click', function () {slideshowalbum.sendajax(-1,slideshowalbum,'Sitepagealbum',"<?php echo $this->url(array('action'=>'featured-photos-carousel'),"sitepagealbum_extended",true); ?>");
        slideshowalbum.options.call_count = 1;

      });

      $('Sitepagealbum_SlideItMoo_forward').addEvent('click', function () { slideshowalbum.sendajax(1,slideshowalbum,'Sitepagealbum',"<?php echo $this->url(array('action'=>'featured-photos-carousel'),"sitepagealbum_extended",true); ?>");
        slideshowalbum.options.call_count = 1;
      });
     
      if((slideshowalbum.options.total -slideshowalbum.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitepagealbum_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitepagealbum_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$module = 'Sitepagealbum';
$photoSettings=  array();
$photoSettings['class'] = 'thumb';

?>
<ul class="Sitepagecontent_featured_slider">
  <li>
		<?php
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_photo > $this->totalItemShow_photo):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_photo > $this->totalItemShow_photo):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitepagealbum_SlideItMoo_outer" class="Sitepagecontent_SlideItMoo_outer Sitepagecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_photo)+$extra_width;?>px;">
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagealbum_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagealbum_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitepagecontent_SlideItMoo_back_disable" id="Sitepagealbum_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitepagealbum_SlideItMoo_inner" class="Sitepagecontent_SlideItMoo_inner">
        <div id="Sitepagealbum_SlideItMoo_items" class="Sitepagecontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitepagecontent_SlideItMoo_element Sitepagealbum_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_photo;?>px;">
              <div class="Sitepagecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredPhotos as $photo):?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $photo->page_id, $layout);?>
                       <div class="featured_thumb_content">
                          <a href="<?php echo $photo->getHref() ?>"  <?php if ($this->showLightBox): ?> onclick="openSeaocoreLightBox('<?php echo $photo->getHref()?>');return false;" <?php endif; ?> title="<?php echo $photo->title; ?>" class="thumb_img">
                             <?php echo $this->itemBackgroundPhoto($photo, 'thumb.normal', $photo->getTitle(), array(
                                'tag' => 'span',                               
                                )); 
                              ?>
                          </a>
                          <span class="show_content_des">
                            <?php
								              $owner = $photo->getOwner();
								              $parent = Engine_Api::_()->getItem('sitepage_album', $photo->album_id);
								              echo $this->translate('in ').
								                  $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));
														?>
														<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $photo->page_id);?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
														$tmpBody = strip_tags($sitepage_object->title);
														$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($photo->page_id, $photo->user_id, $photo->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>     
															<?php echo $this->translate('by ').
																		$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                            <?php endif;?>
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_photo);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id ="Sitepagealbum_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'. $this->layout()->staticBaseUrl        . 'application/modules/Sitepage/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id="Sitepagealbum_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward_disable" id="Sitepagealbum_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
