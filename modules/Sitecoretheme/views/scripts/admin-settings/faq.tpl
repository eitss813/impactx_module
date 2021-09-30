<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php
$verticalThemeActivated = true;
$themeInfo = Zend_Registry::get('Themes', null);
if (!empty($themeInfo)):
    foreach ($themeInfo as $key => $value):
        if ($key != 'sitecoretheme'):
            $verticalThemeActivated = false;
        endif;
    endforeach;
endif;

if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.isActivate', 0)) && empty($verticalThemeActivated)):
    ?>
    <div class="seaocore_tip">
        <span>
            <?php echo "Please activate the '".SITECORETHEME_PLUGIN_NAME."' from 'Appearance' >> 'Theme Editor' available in the admin panel of your site." ?>
        </span>
    </div>
<?php endif; ?>

<h2><?php echo SITECORETHEME_PLUGIN_NAME ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs tabs clr'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<?php
include_once APPLICATION_PATH .
        '/application/modules/Sitecoretheme/views/scripts/admin-settings/faq_help.tpl';
?>