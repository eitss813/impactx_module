<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
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

<h2>
    <?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>

    <div class='seaocore_admin_tabs clr'>

        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class="clear seaocore_settings_form">
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script>
    var autocompleterObj = null;
    function getDestination(value) {
        if(value=='video'){
            $('model').value = '';
            $('toValues').value = '';
            $('model-wrapper').hide();
            return;
        }else{
            $('model-wrapper').show();
        }
        if (autocompleterObj) {
            autocompleterObj.destroy();
            $('model').value = '';
            $('toValues').value = '';
        }
        createAutocompleter(value);
    }
    window.addEvent('domready', function () {
        getDestination($('sitevideo_videoswipper_destination').value);
    });

    function createAutocompleter(value) {
        if (value == 'channel') {
            url = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'admin-settings', 'action' => 'get-channels'), 'default', true) ?>';
        } else if (value == 'event') {
            url = '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'admin-settings', 'action' => 'get-events'), 'default', true) ?>';
        } else {
            return false;
        }

        autocompleterObj = new Autocompleter.Request.JSON('model', url, {
            'postVar': 'text',
            'postData': false,
            'minLength': 1,
            'delay': 250,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'multiple': false,
            'className': 'seaocore-autosuggest tag-autosuggest',
            'filterSubset': true,
            'tokenFormat': 'object',
            'tokenValueKey': 'label',
            'injectChoice': function (token) {

                var choice = new Element('li', {
                    'class': 'autocompleter-choices',
                    'html': token.photo,
                    'id': token.id
                });
                new Element('div', {
                    'html': this.markQueryValue(token.label),
                    'class': 'autocompleter-choice'
                }).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);

            },
            onCommand: function (e) {
            },
            onPush: function () {
            }
        });

        autocompleterObj.addEvent('onSelection', function (element, selected, value, input) {
            $('toValues').value = selected.id;
        });
    }

</script>
