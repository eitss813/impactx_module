<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css');
?>
<?php $i = 0; ?>
<script type="text/javascript">
  var paginatePagePhotos = function(pages, url) {  	

   $('subcategory_backgroundimage').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" /></center>';

  	var is_ajax = 1;
    var url = url;
   url = url + '/pages/' + pages;
   if (history.pushState)
    history.pushState( {}, document.title, url );
   else{
    window.location.hash = photoUrl;
   }
     en4.core.request.send(new Request.HTML({
      'url' : url,
      'method' : 'get',
         'data' : {
        'format' : 'html', 
        'is_ajax' :  is_ajax,
         'pages' : pages       
      }  
    }), {
      'element' : $('sitepage_profile_photo_anchors').getParent()
    });
  }
</script>

<a id="sitepage_profile_photo_anchors" style="position:absolute;"></a>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitepage_viewpages_head">
	<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>	
	  <?php echo $this->sitepage->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
    <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->album->getTitle();?>
  </h2>  
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumeditphoto', 5) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
	<div class="layout_right" id="communityad_editphotos">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumeditphoto', 5),"loaded_by_ajax"=>1,'widgetId'=>'page_editphotos'))?>
	</div>
<?php endif;?>
<div class="layout_middle">
<h3><?php echo $this->translate('Edit Photos');?></h3>
<p><?php echo $this->translate('Here, you can edit photos of this album.');?></p>
<br />
<?php $url = $this->url(array('action' => 'edit-photos','page_id' => $this->page_id, 'album_id' => $this->album_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), 'sitepage_albumphoto_general', true);?>
	<?php if($this->total_images > $this->photos_per_page) :?>
	
		<div class="sitepage_profile_album_paging">
		  <?php $next_page = $this->currentPageNumbers+1; ?>
		  <?php $previous_page = $this->currentPageNumbers-1; ?>
		  <?php $maxpages = $this->maxpage;?>
		  <?php if($this->maxpage >= $next_page) :?>
			  <div id="user_group_members_next">
					<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
						'onclick' => "paginatePagePhotos('$maxpages', '$url')",
					)); ?>
				</div>
				<div id="user_group_members_next">
				  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
				    'onclick' => "paginatePagePhotos('$next_page', '$url')",
				  )); ?>
				</div>
			<?php endif; ?>
			<div class="paging_count">
		 		<?php foreach($this->pagearray as $values) :
				 if($values['link'] == 1) { 	
				 	 echo $values['page'] ?>
				 <?php  }
				 else { ?>
				   <a href='javascript:void(0);' onclick="paginatePagePhotos('<?php echo $values['page'] ?>','<?php echo $url ?>')"><?php echo $values['page'] ?></a>
				 	  <?php
				 }
			 	endforeach; ?>
		 	</div> 	
	 		<?php if($this->pstart != $this->currentPageNumbers):?>
				<?php if($this->currentPageNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous') , array(
			          'onclick' => "paginatePagePhotos('$previous_page', '$url')",
			      )); ?>
				 	</div> 
				<?php endif; ?>
		    <div id="user_group_members_previous">		
		    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
		      'onclick' => "paginatePagePhotos('$this->pstart', '$url')",
		    )); ?>
			  </div> 
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" id="subcategory_backgroundimage" class="sitepage_album_box">
	  <?php echo $this->form->album_id; ?>
	  <ul class='sitepages_editphotos'>
	    <?php foreach( $this->photos as $photo ): ?>
	      <li>
	        <div class="sitepages_editphotos_photo">	      
	          <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
	        </div>
	        <div class="sitepages_editphotos_info">
	          <?php 
	            $key = $photo->getGuid();
	            echo $this->form->getSubForm($key)->render($this);	            
	          ?>
				    <div class="albums_editphotos_cover">
				    	<input id="cover_<?php echo $photo->getIdentity() ?>" type="radio" name="cover" value="<?php echo $photo->file_id ?>" 
	          <?php if(empty($this->album->photo_id) && $i==0): $i = 1;?>	          
	            checked="checked"
	          <?php else: ?>
	          <?php if( $this->album->photo_id == $photo->file_id ): ?> 
	             checked="checked"
	          <?php endif;?>
	           <?php endif;?>
	           />
				    </div>
				    <div class="albums_editphotos_label">
				    	<label for="cover_<?php echo $photo->getIdentity() ?>"><?php echo $this->translate('Album Cover');?></label>
				    </div>				    
	        </div>
	      </li>
	    <?php endforeach; ?>
	  </ul><br />
	  <div class="form-wrapper" >
	  	<div class="form-element">
			  <button type="submit" id="submit" name="submit"><?php echo $this->translate('Save Changes');?></button> <?php echo $this->translate(' or ');?><a onclick="javascript:history.go(-1);return false;" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('cancel');?></a>
	    </div>
	 </div>
	  <br />
	</form>
	<?php if($this->total_images > $this->photos_per_page) :?>
		<div class="sitepage_profile_album_paging">
		  <?php $next_page = $this->currentPageNumbers+1; ?>
		  <?php $previous_page = $this->currentPageNumbers-1; ?>
		  <?php $maxpages = $this->maxpage;?>
	  	<?php if($this->maxpage >= $next_page) :?>
			  <div id="user_group_members_next">
			        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
			          'onclick' => "paginatePagePhotos('$maxpages', '$url')",
			        )); ?>
				</div>
				<div id="user_group_members_next">
				  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
				    'onclick' => "paginatePagePhotos('$next_page', '$url')",
				  )); ?>
				</div>
			<?php endif; ?>
			<div class="paging_count">
	 			<?php foreach($this->pagearray as $values) :
					if($values['link'] == 1) { 	
						echo $values['page'] ?>
					<?php  }
					else { ?>
						<a href='javascript:void(0);' onclick="paginatePagePhotos('<?php echo $values['page'] ?>', '<?php echo $url?>')"><?php echo $values['page'] ?></a>
					<?php
					}
	  		endforeach; ?>
	  	</div>	
	 		<?php if($this->pstart != $this->currentPageNumbers):?>
				<?php if($this->currentPageNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous') , array(
			      	'onclick' => "paginatePagePhotos('$previous_page', '$url')",
			      )); ?>
				 	</div> 
				<?php endif; ?>
	  		<div id="user_group_members_previous">
			    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
			      'onclick' => "paginatePagePhotos('$this->pstart', '$url')",
			    )); ?>
				</div> 
	    <?php endif; ?>
	  </div>
	<?php endif; ?>
</div>