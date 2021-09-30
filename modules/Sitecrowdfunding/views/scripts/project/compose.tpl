<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: compose.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
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
    en4.core.runonce.add(function ()
    {
        var contentAutocomplete = new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('controller' => 'dashboard', 'action' => 'get-members', 'project_id' => $this->project_id), 'sitecrowdfunding_extended', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': true,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });

        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
            if ($('user_ids').value.split(',').length >= 10) {
                $('to').disabled = true;
            } else {
                document.getElementById('user_ids').value += ',' + selected.retrieve('autocompleteChoice').id;
            }
        });
    });
</script>
<div class="global_form_popup">
    <?php echo $this->form->render($this) ?>
</div>
<a href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>

