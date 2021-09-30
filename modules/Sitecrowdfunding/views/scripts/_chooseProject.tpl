<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _chooseProject.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
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
<?php $module = explode('_', $this->parent_type)[0];?>
<?php $moduleName = explode('_', $this->parent_type)[0].$this->subject()->getIdentity();?>

<?php 
    if($module == 'sitereview') {
        $moduleName = "sitereview_".$this->subject()->listingtype_id.$this->subject()->getIdentity(); 
    }
?> 
<?php $project_id = Engine_Api::_()->getApi('settings', 'core')->getSetting("$moduleName.choosed.project", 0); ?>
 
<?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);?>  

<script type="text/javascript"> 

     function doPushSpan(name, toID) {

        var myElement = new Element("span");

        myElement.id = "tospan_" + name + "_" + toID;
        myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"" + toID + "\", \"" + '<?php echo $moduleName; ?>_choosed_project' + "\");'>X</a>";

        myElement.addClass("tag");

        document.getElementById('project_ids-element').appendChild(myElement);
        this.fireEvent('push');
        $('project_ids').disabled = true;
        $('project_ids').value = '';
    } 
    
    en4.core.runonce.add(function(){ 
        if($('choose-project')) {
            $('choose-project').removeEvents('submit').addEvent('submit');  
        }
        var title = '<?php echo $project->title; ?>';
        var projectId = '<?php echo $project->project_id; ?>';
 
        if(projectId && title) { 
            doPushSpan(title, projectId); 
        } 
    }); 

    function removeFromToValue(id) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = $('<?php echo $moduleName; ?>_choosed_project').value;
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
        $('<?php echo $moduleName; ?>_choosed_project').value = toValueArray.join();
        $('project_ids').value = '';
    }
</script> 


<script type="text/javascript"> 
    var maxRecipients = 1;  
    en4.core.runonce.add(function () {
        if ($('project_ids')) {
            var contentAutocomplete = new Autocompleter.Request.JSON('project_ids', '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'action' => 'get-project-suggestions', 'owner_id' => $this->owner_id), 'default', true) ?>', {
                'postVar': 'text',
                'minLength': 1,
                'selectMode': 'pick',
                'element' : '<?php echo $moduleName; ?>_choosed_project', 
                'className': 'tag-autosuggest seaocore-autosuggest',
                'customChoices': true,
                'filterSubset': true,
                'multiple': false, 
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
            }); 
            contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
                document.getElementById('<?php echo $moduleName; ?>_choosed_project').value = selected.retrieve('autocompleteChoice').id;
                doPushSpan(selected.retrieve('autocompleteChoice').label, selected.retrieve('autocompleteChoice').id) 
            });
        }
    }); 
</script> 
 
 <script type="text/javascript">
    if($('save')) {
        $('save').removeEvents('click').addEvent('click', function(){ 
            $('choose-project').removeEvents('submit').submit(); 
        });
    } 
 </script>
  
