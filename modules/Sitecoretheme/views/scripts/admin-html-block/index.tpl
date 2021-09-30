<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2>
    <?php echo $this->translate(SITECORETHEME_PLUGIN_NAME) ?>
</h2>

<?php if (count($this->navigation)): ?>

    <div class='seaocore_admin_tabs tabs clr'>

        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<div class="clear seaocore_settings_form">
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>