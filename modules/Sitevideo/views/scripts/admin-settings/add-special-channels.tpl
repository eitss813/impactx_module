<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AvpCategoryLine.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
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

        var myElement = new Element("span");

        myElement.id = "tospan_" + name + "_" + toID;
        myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"" + toID + "\", \"" + 'toValues' + "\");'>x</a>";

        myElement.addClass("tag");

        document.getElementById('toValues-element').appendChild(myElement);
        this.fireEvent('push');
    }

    window.addEvent('domready', function() {

        var element = window.parent.currentEditingElement;
        var content_id;
        if (element.get('id').indexOf('admin_content_new_') !== 0 && element.get('id').indexOf('admin_content_') === 0) {
            content_id = element.get('id').replace('admin_content_', '');
        }

        var request = new Request.JSON({
            url: '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'settings', 'action' => 'get-channel-widget-params'), "admin_default"); ?>',
            method: 'get',
            data: {
                format: 'json',
                content_id: content_id,
            },
            //responseTree, responseElements, responseHTML, responseJavaScript
            onSuccess: function(responseJSON) {

                for (i = 0; i < responseJSON.toValuesArray.length; i++) {
                    doPushSpan(responseJSON.toValuesArray[i].title, responseJSON.toValuesArray[i].id);
                }

                $('toValues').value = responseJSON.toValuesString;


//                $('starttime-date').value = responseJSON.startDate;
//                $('calendar_output_span_starttime-date').innerHTML = responseJSON.startDate;
//                $('starttime-hour').value = responseJSON.startHour;
//                $('starttime-minute').value = responseJSON.startMinute;
//                $('starttime-ampm').value = responseJSON.startAMPM;
//                
//                $('endtime-date').value = responseJSON.endDate;
//                $('calendar_output_span_endtime-date').innerHTML = responseJSON.endDate;
//                $('endtime-hour').value = responseJSON.endHour;
//                $('endtime-minute').value = responseJSON.endMinute;
//                $('endtime-ampm').value = responseJSON.endAMPM;                
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
        if (toValueArray.length == 1) {
            $('toValues-wrapper').style.display = 'none';
        }
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

        // hide the wrapper for usernames if it is empty
        if ($('toValues').value == "") {
            $('toValues-wrapper').setStyle('height', '0');
        }

        $('channel_ids').disabled = false;
    }

    function removeToValue(id, toValueArray) {
        for (var i = 0; i < toValueArray.length; i++) {
            if (toValueArray[i] == id)
                toValueIndex = i;
        }

        toValueArray.splice(toValueIndex, 1);
        $('toValues').value = toValueArray.join();
    }

    en4.core.runonce.add(function() {
        if ($('channel_ids')) {
            new Autocompleter.Request.JSON('channel_ids', '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'admin-settings', 'action' => 'get-channels'), 'default', true) ?>', {
                'postVar': 'text',
                'postData': false,
                'minLength': 1,
                'delay': 250,
                'selectMode': 'pick',
                'element': 'toValues',
                'autocompleteType': 'message',
                'multiple': false,
                'className': 'seaocore-autosuggest tag-autosuggest',
                'filterSubset': true,
                'tokenFormat': 'object',
                'tokenValueKey': 'label',
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
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);

                },
                onCommand: function(e) {
                },
                onPush: function() {
                    if ($('toValues-wrapper')) {
                        $('toValues-wrapper').style.display = 'block';
                    }
//          if( $('toValues').value.split(',').length >= maxRecipients ){
//            $('event_ids').disabled = true;
//          }
                }
            });
            new Composer.OverText($('channel_ids'), {
                'textOverride': '<?php echo $this->translate('Start typing...') ?>',
                'element': 'label',
                'isPlainText': true,
                'positionOptions': {
                    position: (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
                    edge: (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
                    offset: {
                        x: (en4.orientation == 'rtl' ? -4 : 4),
                        y: 2
                    }
                }
            });
        }
    });

</script>

<div class="form-wrapper">
    <div class="form-label"></div>
    <div id="channel_ids-element" class="form-element">
        <?php echo "Start typing the name of the Channel."; ?>
        <input type="text" style="width:300px;" class="text" value="" id="channel_ids" name="channel_ids">
    </div>
</div>