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
if (!empty($this->isModsSupport)):
    foreach ($this->isModsSupport as $modName) {
        echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with ".SITECORETHEME_PLUGIN_NAME.".", ucfirst($modName)) . "</span></div>";
    }
endif;
?>

<?php
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
$verticalThemeActivated = true;
$themeInfo = Zend_Registry::get('Themes', null);
if (!empty($themeInfo)):
    foreach ($themeInfo as $key => $value):
        if ($key != 'sitecoretheme'):
            $verticalThemeActivated = false;
        endif;
    endforeach;
endif;

if (($coreSettings->getSetting('sitecoretheme.isActivate', 0)) && empty($verticalThemeActivated)): ?>
    <div class="seaocore_tip">
        <span>
            <?php echo "Please"; ?>
            <a target="_blank" href="<?php echo $this->url(array('module' => 'core', 'controller' => 'themes'), 'admin_default', true); ?>">Click here</a> to activate the '<?php echo SITECORETHEME_PLUGIN_NAME ?>'
        </span>
    </div>
<?php endif; ?>

<h2>
    <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>

<div  class='seaocore_admin_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>