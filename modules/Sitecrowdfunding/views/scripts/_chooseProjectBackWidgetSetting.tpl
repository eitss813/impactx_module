<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _chooseProjectBackWidgetSetting.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
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
     function doPushSpan(name, toID) {
      console.log('toID',toID)
        var myElement = new Element("span");

        myElement.id = "tospan_" + name + "_" + toID;
        myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"" + toID + "\", \"" + 'toValues' + "\");'>x</a>";

        myElement.addClass("tag");

        document.getElementById('autosuggest_element_wrapper').appendChild(myElement);
        this.fireEvent('push');
        $('project_ids').value = '';
        $('project_ids').disabled = true; 
    }

    window.addEvent('domready', function () {

        var element = window.parent.currentEditingElement;
        var content_id;
        if (element.get('id').indexOf('admin_content_new_') !== 0 && element.get('id').indexOf('admin_content_') === 0) {
            content_id = element.get('id').replace('admin_content_', '');
        }

        var request = new Request.JSON({
            url: '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'settings', 'action' => 'get-project-widget-params'), "admin_default"); ?>',
            method: 'get',
            data: {
                format: 'json',
                content_id: content_id,
            },
            //responseTree, responseElements, responseHTML, responseJavaScript
            onSuccess: function (responseJSON) { 
                for (i = 0; i < responseJSON.toValuesArray.length; i++) {
                    doPushSpan(responseJSON.toValuesArray[i].title, responseJSON.toValuesArray[i].id);
                } 
                $('toValues').value = responseJSON.toValuesString;
                if($('autosuggest_element_wrapper') && responseJSON.widgetPageName == 'sitecrowdfunding_project_view'){
                    $('autosuggest_element_wrapper').hide();
                    if($('toValues-wrapper')) {
                        $('toValues-wrapper').hide();
                    } 
                }
            }
        });
        request.send();


    });

    function removeFromToValue(id) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = $('toValues').value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

        var checkMulti = id.search(/,/); 
        // check if we are removing multiple recipients
        if (checkMulti != -1) {
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++) {
                removeToValue(recipientsArray[i], toValueArray);
            }
        }
        else {
            removeToValue(id, toValueArray);
        } 
        $('project_ids').disabled = false;
    }

    function removeToValue(id, toValueArray) {
        for (var i = 0; i < toValueArray.length; i++) {
            if (toValueArray[i] == id)
                toValueIndex = i;
        }

        toValueArray.splice(toValueIndex, 1);
        $('toValues').value = toValueArray.join();
        $('project_ids').value = '';
    }
</script>
  
<script type="text/javascript">
    var maxRecipients = 1;
     
    en4.core.runonce.add(function () {
        if ($('project_ids')) {
            var contentAutocomplete = new Autocompleter.Request.JSON('project_ids', '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'action' => 'get-project-suggestions'), 'default', true) ?>', {
                'postVar': 'text', 
                'minLength': 1,
                'selectMode': 'pick',
                'element' : 'toValues', 
                'className': 'tag-autosuggest seaocore-autosuggest',
                'customChoices': true,
                'filterSubset': true,
                'multiple': false, 
                'elementValues': 'toValues',
                'injectChoice': function (token) { 
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        'html': token.photo,
                        'id': token.label
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
                    if ($('toValues-wrapper')) {
                        $('toValues-wrapper').style.display = 'block';
                    }
                    if( $('toValues').value.split(',').length >= maxRecipients ) {
                        $('project_ids').disabled = true;
                    } 
                }
            });
            contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
                document.getElementById('toValues').value = selected.retrieve('autocompleteChoice').id; 
                doPushSpan(selected.retrieve('autocompleteChoice').label, selected.retrieve('autocompleteChoice').id) 
            }); 
        }
    }); 
</script>


<div id="autosuggest_element_wrapper">
<div class="form-wrapper">
    <div class="form-label"></div>
    <div id="project_ids-element" class="form-element">
        <?php echo "Start typing the name of the Project."; ?>
        <input type="text" style="width:300px;" class="text" value="" id="project_ids" name="project_ids">
    </div>
</div> 
<div>
  
