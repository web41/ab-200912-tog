<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<com:THead>
	<title><com:TContentPlaceHolder ID="title" /></title>
	<com:TContentPlaceHolder ID="meta" />
	<link href="<%= $this->UrlPrefix %>/scripts/a.m/a.m.css" rel="stylesheet" type="text/css" />
	<link href="<%= $this->UrlPrefix %>/scripts/calendar/jquery.calendar.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/jquery.js"></script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/jquery-ui.js"></script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/a.m/a.m.js"></script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/calendar/jquery.calendar.js"></script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/javascript.js"></script>
	<com:TContentPlaceHolder ID="script" />
</com:THead>
<body>
<center>
	<com:TForm>
		<div id="banner">
			<div id="logo">
				<a href="#"><b>TheOrganicGrocer.com</b> Administration</a>
			</div>
		</div>
		<div id="main_menu">
			<ul id="mainmenu">
				<li><a href="">product</a></li>
				<li><a href="">category</a>
					<ul>
						<li><a href="">sub cateogory 111111111111111111111111</a></li>
						<li><a href="">sub cateogory 2 2 2</a></li>
						<li><a href="">sub cateogory 3</a></li>
						<li><a href="">sub cateogory 4 4</a></li>
					</ul>
				</li>
				<li><a href="">user</a></li>
				<li><a href="">order</a></li>
				<li><a href="">something for admin</a></li>
			</ul>
		</div>
		<div id="container">
			<com:TContentPlaceHolder ID="content" />
		</div>
	</com:TForm>
</center>
</body>
</html>