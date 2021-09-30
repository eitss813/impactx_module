<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/main.css');
?>
<style>
.sitecrowdfunding_package_popup_stats > span
{
  float: left;
  line-height: 33px;
  width: 50%;
}
</style>
<div class="sitecrowdfunding_package_page global_form_popup">
  <ul class="sitecrowdfunding_package_list">
    <li>
      <div class="sitecrowdfunding_package_list_title"> 
        <div class="fright mtop5">
          <a class="buttonlink" href="javascript:void(0);" onclick="createAD()"><?php echo $this->translate("Create a Project"); ?> &raquo;</a>
 
        </div>
        <h3><?php echo $this->translate('Package Details'); ?> : <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
      </div>
      <?php $item = $this->package; ?>
      <?php $this->detailPackage = 1; ?>
      <div class="mbot10 sitecrowdfunding_package_popup_stats"><?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/package/_packageInfo.tpl'; ?></div>
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
    var url = '<?php echo $this->url(array("action" => "create", 'id' => $this->package->package_id), "sitecrowdfunding_project_general", true) ?>';

    parent.window.location.href = url;
    parent.Smoothbox.close();
  }
</script>