<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="tip">
  <span> <?php echo $this->translate('No Pages have been created yet.'); ?>
    <?php if ($this->can_create): ?>
      <?php
      if (Engine_Api::_()->sitepage()->hasPackageEnable()):
        $createUrl = $this->url(array('action' => 'index'), 'sitepage_packages');
      else:
        $createUrl = $this->url(array('action' => 'create'), 'sitepage_general');
      endif;
      ?>
  <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $createUrl . '">', '</a>'); ?>
<?php endif; ?>
  </span>
</div>