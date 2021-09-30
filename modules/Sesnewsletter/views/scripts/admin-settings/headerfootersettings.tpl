<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: headerfootersettings.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
<?php include APPLICATION_PATH .  '/application/modules/Sesnewsletter/views/scripts/dismiss_message.tpl';?>
<?php $settings = Engine_Api::_()->getApi('settings', 'core');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');
?>

<script>
hashSign = '#';
</script>
<div class='clear'>
  <div class='settings sesbasic_admin_form'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<?php $enablelogo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.enablelogo', 0);?>
<?php $fotrenablelogo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.fotrenablelogo', 0);?>
<script>
 
  window.addEvent('domready',function() {
    changeHeaderLogo('<?php echo $enablelogo;?>');
    changeFooterLogo('<?php echo $fotrenablelogo;?>');
  });
  
  function changeHeaderLogo(value) {
    if(value == 1) {
      if($('sesnewsletter_logositetext-wrapper'))
        $('sesnewsletter_logositetext-wrapper').style.display = 'none';
      if($('sesnewsletter_helogo-wrapper'))
        $('sesnewsletter_helogo-wrapper').style.display = 'block';
    } else { 
      if($('sesnewsletter_logositetext-wrapper'))
        $('sesnewsletter_logositetext-wrapper').style.display = 'block';
      if($('sesnewsletter_helogo-wrapper'))
        $('sesnewsletter_helogo-wrapper').style.display = 'none';
    }
  }
  
  function changeFooterLogo(value) {
    if(value == 1) {
      if($('sesnewsletter_fotrlogositetext-wrapper'))
        $('sesnewsletter_fotrlogositetext-wrapper').style.display = 'none';
      if($('sesnewsletter_fotrlogo-wrapper'))
        $('sesnewsletter_fotrlogo-wrapper').style.display = 'block';
    } else { 
      if($('sesnewsletter_fotrlogositetext-wrapper'))
        $('sesnewsletter_fotrlogositetext-wrapper').style.display = 'block';
      if($('sesnewsletter_fotrlogo-wrapper'))
        $('sesnewsletter_fotrlogo-wrapper').style.display = 'none';
    }
  }
</script>
