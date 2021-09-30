<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-album-of-day.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.Request.js');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl .'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl .'externals/calendar/styles.css');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'album', 'action' => 'get-album'), 'admin_default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'seaocore-autosuggest',
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
      document.getElementById('resource_id').value = selected.retrieve('autocompleteChoice').id;
    });

  });
</script>
<script type="text/javascript">

  en4.core.runonce.add(function()
  {
    en4.core.runonce.add(function init()
    {
      monthList = [];
      myCal = new Calendar({ 'start_cal[date]': 'M d Y', 'end_cal[date]' : 'M d Y' }, {
        classes: ['event_calendar'],
        pad: 0,
        direction: 0
      });
    });
  });


  en4.core.runonce.add(function(){

    // check end date and make it the same date if it's too
    cal_start_date.calendars[0].start = new Date( document.getElementById('start_date-date').value );
    // redraw calendar
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);

    cal_start_date_onHideStart();
    // cal_end_date_onHideStart();
  });

  var cal_start_date_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_end_date.calendars[0].start = new Date( document.getElementById('start_date-date').value );
    // redraw calendar
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
  }
  var cal_end_date_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_start_date.calendars[0].end = new Date( document.getElementById('end_date-date').value );
    // redraw calendar
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);
  }

  window.addEvent('domready', function() {
    if(document.getElementById('start_date-minute')) {
      document.getElementById('start_date-minute').style.display= 'none';
    }
    if(document.getElementById('start_date-ampm')) {
      document.getElementById('start_date-ampm').style.display= 'none';
    }
    if(document.getElementById('start_date-hour')) {
      document.getElementById('start_date-hour').style.display= 'none';
    }

    //End date work
    if(document.getElementById('end_date-minute')) {
      document.getElementById('end_date-minute').style.display= 'none';
    }
    if(document.getElementById('end_date-ampm')) {
      document.getElementById('end_date-ampm').style.display= 'none';
    }
    if(document.getElementById('end_date-hour')) {
      document.getElementById('end_date-hour').style.display= 'none';
    }
    ///// End End date work

  });
</script>
<div class="settings global_form_popup">
  <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
  <?php //echo $this->form->render($this) ?>
</div>