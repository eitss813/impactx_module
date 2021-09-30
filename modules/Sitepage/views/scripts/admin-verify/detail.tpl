<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<div class="global_form_popup admin_page_stats">
  <h3><?php echo $this->translate('Verification Details'); ?></h3>
  <ul class="sitepage-view-detail-table">
    <li>
      <?php echo $this->htmlLink($this->resourceObj->getHref(), $this->itemPhoto($this->resourceObj, 'thumb.icon', '', array('align' => '')), array('target' => '_blank')) ?>
    </li>
    <li>
     <?php echo $this->translate('Verified Page:'); ?>
     <span><?php echo $this->htmlLink($this->resourceObj->getHref(), $this->translate(ucfirst($this->resource_title)), array('target' => '_blank')) ?></span>
    </li>
    <li>
      <?php echo $this->translate('Verified By:'); ?>
      <span><?php echo $this->htmlLink($this->posterObj->getHref(), $this->translate(ucfirst($this->poster_title)), array('target' => '_blank')) ?></span>
    </li>
    <li>
      <?php echo $this->translate('Verification Date:'); ?>
      <span><?php echo $this->locale()->toDateTime($this->verify_date, array('format' => 'MMMM d, y')); ?></span>
    </li>
    <li>
      <?php echo $this->translate('Comments:'); ?>
      <span><?php if (!empty($this->comments)): echo $this->comments;
                else :
                  echo $this->translate("---");
                endif; ?></span>
    </li>
    <li>
      <?php echo $this->translate('Total Verifications:'); ?>
      <span><?php echo $this->verify_count; ?></span>
    </li>    
  </ul>
  <br>
  <button  onclick='javascript:parent.Smoothbox.close();' ><?php echo $this->translate('Close'); ?></button>
</div>


<style type="text/css">
  .sitepage-view-detail-table li {
    margin-top: 10px;
  }
</style>


