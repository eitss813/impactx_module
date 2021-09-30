<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: map-create.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->form): ?>

    <?php echo $this->form->render($this) ?>

<?php else: ?>

    <div class="global_form_popup_message">
        <?php echo $this->translate("Your changes have been saved.") ?>
    </div>

    <script type="text/javascript">
        parent.onFieldCreate(
    <?php echo Zend_Json::encode($this->field) ?>,
    <?php echo Zend_Json::encode($this->htmlArr) ?>
        );
        (function () {
            parent.Smoothbox.close();
        }).delay(1000);
    </script>

<?php endif; ?>