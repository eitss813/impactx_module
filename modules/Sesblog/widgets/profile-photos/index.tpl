<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/style_album.css'); ?> 
<?php if((!$this->is_ajax) && ($this->allow_create) && (Engine_Api::_()->sesblog()->checkBlogAdmin($this->blog))): ?> 
<div class="sesbasic_profile_tabs_top sesbasic_clearfix">
    <?php echo $this->htmlLink(array(
      'route' => 'sesblog_extended',
      'controller' => 'album',
      'action' => 'create',
      'blog_id' => $this->blog_id,
      ), $this->translate('Add New Album'), array(
      'class' => 'sesbasic_button fa fa-plus'
    )) ?>
    </div>
<?php endif; ?>

<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)):?>
  <?php $randonNumber = $this->identityForWidget;?>
<?php else:?>
  <?php $randonNumber = $this->identity;?>
<?php endif;?>

 <div class="sesblog_search_result sesbasic_clearfix sesbm" id="<?php echo !$this->is_ajax ? 'paginator_count_sesblog' : 'paginator_count_ajax_sesblog' ?>"><span id="total_item_count_sesblog"> 
 <?php echo $this->translate(array('%s album found', '%s albums found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount()))?>
 </div>
 
  <?php if(!$this->is_ajax): ?>
  <script type="application/javascript">
  var tabId_pPhoto = <?php echo $this->identity; ?>;
window.addEvent('domready', function() {
	tabContainerHrefSesbasic(tabId_pPhoto);	
});
  </script>
    <div id="scrollHeightDivSes_<?php echo $randonNumber; ?>">
      <ul class="sesblog_album_listings sesblog_browse_album_listings sesbasic_bxs sesbasic_clearfix" id="tabbed-widget_<?php echo $randonNumber; ?>">
  <?php endif;?>			 
  <?php foreach( $this->paginator as $album ): ?>
    <?php if($this->view_type == 1){ ?>
      <li id="thumbs-photo-<?php echo $album->photo_id ?>" class="sesblog_album_list_grid_thumb sesblog_album_list_grid sesea-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesea-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">  
	<a class="sesblog_album_list_grid_img" href="<?php echo Engine_Api::_()->sesblog()->getHref($album->getIdentity(),$album->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
	  <span class="main_image_container" style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normalmain'); ?>);"></span>
	  <div class="ses_image_container" style="display:none;">
	    <?php $image = Engine_Api::_()->sesblog()->getAlbumPhoto($album->getIdentity(),$album->photo_id);
	    foreach($image as $key=>$valuePhoto){?>
	      <div class="child_image_container"><?php echo $valuePhoto->getPhotoUrl('thumb.normalmain');;  ?></div>
	    <?php  }  ?>  
	    <div class="child_image_container"><?php echo $album->getPhotoUrl('thumb.normalmain'); ?></div>          
	  </div>
	</a>
	<?php  if((isset($this->socialSharing)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButton)){  ?>
	  <span class="sesblog_album_list_grid_btns">
	    <?php if(isset($this->socialSharing)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ 
	      //album viewpage link for sharing
	      $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $album->getHref());
	      ?>
	      <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $album, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
	    <?php }
	    $canComment = $this->blog->authorization()->isAllowed($this->viewer, 'comment');

	    if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && isset($this->likeButton) && $canComment){  ?>
	      <!--Album Like Button-->
	      <?php $albumLikeStatus = Engine_Api::_()->sesblog()->getLikeStatusBlog($album->album_id,'sesblog_album'); ?>
	      <a href="javascript:;" data-url='<?php echo $album->album_id; ?>' class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
		<i class="fa fa-thumbs-up"></i>
		<span><?php echo $album->like_count; ?></span>
	      </a>
	    <?php } if(isset($this->favouriteButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)){ ?>
	     <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesblog')->isFavourite(array('resource_type'=>'sesblog_album','resource_id'=>'album_id')); ?>
        <a href="javascript:;" data-url='<?php echo $album->album_id; ?>' class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesblog_albumfavourite <?php echo ($favStatus) ? 'button_active' : '' ; ?>">
          <i class="fa fa-heart"></i>
          <span><?php echo $album->favourite_count; ?></span>
        </a>
        <?php }
        ?>
        <?php if(Engine_Api::_()->sesblog()->checkBlogAdmin($this->blog)):?>
					<a href="javascript:void(0);" class="sesblog_album_list_grid_option_button sesbasic_icon_btn"><i class="fa fa-ellipsis-v"></i></a>
					<div class="sesblog_album_option_box">
						<?php echo $this->htmlLink(array('route' => 'sesblog_extended','controller' => 'album','action' => 'editphotos',
						'album_id' => $album->album_id), $this->translate('Manage Photos'), array('class' => 'fa fa-image')); ?>
						<?php echo $this->htmlLink(array('route' => 'sesblog_extended','controller' => 'album','action' => 'edit',
						'album_id' => $album->album_id), $this->translate('Edit Settings'), array('class' => 'fa fa-edit')); ?>
						<?php echo $this->htmlLink(array('route' => 'sesblog_extended','controller' => 'album','action' => 'delete',
						'album_id' => $album->album_id), $this->translate('Delete Album'), array('class' => 'smoothbox fa fa-trash')); ?>
					</div>
        <?php endif;?>
	  </span>
	<?php }  ?>
	<?php if(isset($this->like) || isset($this->favouriteCount) || isset($this->comment) ||  isset($this->view) || isset($this->title) || isset($this->photoCount) ||  isset($this->by)){ ?>
	  <p class="sesblog_album_list_grid_info sesbasic_clearfix<?php if(!isset($this->photoCount)) { ?> nophotoscount<?php } ?>">
	    <?php if(isset($this->title)) { ?>
	      <span class="sesblog_album_list_grid_title">
		<?php echo $this->htmlLink($album, $this->string()->truncate($album->getTitle(), $this->title_truncation),array('title'=>$album->getTitle())) ; ?>
	      </span>
	    <?php } ?>
	    <span class="sesblog_album_list_grid_stats">
	      <?php if(isset($this->by)) { ?>
		<span class="sesblog_album_list_grid_owner">
		  <?php echo $this->translate('By');
      	$albumOwner  = Engine_Api::_()->getItem('user',$album->owner_id);
      ?>
		  <?php echo $this->htmlLink($albumOwner->getHref(), $albumOwner->getTitle(), array('class' => 'thumbs_author')) ?>
		</span>
	      <?php }?>
	    </span>
	    <span class="sesblog_album_list_grid_stats sesbasic_text_light">
	      <?php if(isset($this->like) && isset($album->like_count)) { ?>
		<span class="sesblog_album_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count))?>">
		  <i class="fa fa-thumbs-up"></i>
		  <?php echo $album->like_count;?>
	      </span>
	      <?php } ?>
         <?php if(isset($this->favouriteCount) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
		  <span class="sesbasic_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $album->favourite_count), $this->locale()->toNumber($album->favourite_count))?>">
	    <i class="fa fa-heart"></i>
	    <?php echo $album->favourite_count;?>
		  </span>
			<?php } ?>
	      <?php if(isset($this->comment)) { ?>
		<span class="sesblog_album_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count))?>">
		  <i class="fa fa-comment"></i>
		  <?php echo $album->comment_count;?>
		</span>
	      <?php } ?>
	      <?php if(isset($this->view)) { ?>
		<span class="sesblog_album_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count))?>">
		  <i class="fa fa-eye"></i>
		  <?php echo $album->view_count;?>
		</span>
	      <?php } ?>
	      <?php if(isset($this->photoCount)) { ?>
		<span class="sesblog_album_list_grid_count" title="<?php echo $this->translate(array('%s photo', '%s photos', $album->count()), $this->locale()->toNumber($album->count()))?>" >
		  <i class="fa fa-image"></i> 
		  <?php echo $album->count();?>                
		</span>
	      <?php } ?>
	    </span>
	  </p>
	<?php } ?>
	<?php if(isset($this->photoCount)) { ?>
	  <p class="sesblog_album_list_grid_count">
	    <?php echo $this->translate(array('%s <span>photo</span>', '%s <span>photos</span>', $album->count()),$this->locale()->toNumber($album->count())); ?>
	  </p>
	<?php } ?>
      </li>
    <?php }?>
  <?php endforeach;?>
  <?php if($this->load_content == 'pagging'){ ?>
    <?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesblog"),array('identityWidget'=>$randonNumber)); ?>
  <?php } ?>
  <?php if(!$this->is_ajax){ ?>
     </ul>
    </div>  
    <?php if($this->load_content != 'pagging'){ ?>
      <div class="sesbasic_load_btn" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > 
	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'sesbasic_link_btn')); ?> 
      </div>
      <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;">
	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sesbasic/externals/images/loading.gif' />
      </div>
    <?php } ?>
  <?php } ?>
  
<?php if($this->load_content == 'auto_load'){ ?>
  <script type="text/javascript">
  window.addEvent('load', function() {
    sesJqueryObject(window).scroll( function() {
      var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
      var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
      if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
	document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
      }
    });
  });
  </script>
<?php } ?>
<script type="text/javascript">
  var params<?php echo $randonNumber; ?> = '<?php echo json_encode($this->params); ?>';
  var identity<?php echo $randonNumber; ?>  = '<?php echo $randonNumber; ?>';
  var searchParams<?php echo $randonNumber; ?>;
  function paggingNumber<?php echo $randonNumber; ?>(pageNum){
    sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','block');
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/profile-photos",
      'data': {
      format: 'html',
      page: pageNum,    
      params :params<?php echo $randonNumber; ?>, 
      is_ajax : 1,
      searchParams : searchParams<?php echo $randonNumber; ?>,
      identity : identity<?php echo $randonNumber; ?>,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      if($('loadingimgsesblog-wrapper'))
	sesJqueryObject('#loadingimgsesblog-wrapper').hide();
	sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','none');
	document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;	sesJqueryObject('#paginator_count_sesblog').find('#total_item_count_sesblog').html(sesJqueryObject('#paginator_count_ajax_sesblog').find('#total_item_count_sesblog').html());
	sesJqueryObject('#paginator_count_ajax_sesblog').remove();
      }
    })).send();
    return false;
  }
  var page<?php echo $randonNumber; ?> = '<?php echo $this->page + 1; ?>';
  viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
  function viewMore_<?php echo $randonNumber; ?> (){
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'widget/index/mod/sesblog/name/profile-photos/index/',
      'data': {
	format: 'html',
	page: page<?php echo $randonNumber; ?>,    
	params :params<?php echo $randonNumber; ?>, 
	is_ajax : 1,
	searchParams : searchParams<?php echo $randonNumber; ?>,
	identity : identity<?php echo $randonNumber; ?>,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
	if($('loadingimgsesblog-wrapper'))
	sesJqueryObject('#loadingimgsesblog-wrapper').hide();
	document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
	sesJqueryObject('#paginator_count_sesblog').find('#total_item_count_sesblog').html(sesJqueryObject('#paginator_count_ajax_sesblog').find('#total_item_count_sesblog'));
	sesJqueryObject('#paginator_count_ajax_sesblog').remove();
	//document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'block';
	document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
      }
    })).send();
    return false;
  };
</script>
