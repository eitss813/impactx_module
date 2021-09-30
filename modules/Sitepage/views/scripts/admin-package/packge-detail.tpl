<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: packge-detail.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Communityad/externals/styles/style_communityad.css');
 ?>
<div class="sitepage_package_page global_form_popup">
  <ul class="sitepage_package_list">
    <li>
      <div class="sitepage_package_list_title">
       
        <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->package->title; ?></h3>
      </div>
      <?php $item=$this->package;?>
      <?php $this->detailPackage=1; ?>
      <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/package/_packageInfo.tpl'; ?>
      <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo $this->translate('Close'); ?></button>
    </li>
  </ul>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>