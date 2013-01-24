<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>New Web Project</title>
		<script src="mootools/js/mootools-core-1.3-full.js" type="text/javascript"></script>
		<script src="date.js" type="text/javascript"></script>
		<script type="text/JavaScript">
			window.addEvent('domready', function() {

				var result = $('result');

				//We can use one Request object many times.
				var req = new Request({

					url : 'listBox-db.php',

					onRequest : function() {
						result.set('text', 'Loading...');
					},

					onSuccess : function(txt) {
						result.set('html', txt);
					},

					// Our request will most likely succeed, but just in case, we'll add an
					// onFailure method which will let the user know what happened.
					onFailure : function() {
						result.set('text', 'The request failed.');
					}
				});

				$('departmentId').addEvent('change', function(event) {
					//event.stop();
					req.send('dept_id=' + $('departmentId').value);
				});
			});
		</script>
	</head>
	<body>
		<form class="exportxls" id="" enctype="multipart/form-data" action="getform.php" method="post">
			<select id="departmentId" name="departmentId">
				<option selected="" value="">Select One</option>
				<option value="2">Billing</option>
				<option value="3">Crystal Report</option>
				<option value="1">Support</option>
			</select>
			<div id="result"></div>
			<input type="submit" value="submit"/>
		</form>
	</body>
</html>

