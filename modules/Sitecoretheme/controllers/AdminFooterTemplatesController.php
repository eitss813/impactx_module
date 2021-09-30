<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFooterTemplatesController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminFooterTemplatesController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_footer');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecoretheme_admin_settings_footer', array(), 'sitecoretheme_admin_footer_templates');

        $tempLanguageDataArray = array();
        $tempLanguageTitleDataArray = array();
        if ($this->getRequest()->isPost()) {
            $localeMultiOptions = Engine_Api::_()->sitecoretheme()->getLanguageArray();
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
            $total_allowed_languages = Count($localeMultiOptions);

            if (!empty($localeMultiOptions)) {
                foreach ($localeMultiOptions as $key => $label) {
                    $lang_name = $label;
                    if (isset($localeMultiOptions[$label])) {
                        $lang_name = $localeMultiOptions[$label];
                    }

                    $page_block_field = "sitecoretheme_footer_lending_page_block_$key";
                    $page_block_title_field = "sitecoretheme_footer_lending_page_block_title_$key";
                    if ($total_allowed_languages <= 1) {
                        $page_block_field = "sitecoretheme_footer_lending_page_block";
                        $page_block_title_field = "sitecoretheme_footer_lending_page_block_title";
                        $page_block_label = "Description";
                        $page_block_title_label = "Title";
                    } elseif ($label == 'en' && $total_allowed_languages > 1) {
                        $page_block_field = "sitecoretheme_footer_lending_page_block";
                        $page_block_title_field = "sitecoretheme_footer_lending_page_block_title";
                    }

                    if (!strstr($key, '_')) {
                        $key = $key . '_default';
                    }

                    $tempLanguageDataArray[$key] = @base64_encode($_POST[$page_block_field]);
                    $tempLanguageTitleDataArray[$key] = @base64_encode($_POST[$page_block_title_field]);
                }

                $coreSettings->setSetting('sitecoretheme.footer.lending.block.languages', $tempLanguageDataArray);
                $coreSettings->setSetting('sitecoretheme.footer.lending.block.title.languages', $tempLanguageTitleDataArray);
            }
        }

        $this->view->form = $form = new Sitecoretheme_Form_Admin_Footertemplates();

        if (!$this->getRequest()->isPost())
            return;

        if (!$form->isValid($this->getRequest()->getPost()))
            return;

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $values = $form->getValues();

        if (array_key_exists('youtube', $values))
            unset($values['youtube']);

        if (array_key_exists('twitter', $values))
            unset($values['twitter']);

        if (array_key_exists('pinterest', $values))
            unset($values['pinterest']);

        if (array_key_exists('facebook', $values))
            unset($values['facebook']);

        if (array_key_exists('linkedin', $values))
            unset($values['linkedin']);
        if (array_key_exists('note_description', $values))
            unset($values['note_description']);

          foreach( $values as $key => $value ) {

            if( $coreSettings->hasSetting($key, $value) ) {
              $coreSettings->removeSetting($key);
            }
            if( $value == null ) {
              continue;
            }
            $coreSettings->setSetting($key, $value);
          }

    if ( isset($values['sitecoretheme_footer_lending_page_block']) && !empty($values['sitecoretheme_footer_lending_page_block'])) {
            $value = @base64_encode($values['sitecoretheme_footer_lending_page_block']);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitecoretheme.footer.lending.block', $value);
        }

        $form->addNotice('Your changes have been saved.');
    }

}