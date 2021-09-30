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

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php if($this->widgetIdentity):?>
	<?php $randonnumber = $this->widgetIdentity;?>
<?php else:?>
	<?php $randonnumber = $this->identity;?>
<?php endif;?>

<ul class="sesbasic_sidebar_block sesblog_review_sidebar_block sesbasic_bxs sesbasic_clearfix"  id="widget_sesblog_<?php echo $randonnumber; ?>" style="position:relative;">
	<div class="sesbasic_loading_cont_overlay" id="sesblog_widget_overlay_<?php echo $randonnumber; ?>"></div>
  <?php include APPLICATION_PATH . '/application/modules/Sesblog/views/scripts/_reviewsidebarWidgetData.tpl'; ?>
</ul>

<?php if(isset($this->widgetName)){ ?>
  <div class="sidebar_privew_next_btns">
    <div class="sidebar_previous_btn">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
        'id' => "widget_previous_".$randonnumber,
        'onclick' => "widget_previous_$randonnumber()",
        'class' => 'buttonlink previous_icon'
      )); ?>
    </div>
    <div class="Sidebar_next_btns">
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
			sesJqueryObject('#widget_previous_<?php echo $randonnumber; ?>').parent().css('display','<?php echo ( $this->results->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    	sesJqueryObject('#widget_next_<?php echo $randonnumber; ?>').parent().css('display','<?php echo ( $this->results->count() == $this->results->getCurrentPageNumber() ? 'none' : '' ) ?>');	
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
          page : <?php echo sprintf('%d', $this->results->getCurrentPageNumber() - 1) ?>
        },
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					anchor_<?php echo $randonnumber ?>.html(responseHTML);
					<?php if(isset($this->view_type) && $this->view_type == 'gridOutside'){ ?>
					jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({theme:"minimal-dark"});
				<?php } ?>
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
          page : <?php echo sprintf('%d', $this->results->getCurrentPageNumber() + 1) ?>
        },
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					anchor_<?php echo $randonnumber ?>.html(responseHTML);
					<?php if(isset($this->view_type) && $this->view_type == 'gridOutside'){ ?>
					jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({theme:"minimal-dark"});
				<?php } ?>
					sesJqueryObject('#sesblog_widget_overlay_<?php echo $randonnumber; ?>').hide();
					showHideBtn<?php echo $randonnumber ?> ();
				}
      }).send();
		};

</script>
<?php } ?>
