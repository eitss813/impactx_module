<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Admin_HtmlBlock extends Engine_Form {

    public function init() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $sitealbumLendingBlockValue = $coreSettings->getSetting('sitealbum.lending.block', null);
        if (empty($sitealbumLendingBlockValue) || is_array($sitealbumLendingBlockValue)) {
            $sitealbumLendingBlockValue = '<h2 style="text-align: center; margin-bottom: 10px; font-size: 26px; background-color: transparent; border: 0px none; padding: 10px 0px;"><strong>Wherever you go your photos will follow you.</strong></h2>
<p style="font-size: 18px; width: 80%; margin: 0px auto; text-align: center; padding: 10px 0px; line-height: 24px;">Find beautiful photos shared by a community of&nbsp; professional photographers. Share and upload personal photographs and connect with other enthusiasts.</p>';
        } else {
            $sitealbumLendingBlockValue = @base64_decode($sitealbumLendingBlockValue);
        }

        //WORK FOR MULTILANGUAGES START
        $localeMultiOptions = Engine_Api::_()->sitealbum()->getLanguageArray();

        $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
        $total_allowed_languages = Count($localeMultiOptions);
        if (!empty($localeMultiOptions)) {
            foreach ($localeMultiOptions as $key => $label) {
                $lang_name = $label;
                if (isset($localeMultiOptions[$label])) {
                    $lang_name = $localeMultiOptions[$label];
                }

                $page_block_field = "sitealbum_lending_page_block_$key";
                $page_block_title_field = "sitealbum_lending_page_block_title_$key";

                if (!strstr($key, '_')) {
                    $key = $key . '_default';
                }

                $keyForSettings = str_replace('_', '.', $key);
                $sitealbumLendingBlockValueMulti = $coreSettings->getSetting('sitealbum.lending.block.languages.' . $keyForSettings, null);
                if (empty($sitealbumLendingBlockValueMulti)) {
                    $sitealbumLendingBlockValueMulti = $sitealbumLendingBlockValue;
                } else {
                    $sitealbumLendingBlockValueMulti = @base64_decode($sitealbumLendingBlockValueMulti);
                }

                $sitealbumLendingBlockTitleValueMulti = $coreSettings->getSetting('sitealbum.lending.block.title.languages.' . $keyForSettings, 'Get Started');
                if (empty($sitealbumLendingBlockTitleValueMulti)) {
                    $sitealbumLendingBlockTitleValueMulti = 'Get Started';
                } else {
                    $sitealbumLendingBlockTitleValueMulti = @base64_decode($sitealbumLendingBlockTitleValueMulti);
                }

                $page_block_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Albums and Photos HTML Block Title & Description in %s"), $lang_name);

                if ($total_allowed_languages <= 1) {
                    $page_block_field = "sitealbum_lending_page_block";
                    $page_block_title_field = "sitealbum_lending_page_block_title";
                    $page_block_label = "Albums and Photos HTML Block Title & Description";
                } elseif ($label == 'en' && $total_allowed_languages > 1) {
                    $page_block_field = "sitealbum_lending_page_block";
                    $page_block_title_field = "sitealbum_lending_page_block_title";
                }

                $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions();
                $editorOptions['height'] = '500px';

                $this->addElement('TinyMce', $page_block_field, array(
                    'label' => $page_block_label,
                    'description' => "Configure the HTML title and description that gets shown after placing the 'Albums and Photos HTML Block' widget from layout editor on any  widgetized page of website.",
                    'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px; height:240px;'),
                    'value' => $sitealbumLendingBlockValueMulti,
                    'filters' => array(
                        new Engine_Filter_Html(),
                        new Engine_Filter_Censor()),
                    'editorOptions' => $editorOptions,
                ));
            }
        }
        //WORK FOR MULTILANGUAGES END

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }

}
