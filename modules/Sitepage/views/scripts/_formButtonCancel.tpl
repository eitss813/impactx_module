<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formButtonCancel.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div id="submit-wrapper" class="form-wrapper">
  <div id="submit-label" class="form-label"> </div>
  <div id="submit-element" class="form-element">
    <button type="submit" id="done" name="done">
      <?php echo ( $this->element->getLabel() ? $this->element->getLabel() : $this->translate('Save Changes')) ?>
    </button>
    <?php echo $this->translate('or'); ?>
    <?php echo $this->htmlLink(array('route' => 'sitepage_gernal', 'action' => 'manage'), $this->translate('cancel')) ?>
  </div>
</div>