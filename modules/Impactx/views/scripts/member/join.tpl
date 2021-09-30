<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: join.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>

<?php echo $this->form->setAttrib('class', 'global_form_popup sitepagemember_member_popup')->render($this) ?>

<script type="text/javascript">
  
  var cal_date_onHideStart = function() {
    // check end date and make it the same date if it's too
    cal_date.calendars[0].start = new Date( document.getElementById('date-date').value );
    // redraw calendar
    cal_date.navigate(cal_date.calendars[0], 'm', 1);
    cal_date.navigate(cal_date.calendars[0], 'm', -1);
  }

  window.addEvent('domready', function() {
    if($('date-minute')) {
      $('date-minute').style.display= 'none';
    }
    if($('date-ampm')) {
      $('date-ampm').style.display= 'none';
    }
    if($('date-hour')) {
      $('date-hour').style.display= 'none';
    }
  });
  
	if($('date-minute')) {
		$('date-minute').style.display= 'none';
	}
	
	if($('date-ampm')) {
		$('date-ampm').style.display= 'none';
	}
	
	if($('date-hour')) {
		$('date-hour').style.display= 'none';
	}
</script>
<script type="text/javascript">

  var addDay=0;
  var addMonth=0;

  function showMonth(month) {
    addMonth=month;
    document.getElementById('addmonth-wrapper').style.display = 'none';
    document.getElementById('month-wrapper').style.display = 'block';
    document.getElementById('month').style.display = 'block';
    var sel = document.getElementById("month");
    var year = document.getElementById("year");
    var selectedTextYear = year.options[year.selectedIndex].text;
    var selectedValueYear = year.options[year.selectedIndex].value;
    var currentYear = '<?php echo (int) date("Y"); ?>'
    //get the selected option
    var selectedTextMonth = sel.options[sel.selectedIndex].text;
    var selectedValueMonth = sel.options[sel.selectedIndex].value;
    if(selectedTextMonth != 'Month') {
      if(parseInt(selectedValueMonth) > '<?php echo (int) date("m"); ?>' && (currentYear == parseInt(selectedTextYear))) {
        sel.selectedIndex="Month";
        document.getElementById('addday-wrapper').style.display = 'none';
        document.getElementById('addday').style.display = 'none';
        document.getElementById('day-wrapper').style.display = 'none';
        document.getElementById('day').style.display = 'none';
      } 
      else {
        document.getElementById('addday-wrapper').style.display = 'block';
        document.getElementById('addday').style.display = 'block';
        document.getElementById('day-wrapper').style.display = 'none';
        document.getElementById('day').style.display = 'none';
      }
    } else {
      document.getElementById('addday-wrapper').style.display = 'none';
      document.getElementById('addday').style.display = 'none';
      document.getElementById('day-wrapper').style.display = 'none';
      document.getElementById('day').style.display = 'none';
    }
  }

  function setTimeMonth() {
  addMonth = 1;
    setTimeout("showAddmonth(1)", 100);
  }

  function showAddmonth(month) {
    if(addMonth == 0 || month == 2) {
      addMonth = 0;
      var sel = document.getElementById("month"); 
      //get the selected option
      var selectedText = sel.options[sel.selectedIndex].text;
      if(selectedText == 'Month') {
        var sel = document.getElementById("day");
        //get the selected option
        sel.options[sel.selectedIndex].text = "Day";
        sel.options[sel.selectedIndex].value = '0';
        
        document.getElementById('addmonth-wrapper').style.display = 'block';
        document.getElementById('month-wrapper').style.display = 'none';
        document.getElementById('month').style.display = 'none';
        document.getElementById('addday-wrapper').style.display = 'none';
        document.getElementById('addday').style.display = 'none';
        document.getElementById('day-wrapper').style.display = 'none';
        document.getElementById('day').style.display = 'none';
      }
    }
  }

  function showDay(day) {
    addDay=day;
    document.getElementById('addday-wrapper').style.display = 'none';
    document.getElementById('day-wrapper').style.display = 'block';
    document.getElementById('day').style.display = 'block';

  }

  function setTime() {
      addDay = 1;
    setTimeout("showAddday(1)", 100);
  }

  function showAddday(day) {
    if(addDay == 0 || day == 2) { 
      addDay = 0;
      var sel = document.getElementById("day");
      //get the selected option
      var selectedText = sel.options[sel.selectedIndex].text;
      var selectedValue = sel.options[sel.selectedIndex].value;
      var selYear = document.getElementById("year");
      var currentYear = '<?php echo (int) date("Y"); ?>'
      var selectedTextYear = selYear.options[selYear.selectedIndex].text;
      var selectedYearValue = selYear.options[selYear.selectedIndex].value; 
      var selMonth = document.getElementById("month");
      var currentMonth = selMonth.options[selMonth.selectedIndex].text;
      var selectedMonthValue = selMonth.options[selMonth.selectedIndex].value; 

      //if(parseInt(selectedValueMonth) > '<?php //echo (int) date("m"); ?>' && (currentYear == parseInt(selectedTextYear))) {
      if(selectedText == 'Day') {
        document.getElementById('addday-wrapper').style.display = 'block';
        document.getElementById('addday').style.display = 'block';
        document.getElementById('day-wrapper').style.display = 'none';
        document.getElementById('day').style.display = 'none';
      } 
      else {
        if(parseInt(selectedValue) > '<?php echo (int) date("d"); ?>' && (currentYear == parseInt(selectedTextYear)) && parseInt(selectedMonthValue) == '<?php echo (int) date("m"); ?>') {
          sel.selectedIndex="Day";
        } 
        else {
          document.getElementById('addday-wrapper').style.display = 'none';
          document.getElementById('addday').style.display = 'none';
          document.getElementById('day-wrapper').style.display = 'block';
          document.getElementById('day').style.display = 'block';
        }
      }
    }
  }
</script>