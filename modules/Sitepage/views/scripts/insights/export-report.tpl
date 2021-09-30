<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-report.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
<script type="text/javascript">
var showMarkerInDate="<?php echo $this->showMarkerInDate ?>";
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

  var cal_start_cal_onHideStart = function(){
     if(showMarkerInDate == 0) return;
    // check end date and make it the same date if it's too
    cal_end_cal.calendars[0].start = new Date( $('start_cal-date').value );
    // redraw calendar
    cal_end_cal.navigate(cal_end_cal.calendars[0], 'm', 1);
    cal_end_cal.navigate(cal_end_cal.calendars[0], 'm', -1);
  }
  var cal_end_cal_onHideStart = function(){
     if(showMarkerInDate == 0) return;
    // check start date and make it the same date if it's too
    cal_start_cal.calendars[0].end = new Date( $('end_cal-date').value );
    // redraw calendar
    cal_start_cal.navigate(cal_start_cal.calendars[0], 'm', 1);
    cal_start_cal.navigate(cal_start_cal.calendars[0], 'm', -1);
  }

  en4.core.runonce.add(function(){
    cal_start_cal_onHideStart();
    cal_end_cal_onHideStart();
  });

  window.addEvent('domready', function() {
    if($('start_cal-minute') && $('end_cal-minute')) {
      $('start_cal-minute').style.display= 'none';
      $('end_cal-minute').style.display= 'none';
    }
    if($('start_cal-ampm') && $('end_cal-ampm')) {
      $('start_cal-ampm').style.display= 'none';
      $('end_cal-ampm').style.display= 'none';
    }
    if($('start_cal-hour') && $('end_cal-hour')) {
      $('start_cal-hour').style.display= 'none';
      $('end_cal-hour').style.display= 'none';
    }
    
    var empty = '<?php echo $this->empty ?>';
    var prefield = '<?php echo $this->prefield ?>';

    form = $('report_form');
    if (form) {
      form.setAttribute("method","get");
    }
  
    if(prefield == 1) {
      onChangeTime($('time_summary'));
      onchangeFormat($('format_report'));

      // display message tip
      if(empty == 1) {
        $('tip').style.display= 'block';
      }
    }
  });

  // on changing the time column in the report form
  function onChangeTime(formElement) {

    if(formElement.value == 'Monthly') {
      $('start_group').setStyle('display', 'block');
      $('end_group').setStyle('display', 'block');
      $('time_group2').setStyle('display', 'none');
    }
    else if(formElement.value == 'Daily') {
      $('start_group').setStyle('display', 'none');
      $('end_group').setStyle('display', 'none');
      $('time_group2').setStyle('display', 'block');
    }
  }
    
  // on changing the report format column in the report form
  function onchangeFormat(formElement) {
    form = $('report_form');
    if(formElement.value == 1) {
      $('tip').style.display= 'none';
    }
  }
    
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class="layout_middle">
  <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
    <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Reports', 'sectionDescription' => '')); ?>
  <div class="sitepage_edit_content border0" style="margin-bottom:0px;">
    <div id="show_tab_content"> 
      <div class="tip" id ='tip' style='display:none;'>
        <span>
          <?php echo $this->translate("There are no activities found in the selected date range.") ?>
        </span>
      </div> 
      <?php echo $this->reportform->render($this) ?>
    </div>
  </div>
</div>
     </div>
</div>