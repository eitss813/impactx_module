<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: joined-more-pages.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>
<?php
$this->headScript()
		->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Observer.js')
		->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.js')
		->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.Request.js');
		
$this->headScript()->appendFile($this->layout()->staticBaseUrl .'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl .'externals/calendar/styles.css');
?>

<script type="text/javascript">

  en4.core.runonce.add(function() {
    var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'sitepagemember', 'controller' => 'member', 'action' => 'get-more-joined-pages', 'user_id' => $this->user_id), 'default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'seaocore-autosuggest tag-autosuggest',
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
      document.getElementById('page_id').value = selected.retrieve('autocompleteChoice').id;
    });

  });
</script>

<div class="global_form_popup sitepagemember_join_more_pages_popup">
	<?php echo $this->form->setAttrib('class', '')->render($this) ?>
</div>