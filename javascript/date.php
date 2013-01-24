<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>New Web Project</title>
		<script src="mootools/js/mootools-core-1.3-full.js" type="text/javascript"></script>
		<script src="date.js" type="text/javascript"></script>
	</head>
	<body>
		<div id="error_date"></div>
		<form class="exportxls" id="" enctype="multipart/form-data" action="date.php" method="post">
			<input type="text" value="01-10-2011" id="startDate" name="startDate" title="samedi 1 octobre 2011">
			<input type="text" value="31-10-2011" id="stopDate" name="stopDate" title="samedi 1 octobre 2011">
			<input type="submit" onclick="return compare();" value="Exporter le fichier xls" style="margin-left:10px;" name="submitexportxls" class="exportxls">
		</form>
		<script type="text/javascript">
			var strDate = '31-10-2011'
			var day = strDate.substring(0,2);
			var month = strDate.substring(3, 5);
			var year = strDate.substring(6, 10);
			
			/*document.write(day+'<br />');
			document.write(month+'<br />');
			document.write(year+'<br />');*/
			
			var d = new Date(year+'-'+month+'-'+day);
			d.setDate(day);
			d.setMonth(month - 1);
			d.setFullYear(year);
		
			
			//var d = new Date('2011-10-31')
			/*document.write(d.getDate())
			document.write("-")
			document.write(d.getMonth() + 1)
			document.write("-")
			document.write(d.getFullYear())*/
		</script>
	</body>
</html>
