<prop:Subject>There is a new order</prop:Subject>
<prop:EmailAddresses><com:TEmailAddress Field="Receiver" Address="sales@theorganicgrocer.com.sg" Name="The Organic Grocer" /></prop:EmailAddresses>
<prop:HtmlContent>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Order Receipt</title>
	<link href="%%BASE_URL%%/style.css" rel="stylesheet" type="text/css" />
	<style type="text/css" media="screen">
	<!--
	  body, div, h2, h3  {background:#fff;}
	-->
	</style>
</head>

<body style="background:none;">
<!-- Main column -->
<div class="main_col">
	%%DYNAMIC_CONTENT%%
	<table cellpadding="8" cellspacing="0" border="0" width="100%">
	<tr>
		<td>Standing order information (if any)</td>
	</tr>
	<tr>
		<td valign="top">
			Frequency: %%SO_FREQUENCY%%<br />
			Duration: %%SO_DURATION%%<br />
			Start Date: %%SO_STARTDATE%%<br />
			Payment: %%SO_PAYMENT%% <br /><br />
		</td>
	</tr>
	</table>
</div>
<!-- Main column: END -->
</body>
</html>
</prop:HtmlContent>