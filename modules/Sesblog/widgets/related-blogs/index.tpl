<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
$allParams = $this->allParams;
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php  if(!$this->is_ajax): ?>
	<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/styles.css'); ?>
<?php endif;?>

<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)):?>
	<?php $randonNumber = $this->identityForWidget;?>
<?php else:?>
	<?php $randonNumber = $this->identity; ?>
<?php endif;?>

<ul id="widget_sesblog_<?php echo $randonnumber; ?>" class="sesblog_related_blogs sesbasic_clearfix sesbasic_bxs">
  <div class="sesbasic_loading_cont_overlay" id="sesblog_widget_overlay_<?php echo $randonnumber; ?>"></div>
  <?php foreach($this->paginator as $item):?>
		<li class="sesblog_grid sesbasic_bxs <?php if((isset($this->my_blogs) && $this->my_blogs)){ ?>isoptions<?php } ?>" style="width:<?php echo is_numeric($allParams['width_grid']) ? $allParams['width_grid'].'px' : $allParams['width_grid'] ?>;">
  <article>
    <div class="sesblog_grid_inner sesblog_thumb">
      <div class="sesblog_grid_thumb" style="height:<?php echo is_numeric($allParams['height_grid']) ? $this->height_grid.'px' : $allParams['height_grid'] ?>;">
        <a href="<?php echo $item->getHref(); ?>" data-url = "<?php echo $item->getType() ?>" class="sesblog_thumb_img"> <span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);"></span> </a>
        
        <?php echo $this->partial('widgets-data/_labels.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams)); ?>
      </div>
      <div>
        <div class="sesblog_grid_info clear clearfix sesbm">
          <div class="sesblog_grid_one_header">
            <?php echo $this->partial('widgets-data/_category.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'showin' => 1)); ?>
              
            <?php echo $this->partial('widgets-data/_readTime.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams));?>
          </div>
          <?php echo $this->partial('widgets-data/_title.tpl', 'sesblog', array('item' => $item, 'truncation' => $allParams['title_truncation_grid'], 'allParams' => $allParams, 'divclass' => 'sesblog_grid_info_title'));?>

          <?php if(Engine_Api::_()->getApi('core', 'sesblog')->allowReviewRating() && in_array('ratingStar', $allParams['show_criteria'])):?>
            <?php echo $this->partial('_blogRating.tpl', 'sesblog', array('rating' => $item->rating, 'class' => 'sesblog_list_rating sesblog_list_view_ratting', 'style' => ''));?>
          <?php endif;?>
          
          <div class="sesblog_grid_meta_block">
            <?php echo $this->partial('widgets-data/_posted_by.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 2)); ?>
          </div>
        </div>
        <div class="sesblog_grid_one_footer">
          <?php echo $this->partial('widgets-data/_date.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'viewType' => 4));?>
        
          <div class="sesblog_list_stats sesbasic_text_light">
            <?php echo $this->partial('widgets-data/_stats.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'readtime' => 1));?>
          </div>
        </div>
      
        <?php echo $this->partial('widgets-data/_buttons.tpl', 'sesblog', array('item' => $item, 'allParams' => $allParams, 'plusicon' => $allParams['socialshare_enable_gridview1plusicon'], 'sharelimit' => $allParams['socialshare_icon_gridview1limit']));?>
      </div>
  </article>
</li>
  <?php endforeach;?>
  <?php if(isset($this->widgetName)){ ?>
		<div class="sidebar_privew_next_btns">
			<div class="sidebar_previous_btn">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
					'id' => "widget_previous_".$randonnumber,
					'onclick' => "widget_previous_$randonnumber()",
					'class' => 'buttonlink previous_icon'
				)); ?>
			</div>
			<div class="sidebar_next_btns">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
					'id' => "widget_next_".$randonnumber,
					'onclick' => "widget_next_$randonnumber()",
					'class' => 'buttonlink_right next_icon'
				)); ?>
			</div>
		</div>
	<?php } ?>
</ul>

<?php if(isset($this->widgetName)){ ?>
  <script type="application/javascript">
		var anchor_<?php echo $randonnumber ?> = sesJqueryObject('#widget_sesblog_<?php echo $randonnumber; ?>').parent();
		function showHideBtn<?php echo $randonnumber ?> (){
			sesJqueryObject('#widget_previous_<?php echo $randonnumber; ?>').parent().css('display','<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
			sesJqueryObject('#widget_next_<?php echo $randonnumber; ?>').parent().css('display','<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');	
		}
		showHideBtn<?php echo $randonnumber ?> ();
		function widget_previous_<?php echo $randonnumber; ?>(){
			sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').show();
			new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					is_ajax: 1,
					params :'<?php echo json_encode($this->params); ?>', 
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					anchor_<?php echo $randonnumber ?>.html(responseHTML);
					sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').hide();
					showHideBtn<?php echo $randonnumber ?> ();
				}
			}).send()
		};

		function widget_next_<?php echo $randonnumber; ?>(){
			sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').show();
			new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					is_ajax: 1,
					params :'<?php echo json_encode($this->params); ?>' , 
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					anchor_<?php echo $randonnumber ?>.html(responseHTML);
					sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').hide();
					showHideBtn<?php echo $randonnumber ?> ();
				}
			}).send();
		};
	</script>
<?php } ?>
