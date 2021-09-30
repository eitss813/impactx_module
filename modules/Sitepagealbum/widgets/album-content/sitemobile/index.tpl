<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php if (!Engine_Api::_()->sitemobile()->isApp() && empty($this->is_ajax)) : ?>
<?php 
$albumTitle = '' != trim($this->album->getTitle()) ? $this->album->getTitle() : $this->translate('Untitled');
$breadcrumb = array(
    array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title" => "Albums","icon" => "arrow-r"),
    array("title"=> $albumTitle,"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist")
 );
echo $this->breadcrumb($breadcrumb);?>
<?php endif; ?>

 <?php if (empty($this->is_ajax)) : ?>
   <?php if (!empty($this->total_images)): ?>
      <div class="sitepage_album_box" id="sitepagealbum_content">
        <ul class="thumbs thumbs_nocaptions" id="thumbs_nocaptions">
   <?php endif; ?>
 <?php endif; ?>
  <?php foreach ($this->photos as $photo):  ?> 
    <li id="thumbs-photo-<?php echo $photo->photo_id ?>">	                   
      <a href="<?php echo $photo->getHref(); ?>"  class="thumbs_photo">               
        <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
      </a>
      <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
           <?php if($photo->likes()->getLikeCount() > 0 || $photo->comment_count > 0) : ?>
          <span class="photo-stats" onclick='sm4.core.comments.comments_likes_popup("<?php echo $photo->getType();?>", <?php echo $photo->getIdentity();?>, "<?php echo $this->url(array('module' => 'core', 'controller' => 'photo-comment', 'action' => 'list'), 'default', 'true'); ?>")'>
            <?php if($photo->likes()->getLikeCount() > 0) : ?>
              <span class="f_small"><?php echo $photo->likes()->getLikeCount(); ?></span>
             <i class="ui-icon-thumbs-up-alt"></i>
            <?php endif;?>
            <?php if($photo->comment_count > 0) : ?>
                <span class="f_small"><?php echo $this->locale()->toNumber($photo->comment_count) ?></span>
                <i class="ui-icon-comment"></i>
            <?php endif;?>
          </span>
        <?php endif;?>
        <?php endif;?>
    </li>
  <?php endforeach; ?>

  <?php if (!empty($this->total_images)): ?>
    <?php if (empty($this->is_ajax)) : ?>
        </ul>
        <div class="feed_viewmore" id="view_more" onclick="viewMorePhoto()" >
          <a href="javascript:void(0);" id="feed_viewmore_link" class="ui-btn-default icon_viewmore" ><?php echo $this->translate('View More')?></a>
        </div>

   <div class="feeds_loading" id="loding_image" style="display: none;">
    <i class="ui-icon-spinner ui-icon icon-spin"></i>
   </div>
      </div>
  <?php endif; ?>
<?php endif; ?>
  <?php if (empty($this->is_ajax)) : ?>
    <?php if (empty($this->total_images)): ?>
                    <div class="tip">
                      <span>
                  <?php echo $this->translate('There are no photos in this page album.') ?>
                      </span>
                    </div>
     <?php endif; ?>
  <?php endif; ?>

  
  
<script type="text/javascript">
  function getNextPage(){
    return <?php echo sprintf('%d', $this->currentPageNumbers + 1) ?>
  }

  sm4.core.runonce.add(function() { 
    hideViewMoreLink();
  });
  
      function viewMorePhoto()
      {
        $.mobile.activePage.find('#view_more').css('display','none');
        $.mobile.activePage.find('#loding_image').css('display','');
        $.ajax({
          type: "POST", 
          dataType: "html",
          'url' : sm4.core.baseUrl + 'core/widget/index/mod/sitepagealbum/name/album-content',
          'data' : {
            format : 'html',
            isajax : 1,
            itemCountPerPage : '<?php echo $this->photos_per_page; ?>',
            pages: getNextPage(),
            'page_id': '<?php echo $this->sitepage->page_id; ?>',
            'album_id': '<?php echo $this->album_id; ?>',
            'slug': '<?php echo $this->album->getSlug(); ?>',
            'tab': '<?php echo $this->tab_selected_id; ?>'
          },
          success : function(responseHTML) {
            $.mobile.activePage.find('#sitepagealbum_content').find('.thumbs_nocaptions').append(responseHTML);
            $.mobile.activePage.find('#loding_image').css('display','none');
              hideViewMoreLink();
              }
            });
          sm4.core.runonce.trigger();
          sm4.core.refreshPage();

            return false;

          } 
          
          function hideViewMoreLink(){
            $.mobile.activePage.find('#view_more').css('display','<?php echo ( $this->maxpage == $this->currentPageNumbers || $this->total_images == 0 ? 'none' : '' ) ?>');
          }
</script>