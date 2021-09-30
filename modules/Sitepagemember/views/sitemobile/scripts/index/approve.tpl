<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: approve.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <script type="text/javascript">
      parent.$('sitepagevideo-item-<?php echo $this->member_id ?>').destroy();
      setTimeout(function() {
        parent.Smoothbox.close();
      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate('Your member has been highlighted.'); ?> 
    </div>
  <?php else: ?>
    <form method="POST" action="<?php echo $this->url() ?>">
      <div>
        <?php if ($this->active == 0 && $this->user_approved == 0): ?>
          <h3><?php echo $this->translate('Approve Member?'); ?></h3>
          <p>
            <?php echo $this->translate('Are you sure that you want to make this Member approve?'); ?>
          </p>
          <p>&nbsp;
          </p>
          <p>
            <input type="hidden" name="member_id" value="<?php echo $this->member_id ?>"/>
            <button type='submit'><?php echo $this->translate('Approve Member'); ?></button>
            <?php echo $this->translate(' or ') ?> <a href="#" data-rel="back" data-role="button"><?php echo $this->translate('cancel'); ?>
						</a>
          </p>
        <?php endif; ?>
      </div>
    </form>
  <?php endif; ?>
</div>