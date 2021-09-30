<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tagscloud.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<script type="text/javascript">
  var tagAction = function(tag, url){
    //$('page').value = 1;
    $('tag').value = tag;
    window.location.href=url;
  } 
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adtagview', 3)  && $page_communityad_integration): ?>
  <div class="layout_right" id="communityad_tagcloud">
		<?php
			echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adtagview', 3),"loaded_by_ajax"=>0,'widgetId'=>"page_tagcloud")); 			 
		?>
  </div>
<?php endif; ?>


<div class="layout_middle">
  <h3><b><?php echo $this->translate('Popular Video Tags'); ?></b></h3>

  <?php echo $this->translate('Browse the tags created for videos by various members.'); ?>

  <br />
  <?php if (!empty($this->tag_array)): ?>
    <?php echo $this->form->render($this) ?>
    <div class="mtop10">
      <?php foreach ($this->tag_array as $key => $frequency): ?>
        <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
        <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $this->tag_id_array[$key]; ?>, "<?php echo $this->url(array('tag' => $this->tag_id_array[$key]), 'sitepagevideo_browse', true);?>")'; style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>&nbsp; 
      <?php endforeach; ?>
    </div>
    <br /><br /><br /><br /><br />
  <?php endif; ?>
</div>