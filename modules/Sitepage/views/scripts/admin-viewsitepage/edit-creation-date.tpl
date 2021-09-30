<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-creation-date.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="settings global_form_popup" style="width:600px;">
  <?php echo $this->form->render($this) ?>
</div>

<?php
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>

<script type="text/javascript">
		if($("endtime-wrapper"))
			$("endtime-wrapper").style.display = "none";
</script>


<script type="text/javascript">
  var cal_starttime_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
  }
  var cal_endtime_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
  en4.core.runonce.add(function(){
    cal_starttime_onHideStart();
    cal_endtime_onHideStart();
  });
</script>