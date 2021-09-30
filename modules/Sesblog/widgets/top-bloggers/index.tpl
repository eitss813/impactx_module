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

	<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
  <?php if($this->widgetIdentity):?>
    <?php $randonnumber = $this->widgetIdentity;?> 
  <?php else:?>
    <?php $randonnumber = $this->identity;?>
  <?php endif;?>
  <div class="sesblog_top_bloger_list" style="position:relative;">
    <?php if($this->view_type == 'Vertical'):?>
      <ul id="widget_sesblog_<?php echo $randonnumber; ?>" class="sesbasic_sidebar_block  sesbasic_clearfix sesbasic_bxs">
        <div class="sesbasic_loading_cont_overlay" id="sesblog_widget_overlay_<?php echo $randonnumber; ?>"></div>
        <?php foreach( $this->paginator as $blogger ): ?>
          <?php $item = Engine_Api::_()->getItem('user',$blogger->user_id);?>
          <li class="sesbloger_top_sidebar_list">
              <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'sesblog_top_blog_thumb')) ?>
            <div class="sesblog_top_blog_info">
              <?php if(isset($this->ownernameActive)):?>
                <div class="sesblog_top_blog_title">
                  <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                </div>
              <?php endif;?>
               <?php if(isset($this->countActive)):?>
                <div class="sesblog_top_blog_list_stats">
                  <b><?php echo $blogger->blog_count; ?></b> <?php echo $this->translate('Blogs');?>
                </div>
              <?php endif;?>
            </div>
          </li>
        <?php endforeach; ?>
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
    <?php else:?>
      <ul id="widget_sesblog_<?php echo $randonnumber; ?>" class="sesbasic_sidebar_block  sesbasic_clearfix sesbasic_bxs">
        <div class="sesbasic_loading_cont_overlay" id="sesblog_widget_overlay_<?php echo $randonnumber; ?>"></div>
        <?php foreach( $this->paginator as $blogger ): ?>
          <?php $item = Engine_Api::_()->getItem('user',$blogger->user_id);?>
          <li class="sesbloger_top_gird_list" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
          <div class="sesbloger_top_gird_thumb_blog" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'), array('class' => 'sesblog_top_blog_thumb')) ?>
            <?php if(isset($this->ownernameActive) || isset($this->countActive)):?>
              <div class="sesblog_top_blog_info">
                <?php if(isset($this->ownernameActive)):?>
                  <div class="sesblog_top_blog_title">
                    <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                  </div>
                <?php endif;?>
                <?php if(isset($this->countActive)):?>
                  <div class="sesblog_top_blog_list_stats">
                    <b><?php echo $this->translate(array('%s Blogs', '%s Blog', $blogger->blog_count), $this->locale()->toNumber($blogger->blog_count)); ?></b>
                  </div>
              <?php endif;?>
              </div>
            <?php endif;?>
            </div>
          </li>
        <?php endforeach; ?>
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
    <?php endif;?>
  </div>
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
