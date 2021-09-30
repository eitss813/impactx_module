<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: create-contact.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<?php
$base_url = $this->layout()->staticBaseUrl;
$this->headScript()
->appendFile($base_url . 'externals/autocompleter/Observer.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var searchAutocomplete = new Autocompleter.Request.JSON('contactName', "<?php echo $this->url(array('module' => 'sesmultipleform', 'controller' => 'contacts', 'action' => 'search'), 'admin_default', true) ?>", {
      'postVar': 'text',
      'delay' : 250,      
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'className': 'sesbasic-autosuggest',
			'indicatorClass':'input_loading',
      'injectChoice': function(token) {
	  var choice = new Element('li', {
	    'class': 'autocompleter-choices',
	    'html': token.photo,
	    'id': token.label
	  });

	  new Element('div', {
	    'html': this.markQueryValue(token.label),
	    'class': 'autocompleter-choice'
	  }).inject(choice);
	  choice.inputValue = token;
	  this.addChoiceEvents(choice).inject(this.choices);
	  choice.store('autocompleteChoice', token);
         var choice = new Element('li', {
	    'class': 'autocompleter-choices',
	    'html': '',
	    'id': 'all'
	  });
      }
    });
    searchAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      	document.getElementById('toValues').value= selected.retrieve('autocompleteChoice').id;
    });
  });
</script>
<div style="width:400px;">
<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
</div>
<script>
document.getElementById('toValues-wrapper').style.display = 'none';
</script>
<style type="text/css">
.form-label label{
	margin-bottom:5px;
	font-weight:bold;
	display:block;
}
.form-element input[type="text"]{
	width:100%;
}
</style>