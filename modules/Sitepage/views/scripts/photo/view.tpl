<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css');
    

    
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js');
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete',
  ));
?>

<?php 
  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
  if (empty ($fbmodule) || empty($fbmodule->enabled) || (Engine_Api::_()->seaocore()->checkVersion($fbmodule->version, '4.2.3') != 1))
   $enable_facebookse = 0;
   
  else 
     $enable_facebookse = 1;
?>

<?php if(empty($this->isajax)): ?>
<div class="layout_middle">
	<div class="sitepage_viewpages_head">
		<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?> 
		<h2>
		  <?php $link =  $this->htmlLink(array( 'route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Albums')) ?>
		  <?php echo $this->translate('%1$s  &raquo; ' .  $link . ' &raquo;  %2$s',
		    $this->sitepage->__toString(),
		    
		  $this->album->__toString()); ?>
		</h2>
	</div>

<?php 
	   if($enable_facebookse) { ?>
       <div id="fbsitepage_photo2">
          <script type="text/javascript">
                    
            var fblike_moduletype = 'sitepage_photo';
            var fblike_moduletype_id = '<?php echo $this->image->getIdentity() ?>';
         </script>
        <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
       </div>
              
       
 <?php } ?>	
	<div class='sitepage_photo_view'>
	  <div class="sitepage_photo_nav">
    	<div id= 'photo_navigation1'>
	      <?php
	      echo $this->translate('Photo %1$s of %2$s in %3$s',
	              $this->locale()->toNumber($this->image->getCollectionIndex() + 1),
	              $this->locale()->toNumber($this->album->count()),(string) $this->album->getTitle())
	      ?>
    	</div>
    	<?php if ($this->album->count() > 1): ?>
        <div id='image_next_div1' style="display:block;">
		      <a href="javascript:void(0);" onclick="photopagination('<?php echo Engine_Api::_()->sitepage()->getHreflink($this->image->getPrevCollectible()) ?><?php echo "/tab/$this->tab_selected_id"?>')">
		        <?php echo $this->translate('Prev');?>
		      </a>
					<a href="javascript:void(0);" onclick="photopagination('<?php echo Engine_Api::_()->sitepage()->getHreflink($this->image->getNextCollectible()) ?><?php echo "/tab/$this->tab_selected_id"?>')">
		        <?php echo $this->translate('Next');?>
		      </a>
	      </div>
    	<?php endif; ?>
    </div>
  <div id='image_div'>      
<?php endif; ?> 

<!--FACEBOOK LIKE BUTTON START HERE-->
  
   <?php if (!empty($enable_facebookse) ) { ?>
               <div id="fbsitepage_photo1" style="display:none;">
                 <script type="text/javascript">
                    
                    var fblike_moduletype = 'sitepage_photo';
		                var fblike_moduletype_id = '<?php echo $this->image->getIdentity() ?>';
                 </script>
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
              </div>
            
  <?php } ?>

<div class='sitepage_photo_info'> 
<div id='photo_navigation2' style="display:none;">
<?php
echo $this->translate('Photo %1$s of %2$s in %3$s',
        $this->locale()->toNumber($this->image->getCollectionIndex() + 1),
        $this->locale()->toNumber($this->album->count()),(string) $this->album->getTitle())
?>
</div>
<?php if ($this->album->count() > 1): ?>
  <div id='image_next_div2' style="display:none;">  
    <a href="javascript:void(0);" onclick="photopagination('<?php echo Engine_Api::_()->sitepage()->getHreflink($this->image->getPrevCollectible()) ?><?php echo "/tab/$this->tab_selected_id"?>' )">
      <?php echo $this->translate('Prev');?>
    </a>
		<a href="javascript:void(0);" onclick="photopagination('<?php echo Engine_Api::_()->sitepage()->getHreflink($this->image->getNextCollectible()) ?><?php echo "/tab/$this->tab_selected_id"?>')">
      <?php echo $this->translate('Next');?>
    </a>
  </div>
<?php endif; ?>
<div class='sitepage_photo_container' id='media_image_div'>
  <a id='media_image_next' <?php if($this->album->count()>1):?> onclick="photopagination('<?php echo $this->escape(Engine_Api::_()->sitepage()->getHreflink($this->image->getNextCollectible())) ?><?php echo "/tab/$this->tab_selected_id"?>')" <?php endif; ?> title="<?php echo $this->image->getTitle();?>">
     <?php
     echo $this->itemPhoto($this->image, null , $this->image->getTitle(), array(
         'id' => 'media_image',
     ));
     ?> 
  </a>
</div>
<br />
<?php if( $this->canEdit ): ?>
  <div class="page_photo_right_options fright">
      <a class="icon_sitepage_photos_rotate_ccw" href="javascript:void(0)" onclick="$(this).set('class', 'icon_loading');en4.sitepage.rotate(<?php echo $this->image->getIdentity() ?>, 90).addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_rotate_ccw') }.bind(this)); loadingImage();" title="<?php echo $this->translate("Rotate Left"); ?>" ></a>

      <a class="icon_sitepage_photos_rotate_cw" href="javascript:void(0)" onclick="$(this).set('class', 'icon_loading');en4.sitepage.rotate(<?php echo $this->image->getIdentity() ?>, 270).addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_rotate_cw') }.bind(this)); loadingImage();" title="<?php echo $this->translate("Rotate Right"); ?>" ></a>

      <a class="icon_sitepage_photos_flip_horizontal" href="javascript:void(0)" onclick="$(this).set('class', 'icon_loading');en4.sitepage.flip(<?php echo $this->image->getIdentity() ?>, 'horizontal').addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_flip_horizontal') }.bind(this)); loadingImage();" title="<?php echo $this->translate("Flip Vertical"); ?>" ></a>

      <a class="icon_sitepage_photos_flip_vertical" href="javascript:void(0)" onclick="$(this).set('class', 'icon_loading');en4.sitepage.flip(<?php echo $this->image->getIdentity() ?>, 'vertical').addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_flip_vertical') }.bind(this)); loadingImage();" title="<?php echo $this->translate("Flip Horizontal"); ?>"></a>
       <input type="hidden" id='canReload' />
  </div>
<?php endif ?>

    <?php if($this->enablePinit): ?>
	  <div class="seaocore_pinit_button">
	  	<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://") . $_SERVER['HTTP_HOST'].$this->image->getHref()); ?>&media=<?php  echo urlencode(!preg_match("~^(?:f|ht)tps?://~i", $this->image->getPhotoUrl()) ?(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://") . $_SERVER['HTTP_HOST'] ):''.$this->image->getPhotoUrl()); ?>&description=<?php echo $this->image->getTitle(); ?>" class="pin-it-button" count-layout="horizontal"  id="new_pin" >Pin It</a>
			<script type="text/javascript" >
			   en4.core.runonce.add(function() {              
			      new Asset.javascript( 'http://assets.pinterest.com/js/pinit.js',{});                 
			   });			 
			</script>
	  </div>
   <?php endif;?>
    <?php echo $this->socialShareButton();?> 
<div style="overflow:hidden;">
	<?php if ($this->image->getTitle()): ?>
	  <div class="sitepage_photo_title">
	  <?php echo $this->image->getTitle(); ?>
	  </div>
	<?php endif; ?>
	<?php if ($this->image->getDescription()): ?>
	  <div class="sitepage_photo_description">
	  <?php echo $this->image->getDescription() ?>
	  </div>
	<?php endif; ?>
	<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) :?>
		<div class="seaotagcheckinshowlocation">
			<?php
				// Render LOCATION WIDGET
				echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin"); 
			?>
		</div>
	<?php endif;?>
	<div class="sitepage_photo_tags" id="media_tags" style="display: none;">
	<?php echo $this->translate('In this photo:'); ?>
	</div>
	<div class="sitepage_photo_date">
	 <?php echo $this->translate('Added'); ?> <?php echo $this->timestamp($this->image->modified_date) ?>
	  <?php if($this->canTag): ?>
			- <a href='javascript:void(0);' onclick='taggerInstance.begin();'><?php echo $this->translate('Tag This Photo');?></a>	
		<?php endif; ?>
		<?php $editurl = $this->url(array('action' => 'photo-edit','photo_id' => $this->image->getIdentity(), 'album_id' => $this->album_id, 'page_id' => $this->sitepage->page_id, 'tab' => $this->tab_selected_id), 'sitepage_imagephoto_specific', true);?>
		<?php $removeurl = $this->url(array('action' => 'remove','photo_id' => $this->image->getIdentity(), 'album_id' => $this->album_id, 'page_id' => $this->sitepage->page_id, 'tab' => $this->tab_selected_id), 'sitepage_imagephoto_specific', true);?>
		<?php if($this->canEdit): ?>

		- <a href="javascript:void(0);" onclick="showSmoothbox('<?php  echo $this->image->getGuid(); ?>', 'edit', '<?php  echo $this->image->getType() ?>', '<?php echo $this->image->getIdentity() ?>', '<?php echo $editurl ?>');">
	    		<?php echo $this->translate('Edit');?>
	      </a>
		<?php endif; ?>    
		<?php if($this->canDelete): ?>
			- <a href="javascript:void(0);" onclick="showSmoothbox('<?php  echo $this->image->getGuid(); ?>', 'delete', '<?php  echo $this->image->getType() ?>', '<?php echo $this->image->getIdentity() ?>', '<?php echo $removeurl;?>');">
	    		<?php echo $this->translate('Delete');?>
	      </a>    				
		<?php endif; ?>

		  <?php if ( SEA_PHOTOLIGHTBOX_SHARE ): ?>
	        - 
	      <a href="javascript:void(0);" onclick="showSmoothbox('<?php  echo $this->image->getGuid(); ?>', 'share', '<?php  echo $this->image->getType() ?>', '<?php echo $this->image->getIdentity() ?>');">
	    		<?php echo $this->translate('Share');?>
	      </a>  
	 		<?php endif; ?>
			<?php if (SEA_PHOTOLIGHTBOX_REPORT): ?>
	          - 
	      <a href="javascript:void(0);" onclick="showSmoothbox('<?php  echo $this->image->getGuid(); ?>', 'report','<?php  echo $this->image->getType() ?>', '<?php echo $this->image->getIdentity() ?>');">
	    		<?php echo $this->translate('Report');?>
	      </a>
	    <?php endif; ?>   
			<?php if( SEA_PHOTOLIGHTBOX_DOWNLOAD): ?>
	         -
       <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
				<a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($this->image->getPhotoUrl()).'&file_id='.$this->image->file_id ?>" target='downloadframe'><?php echo $this->translate('Download')?></a>
			<?php endif; ?>   
	   <?php if( $this->canEdit && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO): ?>
								-
				<a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'photo', 'action' => 'make-page-profile-photo', 'photo' => $this->image->getGuid(), 'page_id' => $this->sitepage->page_id,'format' => 'smoothbox'), 'sitepage_imagephoto_specific', true); ?>', 'profilephoto');">
	    		<?php echo $this->translate('Make Page Profile Photo');?>
	      </a>
		<?php endif; ?> 
    <?php if($this->allowFeatured):?>
       	-
		  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('module' =>'sitepage','controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->image->photo_id), 'default', true)) . "'); return false;")) ?>
       -
			<a href="javascript:void(0);"  onclick='featuredpagealbumPhoto("<?php echo $this->image->photo_id;?>");'><span id="featured_sitepagealbum_photo" <?php if ($this->image->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitepagealbum_photo" <?php if (!$this->image->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>  
    <?php endif; ?> 

	</div>

</div>
</div><br />

 <?php //RENDER FACEBOOK COMMENT WIDGET IF HE HAS ENABLED THIS.
        $success_showFBCommentBox = 0;
        if(!empty($enable_facebookse)) {  
          $success_showFBCommentBox =  Engine_Api::_()->facebookse()->showFBCommentBox ('sitepagealbum');     
          
        }
        
       if ($success_showFBCommentBox != 1)
       	
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
       
       if ($success_showFBCommentBox) {
          $curr_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
         echo $this->content()->renderWidget("facebookse.facebookse-comments", array('module_type' => 'sitepage_photo', 'curr_url' => $curr_url, 'subject' => $this->subject()->getGuid(), 'task' => 1, 'type' => 'sitepage_photo', 'id' => $this->image->getIdentity()));
        }
      
        
  
  
  ?>

<?php if(empty($this->isajax)): ?>
	</div>
</div>
</div>
<?php endif; ?>

<script type="text/javascript">

var baseY = '0';

 function getPrevPhoto(){
   return '<?php echo Engine_Api::_()->sitepage()->getHreflink($this->image->getPrevCollectible()) ?>';
 }
  function getNextPhoto(){
   return '<?php echo Engine_Api::_()->sitepage()->getHreflink($this->image->getNextCollectible()) ?>';
 }

	<?php if($this->viewer()->getIdentity()):?>
		var taggerInstance;

		en4.core.runonce.add(function() {

        taggerInstance = new Tagger('media_image_next', {
				'title' : '<?php echo $this->translate('Tag This Photo');?>',
				'description' : '<?php echo $this->translate('Type a tag or select a name from the list.');?>',
				'createRequestOptions' : {
					'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
					'data' : {
						'subject' : '<?php echo $this->subject()->getGuid() ?>'
					}
				},
				'deleteRequestOptions' : {
					'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
					'data' : {
						'subject' : '<?php echo $this->subject()->getGuid() ?>'
					}
				},
				'cropOptions' : {
					'container' : $('media_image_next')
				},
				'tagListElement' : 'media_tags',
				'existingTags' : <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
				
				'suggestParam' : <?php if (!empty($this->sitepagememberEnabled)): ?><?php echo $this->action('suggest', 'photo', 'sitepage', array('sendNow' => false, 'includeSelf' => true, 'page_id' => $this->sitepage->page_id)) ?><?php else: ?><?php echo $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) ?><?php endif; ?>,
				
				'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
				'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
				'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
       // 'baseY':baseY1
			});
      // Remove the onclick attrib while tagging
			var onclickNext = $('media_image_next').getProperty('onclick');
			taggerInstance.addEvents({
				'onBegin' : function() { 
				$('media_image_next').setProperty('onclick','');
				},
				'onEnd' : function() {
				$('media_image_next').setProperty('onclick',onclickNext);
				}
			});



		});

	<?php endif;?>

 window.addEvent('keyup', function(e) { 
    if( e.target.get('tag') == 'html' || e.target.get('tag') == 'a'||
        e.target.get('tag') == 'body' ) {
      if( e.key == 'right' ) {
        photopagination(getNextPhoto());
      } else if( e.key == 'left' ) {
        photopagination(getPrevPhoto());
      }
    }
  });

  function featuredpagealbumPhoto(photo_id)
  {
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'sitepage/photo/featured',
      'data' : {
        format : 'html',
        'photo_id' : photo_id
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if($('featured_sitepagealbum_photo').style.display=='none'){
          $('featured_sitepagealbum_photo').style.display="";
          $('un_featured_sitepagealbum_photo').style.display="none";
        }else{
          $('un_featured_sitepagealbum_photo').style.display="";
          $('featured_sitepagealbum_photo').style.display="none";
        }
      }
    }), true);

    return false;
  }

  var loadingImage= function(){
     if(document.getElementById('media_image_div'))
		$('media_image').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif";

  };

	var photopagination = function(url)
	{       

        if(document.getElementById('media_image_div'))
       document.getElementById('media_image_div').innerHTML="<img src='"+en4.core.staticBaseUrl+"application/modules/Sitepage/externals/images/loader.gif'  class='' />";
         
        if(document.getElementById('page_lightbox_text'))
        document.getElementById('page_lightbox_text').style.display="none";
        if(document.getElementById('page_lightbox_user_options'))
        document.getElementById('page_lightbox_user_options').style.display="none";
         if(document.getElementById('page_lightbox_user_right_options'))
        document.getElementById('page_lightbox_user_right_options').style.display="none";
     

    en4.core.request.send(new Request.HTML({
			url : url,
			data : {
				format : 'html',
				isajax : 1
			},
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {

				$('image_div').innerHTML = responseHTML;      
        
         if($('photo_navigation1'))
				$('photo_navigation1').innerHTML = $('photo_navigation2').innerHTML;

				if($('image_next_div2')) {
					$('image_next_div1').innerHTML = $('image_next_div2').innerHTML;
				}   
        if ($('fbsitepage_photo1')) {
           $('fbsitepage_photo2').innerHTML = $('fbsitepage_photo1').innerHTML;
           $('fbsitepage_photo2').style.display = 'block';
            $('fbsitepage_photo1').destroy();
        }
        if (typeof FB != 'undefined') { 
          FB.XFBML.parse();
          
        }

			}
		}));
	};

	function showSmoothbox(photo_id, action, type, id, url)
	{
		var page_id = "<?php echo $this->sitepage->page_id; ?>";
		var album_id = "<?php echo $this->album_id; ?>";
		var tab = "<?php echo $this->tab_selected_id; ?>";

		//var photo_id = "<?php //$this->image->getGuid(); ?>";
		if(action == 'profilephoto') {
			var url = photo_id;
			Smoothbox.open(url);
			parent.Smoothbox.close;
		}
		else if(action == 'report') {
			Smoothbox.open(en4.core.baseUrl+'core/report/create/subject/' + photo_id + '/tab/' + tab + '/format/smoothbox');
			parent.Smoothbox.close;
		}
		else if(action == 'share') {
			Smoothbox.open(en4.core.baseUrl+ 'activity/index/share/type/' + type + '/id/' + id + '/tab/' + tab + '/format/smoothbox');
			parent.Smoothbox.close;
		}
		else if(action == 'edit') {
			Smoothbox.open(url);
			parent.Smoothbox.close;
		}
		else if(action == 'delete') {
			Smoothbox.open(url);
			parent.Smoothbox.close;
		}
	};
</script>