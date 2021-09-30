<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: create.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
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
<script type="application/javascript">
  window.addEvent('load', function(){
    choosemember(1);
  });
  
  function choosemember (value) {
    if(value == 1) {
      $('external_emails-wrapper').style.display = 'none';
      $('member_name-wrapper').style.display = 'block';
      $('csvfile-wrapper').style.display = 'none';
    } else if(value == 2) {
      $('external_emails-wrapper').style.display = 'block';
      $('member_name-wrapper').style.display = 'none';
      $('csvfile-wrapper').style.display = 'none';
    } else if(value == 3) {
      $('external_emails-wrapper').style.display = 'none';
      $('member_name-wrapper').style.display = 'none';
      $('csvfile-wrapper').style.display = 'block';
    }
  }

  en4.core.runonce.add(function() {
      
      var contentAutocomplete = new Autocompleter.Request.JSON('member_name', "<?php echo $this->url(array('module' => 'sesnewsletter', 'controller' => 'message', 'action' => 'getusers'), 'admin_default', true) ?>", {
        'postVar': 'text',
        'minLength': 1,
        'selectMode': 'pick',
        'autocompleteType': 'tag',
        'customChoices': true,
        'filterSubset': true,
        'multiple': false,
        'className': 'sesbasic-autosuggest',
        'injectChoice': function(token) {
          var choice = new Element('li', {
            'class': 'autocompleter-choices', 
            'html': token.photo, 
            'id':token.label
          });
          new Element('div', {
            'html': this.markQueryValue(token.label),
            'class': 'autocompleter-choice'
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }
      });
      contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
        $('user_id').value = selected.retrieve('autocompleteChoice').id;
      });
    });
</script>
<div class='clear'>
  <div class="global_form_popup">
    <?php echo $this->form->render($this); ?>
  </div>
</div>
