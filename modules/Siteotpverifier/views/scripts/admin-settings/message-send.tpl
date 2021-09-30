<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: message-send.tpl 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl .'application/modules/Core/externals/scripts/composer.js');
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<div class="global_form_popup" >
  <?php echo $this->form->render($this); ?>
</div>
<script type="text/javascript">
  var contentAutocomplete;
  var maxRecipients = 10;

  function removeFromToValue(id, elmentValue,element) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $(elmentValue).value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1) {
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray, elmentValue);
      }
    } else {
      removeToValue(id, toValueArray, elmentValue);
    }

    // hide the wrapper for element if it is empty
    if ($(elmentValue).value==""){
      $(elmentValue+'-wrapper').setStyle('height', '0');
      $(elmentValue+'-wrapper').setStyle('display', 'none');
    }
    $(element).disabled = false;
  }
  
  function removeToValue(id, toValueArray, elmentValue) {
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }
    toValueArray.splice(toValueIndex, 1);
    $(elmentValue).value = toValueArray.join();
  }
  en4.core.runonce.add(function()
  {

   contentAutocomplete = new Autocompleter.Request.JSON('user_name', '<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'settings', 'action' => 'getallusers'), 'admin_default', true) ?>', {
    'postVar' : 'search',
    'postData' : {'user_ids': $('user_id').value,'level_id':$('member_level').value},
    'minLength': 1,
    'delay' : 250,
    'selectMode': 'pick',
    'autocompleteType': 'tag',
    'className': 'siteotpverifier-autosuggest',
    'filterSubset' : true,
    'multiple' : false,
    'injectChoice': function(token){
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
    },
    onPush : function() {
      if ($('user_id-wrapper')) {
        $('user_id-wrapper').style.display='block';
      }
      
      if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
        this.element.disabled = true;
      }
      contentAutocomplete.setOptions({
        'postData' : {'user_ids': $('user_id').value,'level_id':$('member_level').value}
      });
      
    }

  });
   contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
    $('user_id').value = selected.retrieve('autocompleteChoice').id;
  });

 });

</script>

<script type="text/javascript">

  window.addEvent('domready',function () {
   var e6 = $('user_name-wrapper');
   e6.setStyle('display', 'none');
   
   var e7 = $('user_id-wrapper');
   e7.setStyle('display', 'none');
   onMemberChange();
   ontypeChange();
 });
  function onMemberChange()
  {    
   var sel=$('member');
   if(sel.options[sel.selectedIndex].text=='Specific User')
   {
     $('user_name-wrapper').show();
   } else {
     $('user_name-wrapper').hide();
   }
   doAutoResize();
 }
 function ontypeChange()
  {    
   var sel=$('type');
   if(sel.options[sel.selectedIndex].text=='Profile Type')
   {
        if($('profile_type-wrapper'))
            $('profile_type-wrapper').show();
        $('member_level-wrapper').hide();
        $('member-wrapper').hide();
     
   } else {
        if($('profile_type-wrapper'))
            $('profile_type-wrapper').hide();
        $('member_level-wrapper').show();
        $('member-wrapper').show();
   }
   doAutoResize();
 }
 function onLevelChange()
 {
  contentAutocomplete.setOptions({
    'postData' : {'user_ids': $('user_id').value, 'level_id' : $('member_level').value}
  });
}



var boxSize = 0;
    var doAutoResize = function () {
    parent.Smoothbox.instance.doAutoResize();
    var smoothbox = parent.Smoothbox.instance;
    var iframe = smoothbox.content;
    var element = Function.attempt(function () {
      return iframe.contentWindow.document.body.getChildren()[0];
    }, function () {
      return iframe.contentWindow.document.body;
    }, function () {
      return iframe.contentWindow.document.documentElement;
    });

    var size = Function.attempt(function () {
      return element.getScrollSize();
    }, function () {
      return element.getSize();
    }, function () {
      return {
        x: element.scrollWidth,
        y: element.scrollHeight
      }
    });

    var winSize = window.getSize();
    if (size.x - 50 > winSize.x)
      size.x = winSize.x - 50;
    if (size.y - 50 > winSize.y)
      size.y = winSize.y - 50;
    if (boxSize == 0) {
      boxSize = size.x + 20;
    }

    smoothbox.content.setStyles({
      'width': (boxSize - 20) + 'px',
      'height': (size.y + 20) + 'px'
    });

    smoothbox.options.width = (boxSize - 20);
    smoothbox.options.height = (size.y + 20);
    smoothbox.positionWindow();
  }
  </script>