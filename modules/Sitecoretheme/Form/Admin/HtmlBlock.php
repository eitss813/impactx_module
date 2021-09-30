<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: HtmlBlock.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_HtmlBlock extends Engine_Form
{

  public function init()
  {

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $verticalLendingBlockValue = $coreSettings->getSetting('sitecoretheme.home.lending.block', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $baseURL = $view->baseUrl();
    if( empty($verticalLendingBlockValue) || is_array($verticalLendingBlockValue) ) {
      $verticalLendingBlockValue = '<div style="display: inline-block;"><div style="float: left; margin: 10px 0; opacity: 1; text-align: center; width: 33.3%;">
  
      <div style="background-color: #EBEBEB; background-position: center 50%; background-repeat: no-repeat; border-radius: 50% 50% 50% 50%; height: 175px; margin: 0 auto; width: 175px; background-image: url(' . $baseURL . '/application/themes/sitecoretheme/images/discover-events.png); display:block;"></div>
        <a href="' . $baseURL . '/events">
          <span style="color: #282828; float: left; font-size: 22px; margin-top: 20px; text-align: center; width: 100%;">Discover Events</span>
          <span style="color: #707070; float: left; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%;">Find out the best parties and events happening around you.</span>
        </a>
    </div>
    <div style="float: left; margin: 10px 0; opacity: 1; text-align: center; width: 33.3%;">
    <div style="background-color: #EBEBEB; background-position: center 50%; background-repeat: no-repeat; border-radius: 50% 50% 50% 50%; height: 175px; margin: 0 auto; width: 175px; background-image: url(' . $baseURL . '/application/themes/sitecoretheme/images/engage-icon.png); display:block;"></div>
        <a href="' . $baseURL . '/groups">
          <span style="color: #282828; float: left; font-size: 22px; margin-top: 20px; text-align: center; width: 100%;">Engage</span>
          <span style="color: #707070; float: left; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%;">Join our interest based groups and share stuff.</span>
        </a>
   </div>
   <div style="float: left; margin: 10px 0; opacity: 1; text-align: center; width: 33.3%;">
     <div style="background-color: #EBEBEB; background-position: center 50%; background-repeat: no-repeat; border-radius: 50% 50% 50% 50%; height: 175px; margin: 0 auto; width: 175px; background-image: url(' . $baseURL . '/application/themes/sitecoretheme/images/meetpeople.png); display:block;"></div>
    <a href="' . $baseURL . '/members">
      <span style="color: #282828; float: left; font-size: 22px; margin-top: 20px; text-align: center; width: 100%;">Meet New People</span>
      <span style="color: #707070; float: left; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%;">Make new friends with common interests, Get your own party buddies.</span>
    </a>
  </div></div>';
    } else {
      $verticalLendingBlockValue = @base64_decode($verticalLendingBlockValue);
    }

    //WORK FOR MULTILANGUAGES START
    $localeMultiOptions = Engine_Api::_()->sitecoretheme()->getLanguageArray();

    $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
    $total_allowed_languages = Count($localeMultiOptions);
    if( !empty($localeMultiOptions) ) {
      foreach( $localeMultiOptions as $key => $label ) {
        $lang_name = $label;
        if( isset($localeMultiOptions[$label]) ) {
          $lang_name = $localeMultiOptions[$label];
        }

        $page_block_field = "sitecoretheme_home_lending_page_block_$key";
        $page_block_title_field = "sitecoretheme_home_lending_page_block_title_$key";

        if( !strstr($key, '_') ) {
          $key = $key . '_default';
        }

        $keyForSettings = str_replace('_', '.', $key);
        $verticalLendingBlockValueMulti = $coreSettings->getSetting('sitecoretheme.home.lending.block.languages.' . $keyForSettings, null);
        if( empty($verticalLendingBlockValueMulti) ) {
          $verticalLendingBlockValueMulti = $verticalLendingBlockValue;
        } else {
          $verticalLendingBlockValueMulti = @base64_decode($verticalLendingBlockValueMulti);
        }

        $verticalLendingBlockTitleValueMulti = $coreSettings->getSetting('sitecoretheme.home.lending.block.title.languages.' . $keyForSettings, 'Get Started');
        if( empty($verticalLendingBlockTitleValueMulti) ) {
          $verticalLendingBlockTitleValueMulti = 'Get Started';
        } else {
          $verticalLendingBlockTitleValueMulti = @base64_decode($verticalLendingBlockTitleValueMulti);
        }

        $page_block_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Vertical HTML Block: Title & Description in %s"), $lang_name);

        if( $total_allowed_languages <= 1 ) {
          $page_block_field = "sitecoretheme_home_lending_page_block";
          $page_block_title_field = "sitecoretheme_home_lending_page_block_title";
          $page_block_label = "HTML Block: Title & Description";
        } elseif( $label == 'en' && $total_allowed_languages > 1 ) {
          $page_block_field = "sitecoretheme_home_lending_page_block";
          $page_block_title_field = "sitecoretheme_home_lending_page_block_title";
        }

        $plugins = "directionality,advlist,autolink,lists,link,image,charmap,print,preview,hr,anchor,"
          . "pagebreak,searchreplace,wordcount,visualblocks,visualchars,code,fullscreen,insertdatetime,"
          . "media,nonbreaking,save,table,contextmenu,directionality,emoticons,paste,textcolor,imagetools,colorpicker,autosave";

        $editorOptions = array(
          'upload_url' => false,
          'menubar' => true,
          'forced_root_block' => false,
          'force_p_newlines' => false,
          'plugins' => $plugins,
          'toolbar1' => "ltr,rtl,undo,redo,removeformat,pastetext,|,code,link,media,image,emoticons,|,bullist,numlist,|,print,preview,fullscreen",
          'toolbar2' => "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,outdent,indent,blockquote",
          'image_advtab' => true,
        );
        $editorOptions['height'] = '500px';

        $this->addElement('TinyMce', $page_block_field, array(
          'label' => $page_block_label,
          'description' => "Configure the HTML title and description from here. It is displayed after placing the 'Vertical HTML Block' widget from layout editor on any widgetized page of your website.",
          'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px; height:240px;'),
          'value' => $verticalLendingBlockValueMulti,
          'filters' => array(
            new Engine_Filter_Html(),
            new Engine_Filter_Censor()),
          'editorOptions' => $editorOptions,
        ));
      }
    }
    //WORK FOR MULTILANGUAGES END

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }

}