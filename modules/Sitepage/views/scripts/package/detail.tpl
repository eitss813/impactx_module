<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: package-detail.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>
 <div class="sitepage_package_page global_form_popup" style="margin:10px 0 0 10px;">
  <ul class="sitepage_package_list">
    <li style="width:650px;">
      <div class="sitepage_package_list_title">
        <div class="sitepage_create_link">
          <a href="javascript:void(0);" onclick="createAD()"><?php echo $this->translate("Create a Page"); ?> &raquo;</a>
        </div>
        <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
      </div>
       <?php $item=$this->package;?>
       <?php $this->detailPackage=1; ?>
       <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/package/_packageInfo.tpl'; ?>
      <button onclick='javascript:parent.Smoothbox.close()' class="fright"><?php echo $this->translate('Close'); ?></button>
    </li>
  </ul>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
<script type="text/javascript">

  function createAD() {
		var url='<?php echo $this->url(array("action"=>"create" ,'id' => $this->package->package_id), 'sitepage_general', true) ?>';

		parent.window.location.href=url;
		parent.Smoothbox.close();
  }
</script>