<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: claim.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('action' => 'get-pages'), 'sitepage_claimpages', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'searchbox_autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);

      }
    });

    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      $('page_id').value = selected.retrieve('autocompleteChoice').id;
    });

  });
</script>
<div class="layout_middle">
<div class="generic_layout_container">
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
</div>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adclaimview', 3)  && $page_communityad_integration ): ?>
  <div class="layout_right" id="communityad_claim">
  <div class="generic_layout_container">
    <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adclaimview', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_claim'))?>
  </div>
  </div>
<?php endif; ?>
<div class="layout_middle claim-page-container">
  <?php if ($this->successmessage): ?>
    <ul class="form-notices" >
      <li>
        <?php echo $this->translate('Your request has been sent successfully. The site administrator will act on your request and you will receive an email correspondingly. You can also track your claims over %1$shere%2$s.', '<a href="' . $this->url(array('action' => 'my-pages'), 'sitepage_claimpages') . '">', '</a>'); ?>
      </li>
    </ul>
  <?php else: ?>
    <?php if ($this->showtip) : ?>
      <?php
      echo '<div class="tip"><span>' . sprintf(Zend_Registry::get('Zend_Translate')->_('There are no pages to be claimed yet.')) . '</span></div>';
      ?>
    <?php else: ?>
      <div>
      <?php echo $this->form->render($this); ?>
      </div>
      <?php endif; ?>
  <?php endif; ?>
</div>