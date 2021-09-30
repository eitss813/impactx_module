var initializeCalendar = function (isLifetime, currentDate) {

    var cal_bound_start = new Date(currentDate);
    var currenct_date = new Date(currentDate);
    days = 89;
    isLifetime = parseInt(isLifetime);
    if (isLifetime) {
        days = 1824;//5 Years -1 day
    }
    var cal_bound_end = new Date(currenct_date.setDate(currenct_date.getDate() + days));

    // check start date and make it the same date if it's too	
    cal_starttime.calendars[0].start = cal_bound_start;
    cal_endtime.calendars[0].start = cal_bound_start;
    cal_endtime.calendars[0].end = cal_bound_end;

    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);

    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);

    // $('calendar_output_span_starttime-date').innerHTML = currentDate;
    // $('calendar_output_span_endtime-date').innerHTML = currentDate;

    cal_starttime.changed(cal_starttime.calendars[0]);
    cal_endtime.changed(cal_endtime.calendars[0]);

    cal_starttime.calendars[0].val = cal_starttime.calendars[0].start;
    cal_starttime.calendars[0].month = cal_starttime.calendars[0].start.getMonth();
    cal_starttime.calendars[0].year = cal_starttime.calendars[0].start.getFullYear();
}
var cal_starttime_onHideStart = function () {
    var cal_bound_start = $('starttime-date').value;
    var days = 89;
    if ($('lifetime-1') && $('lifetime-1').checked)
        days = 1824;
    if(cal_bound_start){
        $('calendar_output_span_starttime-date').innerHTML = seao_getstarttime($('starttime-date').value);
        var currenct_date = new Date(cal_bound_start);
        cal_endtime.calendars[0].start = new Date(cal_bound_start);
        var cal_bound_end = new Date(currenct_date.setDate(currenct_date.getDate() + days));

        cal_endtime.calendars[0].end = cal_bound_end;
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
        cal_endtime.changed(cal_endtime.calendars[0]);
    }
}


var cal_endtime_onHideStart = function () {
    if($('endtime-date').value) {
        $('calendar_output_span_endtime-date').innerHTML = seao_getstarttime($('endtime-date').value);
    }
}


 