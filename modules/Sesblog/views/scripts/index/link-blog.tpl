<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: link-blog.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<script type="text/javascript">

  function viewMore() {
    
    if ($('view_more'))
    $('view_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>"; 
      
    document.getElementById('view_more').style.display = 'none';
    document.getElementById('loading_image').style.display = '';
  
    var id = '<?php echo $this->blog_id; ?>';
    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sesblog/index/link-blog/blog_id/' + id ,
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('event_results').innerHTML = document.getElementById('event_results').innerHTML + responseHTML;
        document.getElementById('view_more').destroy();
        document.getElementById('loading_image').style.display = 'none';
      }
    })).send();
    return false;
  }
</script>

<?php if (empty($this->viewmore)): ?>
  <div class="sesbasic_items_listing_popup">
    <div class="sesbasic_items_listing_header">
         <?php echo $this->translate('Events in which you can link to blog') ?>
      <a class="fa fa-times" href="javascript:;" onclick='smoothboxclose();' title="<?php echo $this->translate('Close') ?>"></a>
    </div>
    <div class="sesbasic_items_listing_cont" id="event_results">
<?php endif; ?>

    <?php if (count($this->paginator) > 0) : ?>
      <form id='link_blog' name="link_blog" method="post" action="<?php echo $this->url();?>">
	<?php foreach ($this->paginator as $event): ?>
	  <div class="item_list">
	    <input type='checkbox' class='checkbox' name="event[]" value="<?php echo $event->event_id;?>" />
	    <div class="item_list_thumb">
	      <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.icon'), array('title' => $event->getTitle(), 'target' => '_parent')); ?>
	    </div>
	    <div class="item_list_info">
	      <div class="item_list_title">
		<?php echo $this->htmlLink($event->getHref(), $event->getTitle(), array('title' => $event->getTitle(), 'target' => '_parent')); ?>
	      </div>
	    </div>
	  </div>
	<?php endforeach; ?>
        <div class='buttons'>
	  <button type='submit'><?php echo $this->translate("Link Event") ?></button>
        </div>
      </form><br />
    <?php endif; ?>     
      
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1 && empty($this->viewmore)): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div class="sesbasic_view_more" id="view_more" onclick="viewMore();" >
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="loading_image" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sesbasic/externals/images/loading.gif' alt="Loading" />
          <?php echo $this->translate("Loading ...") ?>
        </div>
  <?php endif; ?>
     </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
  function smoothboxclose() {
    parent.Smoothbox.close();
  }
</script>
