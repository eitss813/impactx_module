<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
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
          <?php if ($this->params == 'leave') : ?>
          <h3><?php echo $this->translate('Leave Page'); ?></h3>
          <?php else : ?><h3><?php echo $this->translate('Remove Member ?'); ?></h3>
          <?php endif ; ?>
          <p><?php if ($this->params == 'leave') : ?>
            <?php echo $this->translate('Are you sure you want to leave this page?'); ?>
            <?php else : ?>
            <?php echo $this->translate('Are you sure you want to remove this member from the page ?'); ?>
            <?php endif; ?>
          </p>
          <p>&nbsp;
          </p>
          <p>
            <input type="hidden" name="member_id" value="<?php echo $this->member_id ?>"/>
            <?php if ($this->params == 'leave') : ?>
            <button type='submit'><?php echo $this->translate('Leave Page'); ?></button>
            <?php else : ?>
            <button type='submit'><?php echo $this->translate('Remove Member'); ?></button>
            <?php endif; ?>
            <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
          </p>
      </div>

    </form>
  <?php endif; ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>