<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featured-photos-carousel.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$photoSettings=  array();
$photoSettings['class'] = 'thumb';

?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<?php $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);?>
<?php if ($this->direction == 1) { ?>
 <?php  $j=0; $offset=$this->offset; ?>

    <?php foreach ($this->featuredPhotos as $photo): ?>
      <?php if($j% $this->itemsVisible ==0):?>
        <div class="Sitepagecontent_SlideItMoo_element Sitepagealbum_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif;?>
      <div class="featured_thumb_content">
        <a href="<?php echo $photo->getHref() ?>"  <?php if ($this->showLightBox): ?> onclick='openSeaocoreLightBox("<?php echo $photo->getHref() ?>") ?>");return false;' <?php endif; ?> title="<?php echo $photo->title; ?>" class="thumb_img">
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
					$tmpBody = strip_tags($sitepage_object->title);
					$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
					?>
					<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($photo->page_id, $photo->user_id, $photo->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
					<?php if($postedBy):?>
						<?php echo $this->translate('by ').
									$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
          <?php endif;?>
      	</span>
      </div>
        <?php $j++; $offset++;?>
       <?php if(($j% $this->itemsVisible) ==0):?>
           </div>
        </div>    
       <?php endif;?>     
    <?php endforeach; ?>
    <?php if($j <($this->totalItemsInSlide)):?>
       <?php for ($j;$j<($this->totalItemsInSlide); $j++ ): ?>
      <div class="featured_thumb_content">
      </div>
       <?php endfor; ?>
         </div>
      </div>
    <?php endif;?>
     
<?php } else {?>
<?php $count=$this->itemsVisible;
$j=0;  $offset=$this->offset+$count;?>
  <?php for ($i =$count; $i < $this->totalItemsInSlide; $i++):?>
      <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepagealbum_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>
          <?php if ($i < $this->count): ?>
            <div class="featured_thumb_content">
              <a href="<?php echo $this->featuredPhotos[$i]->getHref() ?>"  <?php if ($this->showLightBox): ?> onclick='openSeaocoreLightBox("<?php echo $this->featuredPhotos[$i]->getHref()?>");return false;' <?php endif; ?> title="<?php echo $this->featuredPhotos[$i]->title; ?>" class="thumb_img">
                <?php echo $this->itemBackgroundPhoto($this->featuredPhotos[$i], 'thumb.normal', $this->featuredPhotos[$i]->getTitle(), array(
                        'tag' => 'span',
                        )); 
                ?> 
             	</a>
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredPhotos[$i]->getOwner();
                $parent = Engine_Api::_()->getItem('sitepage_album', $this->featuredPhotos[$i]->album_id);
                echo $this->translate('in ').
                     $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));
                ?>
                <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredPhotos[$i]->page_id);?>
								<?php
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredPhotos[$i]->page_id, $this->featuredPhotos[$i]->user_id, $this->featuredPhotos[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
								<?php if($postedBy):?>
                <?php echo $this->translate('By ').
                     $this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                <?php endif;?>
            	</span>
             </div>
          <?php else: ?>
             <div class="featured_thumb_content">
             </div>
          <?php endif; ?>
      <?php $j++; $offset++;?>
      <?php if (($j % $this->itemsVisible) == 0): ?>
          </div>
        </div>
      <?php endif; ?>     
     
  <?php endfor;?>
 <?php $j=0; $offset=$this->offset; ?>
 <?php for ($i = 0; $i < $count; $i++): ?>
   <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepagealbum_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>        
            <div class="featured_thumb_content">
	            <a href="<?php echo $this->featuredPhotos[$i]->getHref() ?>"  <?php if ($this->showLightBox): ?> onclick='openSeaocoreLightBox("<?php echo $this->featuredPhotos[$i]->getHref()?>");return false;' <?php endif; ?> title="<?php echo $this->featuredPhotos[$i]->title; ?>" class="thumb_img">
                  <?php echo $this->itemBackgroundPhoto($this->featuredPhotos[$i], 'thumb.normal', $this->featuredPhotos[$i]->getTitle(), array(
                                'tag' => 'span',
                                )); 
                ?> 
	            </a>
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredPhotos[$i]->getOwner();
                $parent = Engine_Api::_()->getItem('sitepage_album', $this->featuredPhotos[$i]->album_id);
                echo $this->translate('in ').
                     $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));
                ?>
                <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredPhotos[$i]->page_id);?>
								<?php
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredPhotos[$i]->page_id, $this->featuredPhotos[$i]->user_id, $this->featuredPhotos[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
								<?php if($postedBy):?>
									<?php echo $this->translate('By ').
											$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                <?php endif;?>
            	</span>
	          </div>
         <?php $j++; $offset++; ?>
        <?php if ($j % $this->itemsVisible == 0): ?>
          </div>
        </div>
      <?php endif; ?>
  <?php endfor; ?>
 <?php } ?>
