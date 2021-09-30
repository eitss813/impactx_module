<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: cancel.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->form): ?>
    <?php echo $this->form->render($this) ?>
<?php else: ?>
    <div style="padding: 10px;">
        <?php if ($this->status): ?>
            <?php echo $this->translate('The package has been cancelled.') ?>
        <?php else: ?>
            <?php
            echo $this->translate('There was a problem cancelling the package. The message was: %s', $this->error)
            ?>
        <?php endif; ?>

        <br />
        <br /> 

        <a href="javascript:void(0);" onclick="parent.Smoothbox.close();
                    return false">
               <?php echo $this->translate('close') ?>
        </a>

    </div>

<?php endif; ?>