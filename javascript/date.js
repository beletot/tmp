function compare() {
	$('error_date').set('text', '');
	var startDate = $('startDate').get('value');
	var stopDate = $('stopDate').get('value');

	var from = strtotime(startDate);
	var to = strtotime(stopDate);
	
	diff = to.getTime() - from.getTime();
	if(diff <= 0) {
		$('error_date').set('text', 'Veuillez vérifier vos dates.');
		return false;
	}
	return true;
}

function strtotime(strDate) {
	//var strDate = '31-10-2011'
	var day = strDate.substring(0, 2);
	var month = strDate.substring(3, 5);
	var year = strDate.substring(6, 10);

	/*document.write(day + '<br />');
	document.write(month + '<br />');
	document.write(year + '<br />');*/

	var d = new Date(year + '-' + month + '-' + day);
	d.setDate(day);
	d.setMonth(month - 1);
	d.setFullYear(year);
	return d;
}