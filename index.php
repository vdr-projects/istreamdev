<?php
include('includes/include.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="javascript/spinningwheel-min.js" type="text/javascript"></script>
<script src="javascript/ajax.js" type="text/javascript"></script>
<script src="javascript/functions.js" type="text/javascript"></script>
<title>iStreamdev</title>
<link href="images/startup.png" rel="apple-touch-startup-image" />
<link rel="apple-touch-icon" href="images/istreamdev.png"/>
</head>


<?php
selectpage();
?>

<div id="footer">
	
	iStreamdev version: <? print $isdversion; ?>
</div>

</body>

</html>


