<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: package-detail.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sitecrowdfunding_package_page global_form_popup">
  <ul class="sitecrowdfunding_package_list">
    <li>
      <div class="Sitecrowdfunding_package_list_title">
        <h3><?php echo 'Package Details'; ?>: <?php echo $this->package->title; ?></h3>
      </div>
      <?php $item = $this->package; ?>
      <?php $this->detailPackage = 1; ?>
      <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/package/_packageInfo.tpl'; ?>
      <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo 'Close'; ?></button>
    </li>
  </ul>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>