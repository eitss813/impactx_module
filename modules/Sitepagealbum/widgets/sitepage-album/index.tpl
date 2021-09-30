<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if($this->paginator->getTotalItemCount()):?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagealbum_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>

	<ul class="thumbs clr sitepage_content_thumbs">
		<?php foreach ($this->paginator as $albums): ?>
      <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $albums->page_id, $layout);?>
			<li id="thumbs-photo-<?php echo $albums->photo_id ?>"> 
					<?php if($albums->photo_id != 0):?>
						<a class="thumbs_photo" href="<?php echo $albums->getHref(array( 'page_id' => $albums->page_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $tab_id)); ?>">
								 <?php echo $this->itemBackgroundPhoto($albums, 'thumb.normal', $albums->getTitle(), array(
                                'tag' => 'span',              
                                )); 
                              ?>
						</a>
          <?php else:?>
						<a class="thumbs_photo" href="<?php echo $albums->getHref(array( 'page_id' => $albums->page_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $tab_id)); ?>">
								<span><?php echo $this->itemPhoto($albums, 'thumb.normal'); ?></span>
						</a>
          <?php endif;?>
					<p class="thumbs_info">
						<span class="thumbs_title">
              <?php if($albums->featured):?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'fright', 'title' => $this->translate('Featured'))) ?>
              <?php endif;?>
							<?php if (($albums->price>0)): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'fright', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif; ?>
							<a href="<?php echo $this->url(array( 'page_id' => $albums->page_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $tab_id), 'sitepage_albumphoto_general') ?>" title="<?php echo $albums->title;?>"><?php echo $albums->title;?></a>
						</span>
              <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $albums->page_id);?>
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($albums->page_id, $albums->owner_id, $albums->getSlug()),  $sitepage_object->title) ?> <br />

						<?php echo $this->translate(array('%s photo', '%s photos', $albums->count()),$this->locale()->toNumber($albums->count())) ?>
						-		        	
						<?php echo $this->translate(array('%s like', '%s likes', $albums->like_count), $this->locale()->toNumber($albums->like_count)) ?>  
            - 
            <?php echo $this->translate(array('%s view', '%s views', $albums->view_count), $this->locale()->toNumber($albums->view_count)) ?>       
					</p>
				</li>		      
			<?php endforeach; ?>
		</ul>
	<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepagealbum"), array("orderby" => $this->orderby)); ?>

<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>


<script type="text/javascript">
  var pageAction = function(page){
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_page')){
				form=$('filter_form_page');
			}
    form.elements['page'].value = page;
    
		form.submit();
  } 
</script>