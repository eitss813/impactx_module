<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: finish.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<form method="get" action="<?php if (!empty($this->id)): echo $this->escape($this->url(array('page_id' => $this->id), 'sitepage_edit', true));
  else: echo $this->escape($this->url(array('action' => 'manage'), 'sitepage_general', true));
  endif; ?>"
      class="global_form" enctype="application/x-www-form-urlencoded">
  <div>
    <div>
			<?php if ($this->status == 'pending'): ?>
				<h3>
					<?php echo $this->translate('Payment Pending') ?>
				</h3>
				<p class="form-description">
					<?php echo $this->translate('Thank you for submitting your payment. Your payment is currently pending - your page will be activated when we are notified that the payment has completed successfully. Please return to our login page when you receive an email notifying you that the payment has completed.') ?>
				</p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <button type="submit">
              <?php if (!empty($this->id)): ?>
                <?php echo $this->translate('Back to Dashboard page') ?>
              <?php else: ?>
								<?php echo $this->translate('Back to My Pages') ?>
              <?php endif; ?>
            </button>
          </div>
        </div>

        <?php elseif ($this->status == 'active'): ?>

        <h3>
          <?php echo $this->translate('Payment Successful') ?>
        </h3>
        <p class="form-description">
					<?php echo $this->translate('Thank you! Your payment for your Page has been completed successfully.') ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <button type="submit">
              <?php if (!empty($this->id)): ?>
                <?php echo $this->translate('Continue to Dashboard page') ?>
              <?php else: ?>
								<?php echo $this->translate('Continue to My Pages') ?>
							<?php endif; ?>
            </button>
          </div>
        </div>
        <?php else: ?>
        <h3>
          <?php echo $this->translate('Payment Failed') ?>
        </h3>
        <p class="form-description">
          <?php if (empty($this->error)): ?>
            <?php echo $this->translate('Our payment processor has notified us that your payment could not be completed successfully. We suggest that you try again with another credit card or funding source.') ?>
          <?php else: ?>
						<?php echo $this->translate($this->error) ?>
					<?php endif; ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <button type="submit">            
              <?php if (!empty($this->id)): ?>
                <?php echo $this->translate('Back to Dashboard page') ?>
              <?php else: ?>
								<?php echo $this->translate('Back to My Pages') ?>
							<?php endif; ?>
            </button>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</form>