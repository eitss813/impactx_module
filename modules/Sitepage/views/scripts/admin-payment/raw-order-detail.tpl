<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: raw-order-detail.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="payment_transaction_detail_headline">
  <?php echo $this->translate("Raw Order Details") ?>
</h2>

<?php if (!is_array($this->data)): ?>

  <div class="error">
    <span>
      <?php echo $this->translate('Order could not be found.') ?>
    </span>
  </div>

<?php else: ?>

  <dl class="payment_transaction_details">
    <?php foreach ($this->data as $key => $value): ?>
      <dd>
        <?php echo $key ?>
      </dd>
      <dt>
      <?php echo $value ?>
      </dt>
    <?php endforeach; ?>
  </dl>

<?php endif; ?>