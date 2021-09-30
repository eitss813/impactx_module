<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _calender.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script>
  var seao_dateFormat = '<?php echo Engine_Api::_()->seaocore()->getLocaleDateFormat(); ?>';
   en4.core.runonce.add(function()
  {
  initializeCalendar();
  cal_starttime_onHideStart();
  });
  var initializeCalendar = function() { 
    var cal_starttime_date;
    if( seao_dateFormat == 'dmy' )
      cal_starttime_date = en4.seaocore.covertdateDmyToMdy($('starttime-date').value);
    else
      cal_starttime_date = $('starttime-date').value;
      // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( cal_starttime_date );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);

    // check start date and make it the same date if it's too	
    cal_starttime.calendars[0].start = new Date( cal_starttime_date );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
  var cal_starttime_onHideStart = function() {
    
    var cal_starttime_date;
    var cal_endtime_date;
    // check end date and make it the same date if it's too
    if( seao_dateFormat == 'dmy' )
      cal_starttime_date = en4.seaocore.covertdateDmyToMdy($('starttime-date').value);
    else
      cal_starttime_date = $('starttime-date').value;
    
    if( seao_dateFormat == 'dmy' )
      cal_endtime_date = en4.seaocore.covertdateDmyToMdy($('endtime-date').value);
    else
      cal_endtime_date = $('endtime-date').value;
    
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( cal_starttime_date);
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    
    //CHECK IF THE END TIME IS LESS THEN THE START TIME THEN CHANGE IT TO THE START TIME.
     var startdatetime = new Date(cal_starttime_date);
     var enddatetime = new Date(cal_endtime_date);
     if(startdatetime.getTime() > enddatetime.getTime()) {
       $('endtime-date').value = $('starttime-date').value;
       $('calendar_output_span_endtime-date').innerHTML = $('endtime-date').value;
       cal_endtime.changed(cal_endtime.calendars[0]);
     }
  };
  </script>
