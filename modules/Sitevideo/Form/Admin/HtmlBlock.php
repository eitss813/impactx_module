<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_HtmlBlock extends Engine_Form {

    public function init() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        
        $sitevideoLendingBlockValue = $coreSettings->getSetting('sitevideo.lending.block', null);
        if (empty($sitevideoLendingBlockValue) || is_array($sitevideoLendingBlockValue)) {
            $sitevideoLendingBlockValue = '<div style="width: 75%;margin: 0 auto;"><p style="text-align: center;line-height: 55px;"><span style="font-size: 30pt;"><strong>Upload, watch and share videos on your site</strong></span></p><p style="text-align: center;"><span style="font-size: 16pt;line-height: 22pt;">Post and share videos with your community members, friends, or with anyone, on computers, phones and tablets. <a href='.$view->baseUrl().'/videos/browse><strong>See all our videos &raquo;</strong></a></span></p></div>';
        } else {
            $sitevideoLendingBlockValue = @base64_decode($sitevideoLendingBlockValue);
        }

        //WORK FOR MULTILANGUAGES START
        $localeMultiOptions = Engine_Api::_()->sitevideo()->getLanguageArray();

        $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
        $total_allowed_languages = Count($localeMultiOptions);
        if (!empty($localeMultiOptions)) {
            foreach ($localeMultiOptions as $key => $label) {
                $lang_name = $label;
                if (isset($localeMultiOptions[$label])) {
                    $lang_name = $localeMultiOptions[$label];
                }

                $page_block_field = "sitevideo_lending_page_block_$key";
                $page_block_title_field = "sitevideo_lending_page_block_title_$key";

                if (!strstr($key, '_')) {
                    $key = $key . '_default';
                }

                $keyForSettings = str_replace('_', '.', $key);
                $sitevideoLendingBlockValueMulti = $coreSettings->getSetting('sitevideo.lending.block.languages.' . $keyForSettings, null);
                if (empty($sitevideoLendingBlockValueMulti)) {
                    $sitevideoLendingBlockValueMulti = $sitevideoLendingBlockValue;
                } else {
                    $sitevideoLendingBlockValueMulti = @base64_decode($sitevideoLendingBlockValueMulti);
                }

                $sitevideoLendingBlockTitleValueMulti = $coreSettings->getSetting('sitevideo.lending.block.title.languages.' . $keyForSettings, 'Get Started');
                if (empty($sitevideoLendingBlockTitleValueMulti)) {
                    $sitevideoLendingBlockTitleValueMulti = 'Get Started';
                } else {
                    $sitevideoLendingBlockTitleValueMulti = @base64_decode($sitevideoLendingBlockTitleValueMulti);
                }

                $page_block_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Channels and Videos HTML Block: Title & Description in %s"), $lang_name);

                if ($total_allowed_languages <= 1) {
                    $page_block_field = "sitevideo_lending_page_block";
                    $page_block_title_field = "sitevideo_lending_page_block_title";
                    $page_block_label = "Channels and Videos HTML Block: Title & Description";
                } elseif ($label == 'en' && $total_allowed_languages > 1) {
                    $page_block_field = "sitevideo_lending_page_block";
                    $page_block_title_field = "sitevideo_lending_page_block_title";
                }

                $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions();
                $editorOptions['height'] = '500px';

                $this->addElement('TinyMce', $page_block_field, array(
                    'label' => $page_block_label,
                    'description' => "Configure the HTML title and description from here. It is displayed after placing the 'Channels and Videos HTML Block' widget from layout editor on any widgetized page of your website.",
                    'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px; height:240px;'),
                    'value' => $sitevideoLendingBlockValueMulti,
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
