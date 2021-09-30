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

<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)):?>
	<?php $randonNumber = $this->identityForWidget;?>
<?php else:?>
	<?php $randonNumber = $this->identity;?>
<?php endif;?>

<?php if(!$this->is_ajax){ ?>
  <div class="sesblog_small_tabs_container sesbasic_clearfix sesbasic_bxs sesbasic_sidebar_block">
    <div class="sesblog_small_tabs sesbasic_clearfix" <?php if(count($this->defaultOptions) ==1){ ?> style="display:none" <?php } ?>> 
    	<ul id="tab-widget-sesblog-<?php echo $randonNumber; ?>" class="sesbasic_clearfix">
       <?php 
         $defaultOptionArray = array();
         foreach($this->defaultOptions as $key=>$valueOptions){ 
         $defaultOptionArray[] = $key;
       ?>
       <li <?php if($this->defaultOpenTab == $key){ ?> class="active"<?php } ?> id="sesTabContainer_<?php echo $randonNumber; ?>_<?php echo $key; ?>">
         <a href="javascript:;" data-src="<?php echo $key; ?>" onclick="changeTabSes_<?php echo $randonNumber; ?>('<?php echo $key; ?>')"><?php echo $this->translate(($valueOptions)); ?></a>
       </li>
       <?php } ?>
      </ul>
    </div>
  <div class="sesbasic_tabs_content sesbasic_clearfix">
<?php } ?>
 <ul class="sesblog_blog_listing sesbasic_clearfix clear" id="sidebar-tabbed-widget_<?php echo $randonNumber; ?>">
    <?php include APPLICATION_PATH . '/application/modules/Sesblog/views/scripts/_sidebarWidgetData.tpl'; ?>
</ul>

<?php if(!$this->is_ajax){ ?>
    </div>
  </div>
<?php } ?>

<?php if(!$this->is_ajax):?>
  <script type="application/javascript"> 
    var availableTabs_<?php echo $randonNumber; ?>;
    var requestTab_<?php echo $randonNumber; ?>;
    <?php if(isset($defaultOptionArray)){ ?>
      availableTabs_<?php echo $randonNumber; ?> = <?php echo json_encode($defaultOptionArray); ?>;
    <?php  } ?>
    var defaultOpenTab ;
    
    function changeTabSes_<?php echo $randonNumber; ?>(valueTab){
      if(sesJqueryObject("#sesTabContainer_<?php echo $randonNumber; ?>_"+valueTab).hasClass('active'))
      return;
      var id = '_<?php echo $randonNumber; ?>';
      var length = availableTabs_<?php echo $randonNumber; ?>.length; console.log(availableTabs_<?php echo $randonNumber; ?>, length);
      for (var i = 0; i < length; i++){
        if(availableTabs_<?php echo $randonNumber; ?>[i] == valueTab){
          document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).addClass('active');
          sesJqueryObject('#sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).addClass('sesbasic_tab_selected');
        }else{
          sesJqueryObject('#sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).removeClass('sesbasic_tab_selected');
          document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).removeClass('active');
        }
      }
      if(valueTab) {
        if(document.getElementById("error-message_<?php echo $randonNumber;?>"))
          document.getElementById("error-message_<?php echo $randonNumber;?>").style.display = 'none';
        
        if(document.getElementById('sidebar-tabbed-widget_<?php echo $randonNumber; ?>'))
          document.getElementById('sidebar-tabbed-widget_<?php echo $randonNumber; ?>').innerHTML ='';
        
        sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').hide();
        
        document.getElementById('sidebar-tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = '<div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="margin-top:30px;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>';
        
        if(typeof(requestTab_<?php echo $randonNumber; ?>) != 'undefined')
        requestTab_<?php echo $randonNumber; ?>.cancel();
        
        if(typeof(requestViewMore_<?php echo $randonNumber; ?>) != 'undefined')
          requestViewMore_<?php echo $randonNumber; ?>.cancel();
	
        defaultOpenTab = valueTab;
        requestTab_<?php echo $randonNumber; ?> = new Request.HTML({
          method: 'post',
          'url': en4.core.baseUrl+"widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/openTab/"+valueTab,
          'data': {
            format: 'html',  
            params : <?php echo json_encode($this->params); ?>, 
            is_ajax : 1,
            identity : '<?php echo $randonNumber; ?>',
            height:'<?php echo $this->height;?>'
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
          
            sesJqueryObject('#map-data_<?php echo $randonNumber;?>').removeClass('checked');
            
            if(sesJqueryObject('#sesbasic_loading_cont_overlay_<?php echo $randonNumber?>').length)
            sesJqueryObject('#sesbasic_loading_cont_overlay_<?php echo $randonNumber?>').css('display','none');
            else
            sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').hide();
              
            sesJqueryObject('#error-message_<?php echo $randonNumber;?>').remove();
            var check = true;
            if(document.getElementById('sidebar-tabbed-widget_<?php echo $randonNumber; ?>'))
            document.getElementById('sidebar-tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = responseHTML;
            oldMapData_<?php echo $randonNumber; ?> = [];
            sesJqueryObject('.sesbasic_view_more_loading_<?php echo $randonNumber;?>').hide();
            if(typeof viewMoreHide_<?php echo $randonNumber; ?> == 'function')
            viewMoreHide_<?php echo $randonNumber; ?>();
          }
        });
        requestTab_<?php echo $randonNumber; ?>.send();
        return false;			
      }
    }
  </script> 
<?php endif;?>
