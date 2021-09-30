<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css')
?>

<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideTabs.js');
?>
<div class="generic_layout_container">
	<h3>
		<?php echo "Photos"; ?>
	</h3>
</div>


<?php if($this->can_edit): ?>
	<script type="text/javascript">
			var SortablesInstance;
			en4.core.runonce.add(function() {
				$$('.thumbs_nocaptions > li').addClass('sortable');
				SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
					clone: true,
					constrain: true,
					//handle: 'span',
					onComplete: function(e) {
						var ids = [];
						$$('.thumbs_nocaptions > li').each(function(el) {
							ids.push(el.get('id').match(/\d+/)[0]);
						});
						//console.log(ids);
						// Send request
						
						var url = '<?php echo $this->url(array('action' => 'album-order','page_id' => $this->sitepage->page_id), 'sitepage_albumphoto_general')?>';
						var request = new Request.JSON({
							'url' : url,
							'data' : {
								format : 'json',
								order : ids
							}
						});
						request.send();
					}
				});
			});
	</script>
<?php endif; ?>

<?php if (!empty($this->show_content)) : ?>
	<script type="text/javascript">
	  var paginatePageAlbums = function(page, pages) {
	  	$('album_image').innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitepage/externals/images/loader.gif" /></center>';
	    var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/photos-sitepage';	
	    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'page' : page,
	        'pages' : pages,
	        'isajax' : '1',
	        'tab' : '<?php echo $this->content_id ?>',
	        'itemCount' : '<?php echo $this->itemCount ?>',
          'itemCount_photo' : '<?php echo $this->itemCount_photo ?>',
          'albumsorder' : '<?php echo $this->albums_order ?>'
	      }
	    }), {
	      'element' : $('id_' + <?php echo $this->content_id ?>)
	    });
	  }
	  var paginatePagePhotos = function(pages,page) {
	  	$('photo_image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" /></center>';
	    var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/photos-sitepage';	
	    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'pages' : pages,
	        'page' : page,
	        'isajax' : '1',
	        'tab' : '<?php echo $this->content_id ?>',
          'itemCount' : '<?php echo $this->itemCount ?>',
          'itemCount_photo' : '<?php echo $this->itemCount_photo ?>',
          'albumsorder' : '<?php echo $this->albums_order ?>'
	      },
     		onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
           $('id_' + <?php echo $this->content_id ?>).innerHTML=responseHTML;
//          if($('white_content_default')){
//           $('white_content_default').addEvent('click', function(event) {
//            event.stopPropagation();
//            });
//          }
        }
	    }), {
	    //  'element' : $('id_' + <?php //echo $this->content_id ?>)
	    });
	  }  
	</script>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) :?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_photo">
			<?php echo $this->translate($this->sitepage->getTitle());?><?php echo $this->translate("'s Photos");?>
		</div>
	<?php endif;?>	
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
		<div class="layout_right" id="communityad_photo">
			<?php
                echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_photo')); 			 
			?>
		</div>
		<div class="layout_middle">
	<?php endif;?>
	<?php  if($this->can_edit && !empty($this->allowed_upload_photo)): ?>
		<div class="seaocore_add">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => 0, 'tab' => $this->identity_temp), 'sitepage_photoalbumupload', true) ?>'  class='buttonlink icon_sitepage_photo_new '><?php echo $this->translate('Create an Album'); ?></a>
		</div>
	<?php elseif(!empty($this->allowed_upload_photo) && ($this->sitepage->owner_id != $this->viewer_id)): ?>
		<div class="seaocore_add">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => $this->default_album_id, 'tab' => $this->identity_temp), 'sitepage_photoalbumupload', true) ?>'  class='buttonlink icon_sitepage_photo_new '><?php echo $this->translate('Add Photos'); ?></a>
		</div>
	<?php endif; ?>

	
		<div class="sitepage_profile_photos_head">
		<?php if($this->album_count > 0) :?>
     <b><?php echo $this->translate($this->sitepage->getTitle()); ?><?php echo $this->translate("'s Albums");?></b> &#8226;
     <?php echo $this->translate(array('%s Photo Album', '%s Photo Albums', count($this->paginator)),$this->locale()->toNumber(count($this->paginator))) ?>
		</div>
		<?php endif;?>
		<div class="sitepage_profile_album_paging" align="right">
    	<?php if($this->album_count > $this->albums_per_page) :?>
			<?php $next_pages = $this->currentAlbumPageNumbers+1; ?>
			<?php $previous_pages = $this->currentAlbumPageNumbers-1; ?>
			<?php $maxpagess = $this->maxpages;?>
			<?php if($this->maxpages >= $next_pages) :?>
				<div id="user_group_members_next">
					<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
							'onclick' => "paginatePageAlbums('$maxpagess', $this->currentPageNumbers)",
						)); ?>
				</div>
				<div id="user_group_members_next">
					<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
							'onclick' => "paginatePageAlbums('$next_pages', $this->currentPageNumbers)",
						)); ?>
				</div>
			<?php endif; ?>

			<div class="paging_count">
				<?php foreach($this->pagesarray as $valuess) :
					if($valuess['links'] == 1) {  	
						echo $valuess['pages'] ?>
							<?php 	  
					}
					else {
					?> 
					<a href='javascript:void(0);' onclick="paginatePageAlbums('<?php echo $valuess['pages'] ?>', '<?php echo $this->currentPageNumbers?>')"><?php echo $valuess['pages'] ?></a> 
						<?php
					}
				endforeach; ?>
			</div>  

			<?php if($this->pstarts != $this->currentAlbumPageNumbers):?>
				<?php if($this->currentAlbumPageNumbers != $this->pstarts+1): ?>
					<div id="user_group_members_previous">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Prev') , array(
							'onclick' => "paginatePageAlbums('$previous_pages', '$this->currentPageNumbers')",
						)); ?>
				</div> 
				<?php endif; ?>
				<div id="user_group_members_previous">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
					'onclick' => "paginatePageAlbums('$this->pstarts', '$this->currentPageNumbers')",
				)); ?>
				</div> 
			<?php endif; ?>
		<?php endif; ?>
	</div>
	
	<?php if(count($this->paginator) > 0) :?>
		<div id='album_image' class="sitepage_album_box clr">
			<ul class="thumbs thumbs_nocaptions">
		    <?php foreach ($this->paginator as $albums): ?>
		      <li style="height:200px;"  id="thumbs-photo-<?php echo $albums->photo_id ?>"> 
		      <?php if($albums->photo_id != 0): ?>
		        <a href="<?php echo $this->url(array( 'page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $this->identity_temp), 'sitepage_albumphoto_general') ?>" class="thumbs_photo" title="<?php echo $albums->title;?>">
		           <?php echo $this->itemBackgroundPhoto($albums, 'thumb.profile', $albums->getTitle(), array(
                                'tag' => 'span',
                                )); 
             		 ?>
		        </a>
		      <?php else: ?>
		        <a href="<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(),'tab' => $this->identity_temp), 'sitepage_albumphoto_general') ?>" class="thumbs_photo" title="<?php echo $albums->title;?>" >
		          <span><?php echo $this->itemPhoto($albums, 'thumb.profile'); ?></span>
		        </a>
		        <?php endif; ?>
		        <div class="sitepage_profile_album_title">
		        	<a href="<?php echo $this->url(array( 'page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $this->identity_temp), 'sitepage_albumphoto_general') ?>" title="<?php echo $albums->title;?>"><?php echo $albums->title;?></a>
		        </div>
		        <div class="sitepage_profile_album_stat">
		        	<?php echo $this->translate(array('%s photo', '%s photos', $albums->count()),$this->locale()->toNumber($albums->count())) ?>
		        	-		        	
		        	<?php echo $this->translate(array('%s like', '%s likes', $albums->like_count), $this->locale()->toNumber($albums->like_count)) ?>        
		        </div>
		      </li>		      
		    <?php endforeach; ?>
			</ul>
		</div>
	<?php else: ?>
    <div class="tip">
        <span> 
            <?php echo $this->translate("Nobody has created an album yet.");?>
        </span>
    </div>
  <?php endif;?> 
	
	<?php if(count($this->paginators) > 0) :?>
		<div class="sitepage_profile_photos_head">
			<b><?php echo $this->translate('Photos by Others');?></b> &#8226;
			<?php echo $this->translate(array('%s photo', '%s photos', count($this->paginators)),$this->locale()->toNumber(count($this->paginators))) ?>
		</div>	
	<?php endif; ?>
	
	<div class="sitepage_profile_album_paging" align="right">
		<?php if($this->total_images > $this->photos_per_page) :?>
		  <?php $next_page = $this->currentPageNumbers+1; ?>
		  <?php $previous_page = $this->currentPageNumbers-1; ?>
		  <?php $maxpages = $this->maxpage;?>
		  <?php if($this->maxpage >= $next_page) :?>
			  <div id="user_group_members_next">
			        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
			          'onclick' => "paginatePagePhotos('$maxpages', '$this->currentAlbumPageNumbers')",
			        )); ?>
				</div>
				<div id="user_group_members_next">
				  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
				    'onclick' => "paginatePagePhotos('$next_page', '$this->currentAlbumPageNumbers')",
				  )); ?>
				</div>
			<?php endif; ?>
			<div class="paging_count">
				<?php foreach($this->pagearray as $values) :
				 if($values['link'] == 1) { 	
				 	 echo $values['page'] ?>
				 <?php  }
				 else { ?>
				   <a href='javascript:void(0);' onclick="paginatePagePhotos('<?php echo $values['page'] ?>', '<?php echo $this->currentAlbumPageNumbers ?>')"><?php echo $values['page'] ?></a>
					<?php
				}
				endforeach; ?>
			</div>	
	 		<?php if($this->pstart != $this->currentPageNumbers):?>
			 <?php if($this->currentPageNumbers != $this->pstart+1): ?>
			    <div id="user_group_members_previous">
		        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Prev') , array(
		          'onclick' => "paginatePagePhotos('$previous_page', '$this->currentAlbumPageNumbers')",
		        )); ?>
			 	</div> 
			 <?php endif; ?>
		    <div id="user_group_members_previous">	
			    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
			      'onclick' => "paginatePagePhotos('$this->pstart', '$this->currentAlbumPageNumbers')",
			    )); ?>
			 </div> 
		  <?php endif; ?>
		<?php endif; ?>
	</div>
	
	<?php if(count($this->paginators) > 0) :?>
		<div id='photo_image' class="sitepage_album_box clr">
			<ul class="sitepage_thumbs">
        <?php $k =0;?>
		    <?php foreach ($this->paginators as $photo): ?>
		      <li>
		        <?php //if(!$this->showLightBox):?>
<!--              <a class="thumbs_photo" href="<?php //echo $photo->getHref(); ?>" title="<?php //echo $photo->title;?>">
                <span style="background-image: url(<?php //echo $photo->getPhotoUrl('thumb.profile'); ?>);"></span>
              </a>-->
            <?php //else:?>             
              <a href="<?php echo $photo->getHref() ?>"  <?php if(SEA_SITEPAGEALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $photo->getHref() . '/type/creation_date' . '/count/'.$this->total_images . '/offset/' . $k. '/owner_id/' . $this->viewer_id; ?>');return false;" <?php endif;?> class="thumbs_photo">
					 <?php echo $this->itemBackgroundPhoto($photo, 'thumb.profile', $photo->getTitle(), array(
                                'tag' => 'span',
                                )); 
             		 ?>
              </a>    
            <?php //endif; ?>
		      </li>
          <?php $k++;?>
		    <?php endforeach; ?>
			</ul>
		</div> 
		<?php endif; ?>
		<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
			</div>		
	 <?php endif; ?>
<?php endif;?>
<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<script type="text/javascript">
    var photo_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumwidget', 3);?>'; 
    var adwithoutpackage = '<?php echo Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage) ?>';
    var page_communityad_integration = '<?php echo $page_communityad_integration; ?>';
		var is_ajax_divhide = '<?php echo $this->isajax;?>';
	  var execute_Request_Photo = '<?php echo $this->show_content;?>';
	  var show_widgets = '<?php echo $this->widgets ?>';
	//window.addEvent('domready', function () {	   	
	  var PhototabId = '<?php echo $this->module_tabid;?>';	   
    var PhotoTabIdCurrent = '<?php echo $this->identity_temp; ?>';	
    var globalContentElement = en4.seaocore.getDomElements('content');

    if (PhotoTabIdCurrent == PhototabId) {
    	if(page_showtitle != 0) {
    		if($('profile_status') && show_widgets == 1) {
				  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Photos');?></h2>";	
    		}
    		if($('layout_photo')) {
			    $('layout_photo').style.display = 'block';
			  }	
    	}

      hideWidgetsForModule('sitepagealbum');
		  prev_tab_id = '<?php echo $this->content_id; ?>'; 
		  prev_tab_class = 'layout_sitepage_photos_sitepage';   
		  execute_Request_Photo = true;
		  hideLeftContainer (photo_ads_display, page_communityad_integration, adwithoutpackage);
    }	  
    else if (is_ajax_divhide != 1) {	  	
	  	if($(globalContentElement).getElement('.layout_sitepage_photos_sitepage')) {
				$(globalContentElement).getElement('.layout_sitepage_photos_sitepage').style.display = 'none';
		  }	
	  	
	  }
   //});	
	$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
		$(globalContentElement).getElement('.layout_sitepage_photos_sitepage').style.display = 'block';
    if(page_showtitle != 0) {
    	if($('profile_status') && show_widgets == 1) {
			  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Photos');?></h2>";	
    	}	    	
    }	
	
		
    hideWidgetsForModule('sitepagealbum');
		$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }	
		
		if (prev_tab_id != '<?php echo $this->content_id; ?>') {
			execute_Request_Photo = false;
			prev_tab_id = '<?php echo $this->content_id; ?>';		
			prev_tab_class = 'layout_sitepage_photos_sitepage';   	
		}
		
		if(execute_Request_Photo == false) {	
			ShowContent('<?php echo $this->content_id; ?>', execute_Request_Photo, '<?php echo $this->identity_temp?>', 'photo', 'sitepage', 'photos-sitepage', page_showtitle, 'null', photo_ads_display, page_communityad_integration, adwithoutpackage, '<?php echo $this->itemCount ?>', '<?php echo $this->itemCount_photo ?>', null, '<?php echo $this->albums_order ?>');
			execute_Request_Photo = true;    		
		}   

		if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1);?>' && photo_ads_display == 0)
			{setLeftLayoutForPage();}
	}); 
</script>
<?php //if($this->showLightBox):?>
<?php
  //include APPLICATION_PATH . '/application/modules/Sitepagealbum/views/scripts/_lightboxImage.tpl';
?>
<?php //endif; ?>

<?php if($this->count == 0):  ?>
   <style>
	   .generic_layout_container.layout_sitepage_page_videos {
		   display: none !important;
	   }
  </style>
<?php endif; ?>
