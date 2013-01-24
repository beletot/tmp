<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>get windows user</title>
</head>

<body>
<?php echo getenv("username").'<br />'; ?>
<?php echo "USERNAME ".shell_exec("echo %USERNAME%").'<br />'; ?>
<?php echo "USERPROFILE ".shell_exec("echo %USERPROFILE%").'<br />'; ?>
<?php echo "COMPUTERNAME ".shell_exec("echo %COMPUTERNAME%").'<br />'; ?>
<?php echo "USERDNSDOMAIN ".shell_exec("echo %USERDNSDOMAIN%").'<br />'; ?>
<?php echo "USERDOMAIN ".shell_exec("echo %USERDOMAIN%").'<br />'; ?>
<?php echo "HOMEPATH ".shell_exec("echo %HOMEPATH%").'<br />'; ?>
<?php echo "LOGONSERVER ".shell_exec("echo %LOGONSERVER%").'<br />'; ?>
<?php 

?>
</body>
</html>
