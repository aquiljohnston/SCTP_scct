//function called when other is selected in week dropdown to reset widget to default
function resetDatePicker(){
	//get date picker object
	var datePicker = $('#dynamicmodel-daterangepicker-container').data('daterangepicker');
	//create default start end
	var fm = moment().startOf('day') || '';
	var to = moment() || '';
	//set default selections in widget
	datePicker.setStartDate(fm);
	datePicker.setEndDate(to);
	//set default date range
	daterange = fm.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD');
	$('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html(daterange);
}

//function to set to and from values for date picker based on current date range
function refreshDatePicker(){
	//get current date range
	dateRange = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html();
	//probably a cleaner way to determine if is the initial page load vs refresh
	//check if initial page load and skip refresh if this is the case
	if(dateRange.indexOf('text-muted') > -1) return;
	//parse date range
	dateRangeArray = dateRange.split(' ');
	var fm = moment(dateRangeArray[0]);
	var to = moment(dateRangeArray[2]);
	//get date picker object
	var datePicker = $('#dynamicmodel-daterangepicker-container').data('daterangepicker');
	//set date range values
	datePicker.setStartDate(fm);
	datePicker.setEndDate(to);
}