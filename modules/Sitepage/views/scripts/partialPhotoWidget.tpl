<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialPhotoWidget.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');
  
  $this->headLink()
	  ->appendStylesheet($this->layout()->staticBaseUrl
	    . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css');       
  $i=0;
?>
<?php
$front = Zend_Controller_Front::getInstance(); 
$action = $front->getRequest()->getActionName();
$moduleName = $front->getRequest()->getModuleName();
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<script type="text/javascript">
  var submit_topageprofile = true;
  function ShowPhotoPage(pageurl) {
    if (submit_topageprofile) {
      window.location = pageurl;
    }
    else {
      submit_topageprofile = true;
    }
  }

</script>

<?php if(empty($this->showFullPhoto)):?>
<div class="sitepagealbum_sidebar">
	<?php if ((count($this->paginator) > 0)): ?>
	  <ul class="sitepagealbum_sidebar_thumbs">

	    <?php foreach ($this->paginator as $sitepagephoto):  ?>
	      <li class="mbot5"> 
	        <?php //if (!$this->showLightBox): ?>
<!--	          <a href="javascript:void(0)" onclick='ShowPhotoPage("<?php //echo $sitepagephoto->getHref() ?>")' title="<?php //echo $sitepagephoto->title; ?>"  class="thumbs_photo">		
	            <span style="background-image: url(<?php //echo $sitepagephoto->getPhotoUrl('thumb.normal'); ?>);"></span>
	          </a>-->
	        <?php //else: ?>           
	           <a href="<?php echo $sitepagephoto->getHref() ?>" <?php if(SEA_SITEPAGEALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $sitepagephoto->getHref() . '/type/' . $this->type . '/count/'. $this->count. '/offset/' . $i . '/urlaction/' . $this->urlaction; ?>');return false;" <?php endif;?> title="<?php echo $sitepagephoto->title; ?>" class="thumbs_photo">
	            <?php echo $this->itemBackgroundPhoto($sitepagephoto, 'thumb.normal', $sitepagephoto->getTitle(), array(
                                'tag' => 'span',
                                )); 
                ?>
	          </a>
	        <?php //endif; ?>
          <?php if($this->displayPageName):?> 
            <div class='sitepagealbum_thumbs_details'>	
                <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $sitepagephoto->page_id, $layout);?>
                <?php $parent = Engine_Api::_()->getItem('sitepage_album', $sitepagephoto->album_id);?>
								<?php echo $this->translate('in ').
										$this->htmlLink($parent->getHref(array('tab'=> $tab_id)), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));?>
               <?php echo $this->translate('of '); ?><?php echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagephoto->page_id)), $sitepagephoto->page_title,array('title' => $sitepagephoto->page_title)) ?>
            </div>         
          <?php endif;?>    
          <?php if($this->displayUserName):?>
            <?php if(!empty($sitepagephoto->owner_id)) :?>
              <div class='sitepagealbum_thumbs_details'>	
                <?php if($postedBy):?> 
									<?php $userItem = Engine_Api::_()->getItem('user', $sitepagephoto->owner_id);	 ?>         	
									<?php echo $this->translate('by '); ?><?php echo $this->htmlLink($userItem->getHref(),$userItem->getTitle(),array('title' => $userItem->getTitle()));?> 
                <?php endif;?>
              </div>
            <?php endif;?>
          <?php endif;?>      
	        <?php if($this->show_detail == 1):?>
	        	<?php if($this->show_info == 'comment') :?>
			        <div class='sitepagealbum_thumbs_details center'>	
		            <?php echo $this->translate(array('%s comment', '%s comments', $sitepagephoto->comment_count), $this->locale()->toNumber($sitepagephoto->comment_count)) ?>          
		          </div>
	          <?php elseif($this->show_info == 'like') :?>
		          <div class='sitepagealbum_thumbs_details center'>	
		            <?php echo $this->translate(array('%s like', '%s likes', $sitepagephoto->like_count), $this->locale()->toNumber($sitepagephoto->like_count)) ?>          
		          </div>
	          <?php endif;?>
          <?php endif;?>
	     </li>
       <?php $i++;?>
	    <?php endforeach; ?>
	  </ul>	
	<?php endif; ?>  
</div>
<?php else:?>

<div class="sitepagealbum_sidebar">
	<?php if ((count($this->paginator) > 0)): ?>
	  <ul class="generic_sitepagealbum_photo_widget">
	    <?php foreach ($this->paginator as $sitepagephoto):  ?>	   
	      <li class="mbot5"> 
          <div class="photo">
            <?php //if (!$this->showLightBox): ?>
<!--              <a href="javascript:void(0)" onclick='ShowPhotoPage("<?php //echo $sitepagephoto->getHref() ?>")' title="<?php //echo $sitepagephoto->title; ?>"  class="thumbs_photo">		
                <img src="<?php //echo $sitepagephoto->getPhotoUrl('thumb.normal'); ?>" class="thumb_normal" />
              </a>-->
            <?php //else: ?>
             <a href="<?php echo $sitepagephoto->getHref() ?>" <?php if(SEA_SITEPAGEALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $sitepagephoto->getHref() . '/type/' . $this->type . '/count/'. $this->count. '/offset/' . $i . '/urlaction/' . $this->urlaction; ?>');return false;" <?php endif;?> title="<?php echo $sitepagephoto->title; ?>" class="thumbs_photo">          
	            <?php
             	echo $this->itemPhoto($sitepagephoto, 'thumb.normal' , $sitepagephoto->getTitle(), array(
                 					'class' => 'thumb_normal',
             						));
             	?>
	          </a>
            <?php //endif; ?> 
          </div> 
          <?php if($this->displayPageName):?>
            <div class='sitepagealbum_thumbs_details'>	
               <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $sitepagephoto->page_id, $layout);?>
               <?php $parent = Engine_Api::_()->getItem('sitepage_album', $sitepagephoto->album_id);?>
								<?php echo $this->translate('in ').
								$this->htmlLink($parent->getHref(array('tab'=> $tab_id)), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));?>
               <?php echo $this->translate('of '); ?><?php echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagephoto->page_id)), $sitepagephoto->page_title,array('title' => $sitepagephoto->page_title)) ?>
            </div>         
          <?php endif;?> 
          <?php if($this->displayUserName):?>
            <?php if(!empty($sitepagephoto->owner_id)) :?>
              <?php if($postedBy):?> 
                <div class='sitepagealbum_thumbs_details'>	
                <?php $userItem = Engine_Api::_()->getItem('user', $sitepagephoto->owner_id);	 ?>         	
                <?php echo $this->translate('by '); ?><?php echo $this->htmlLink($userItem->getHref(),$userItem->getTitle(),array('title' => $userItem->getTitle()));?>                   </div>
              <?php endif;?>
            <?php endif;?>
          <?php endif;?>
	        <?php if($this->show_detail != 1):?>
	        	<?php if($this->show_info == 'comment') :?>
			        <div class='sitepagealbum_thumbs_details'>	
		            <?php echo $this->translate(array('%s comment', '%s comments', $sitepagephoto->comment_count), $this->locale()->toNumber($sitepagephoto->comment_count)) ?>          
		          </div>
	          <?php elseif($this->show_info == 'like'):?>
		          <div class='sitepagealbum_thumbs_details'>	
		            <?php echo $this->translate(array('%s like', '%s likes', $sitepagephoto->like_count), $this->locale()->toNumber($sitepagephoto->like_count)) ?>          
		          </div>
	          <?php endif;?>
          <?php endif;?>
	     </li>
       <?php $i++;?>
	    <?php endforeach; ?>
	  </ul>	
	<?php endif; ?>  
</div>
<?php endif; ?> 