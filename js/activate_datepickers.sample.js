$(function() {
	update_pickers();
});

function update_pickers()
{
	$('.date-picker').datepicker({dateFormat: 'yy-mm-dd', maxDate: "+0"});
}

