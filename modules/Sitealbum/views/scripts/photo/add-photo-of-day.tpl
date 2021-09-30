<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-photo-of-day.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
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
    cal_start_date.calendars[0].start = new Date( $('start_date-date').value );
    // redraw calendar
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);

    cal_start_date_onHideStart();
    // cal_end_date_onHideStart();
  });

  var cal_start_date_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_end_date.calendars[0].start = new Date( $('start_date-date').value );
    // redraw calendar
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
  }
  var cal_end_date_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_start_date.calendars[0].end = new Date( $('end_date-date').value );
    // redraw calendar
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);
  }

  window.addEvent('domready', function() {
    if($('start_date-minute')) {
      $('start_date-minute').style.display= 'none';
    }
    if($('start_date-ampm')) {
      $('start_date-ampm').style.display= 'none';
    }
    if($('start_date-hour')) {
      $('start_date-hour').style.display= 'none';
    }

    //End date work
    if($('end_date-minute')) {
      $('end_date-minute').style.display= 'none';
    }
    if($('end_date-ampm')) {
      $('end_date-ampm').style.display= 'none';
    }
    if($('end_date-hour')) {
      $('end_date-hour').style.display= 'none';
    }
    ///// End End date work

  });
</script>
<div class="settings global_form_popup"> 
  <?php echo $this->form->render($this) ?>
</div>