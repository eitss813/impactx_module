<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featured.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
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
      <?php echo $this->translate('Your member has been featured.'); ?> 
    </div>
  <?php else: ?>
    <form method="POST" action="<?php echo $this->url() ?>">
      <div>
        <?php if ($this->featured == 0): ?>
          <h3><?php echo $this->translate('Make Member Featured ?'); ?></h3>
          <p>
            <?php echo $this->translate('Are you sure that you want to make this Member featured?'); ?>
          </p>
          <p>&nbsp;
          </p>
          <p>
            <input type="hidden" name="member_id" value="<?php echo $this->member_id ?>"/>
            <button type='submit'><?php echo $this->translate('Make Featured'); ?></button>
            <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
          </p>
        <?php elseif ($this->featured == 1): ?>
          <h3><?php echo $this->translate('Make Member Un-featured ?'); ?></h3>
          <p>
            <?php echo $this->translate('Are you sure that you want to make this Member un-featured ?'); ?>
          </p>
          <p>&nbsp;
          </p>
          <p>
            <input type="hidden" name="member_id" value="<?php echo $this->member_id ?>"/>
            <button type='submit'><?php echo $this->translate('Make Un-featured'); ?></button>
            <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
          </p>
        <?php endif; ?>
      </div>

    </form>
  <?php endif; ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>