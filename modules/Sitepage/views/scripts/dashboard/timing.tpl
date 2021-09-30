<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
  <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
  <div class="layout_middle">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
    <div class="sitepage_edit_content">
      <div class="sitepage_edit_header">
        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
        <?php if($this->sitepage->draft == 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $this->sitepage->page_id), $this->translate('Mark As Live'), array('class'=>'smoothbox')) ?>
        <h3><?php echo $this->translate('Dashboard: ').$this->sitepage->title; ?></h3>
      </div>
      <div id="show_tab_content">
        <div class="sitepage_manage_announcements">
        <div class="tip" id="timing_tip" style="display: none">
          <?php echo $this->translate('Please make sure you select the checkbox corresponsing to the day for which you want to save the operating hours.');?>
        </div>
          <?php
          echo $this->form->render($this); 
          ?>
        </div>
        <div id="show_tab_content_child">
      </div>
      </div>
    </div>
  </div>
</div>
</div>
<script type= "text/javascript">
  en4.core.runonce.add(function(){
    showTiming(document.querySelector('input[name="days"]:checked').value);
  });
function showTiming(option) {
  if(option == '1') {
    var ele = document.getElementsByClassName('time');
    for (var i = 0; i < ele.length; i++ ) {
      ele[i].style.display = "none";
    }
    $('timing_tip').style.display = "none";
  }
  else {
    var ele = document.getElementsByClassName('time');
    for (var i = 0; i < ele.length; i++ ) {
      ele[i].style.display = "block";
    }
    $('timing_tip').style.display = "block";
  }
}

function filterTime(value, fieldData) {
  fieldData.value = value;
  fieldData.setAttribute('min', value);
}

</script>