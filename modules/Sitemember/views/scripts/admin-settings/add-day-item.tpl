<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-day-item.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">

  function getUrlParam(name) {
    var regexS;
    var regexl;
    var results;

    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    regexS = "[\\?&]" + name + "=([^&#]*)";
    regex = new RegExp(regexS);
    results = regex.exec(parent.window.location.href);

    if (results == null) {
      return "";
    } else {
      return results[1];
    }
  }

  en4.core.runonce.add(function() {
    $('user_id-wrapper').style.display = 'none';
    var pageId = getUrlParam('page');
    var contentAutocomplete = new Autocompleter.Request.JSON('member_title', '<?php echo $this->url(array('module' => 'sitemember', 'controller' => 'location', 'action' => 'getmember'), 'default', true) ?>/page_id/' + pageId, {
      'postVar': 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'seaocore-autosuggest',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
    });
  });
</script>

<div class="form-wrapper">
  <div class="form-label"></div>
  <div id="member_title-element" class="form-element">
    <?php echo "Start typing the name of the member."; ?>
    <input type="text" style="width:300px;" class="text" value="" id="member_title" name="member_title">
  </div>
</div>