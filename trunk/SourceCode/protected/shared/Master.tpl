<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<com:THead>
	<title><com:TContentPlaceHolder ID="title" /></title>
	<com:TContentPlaceHolder ID="meta" />
	<com:TContentPlaceHolder ID="script" />
</com:THead>
<body>
<center>
	<com:TForm>
	<div id="container">
		<!-- Banner -->
		<div id="banner">
			<div id="logo">
				<a href="#"><img src="<%= $this->Page->Theme->BaseUrl %>/images/logo.png" width="364" height="119" alt="logo" /></a>
			</div>
			<div class="banner_right">
				<ul>
					<li><a href="">home</a></li>  |  
					<li><a href="">About Us</a></li>  |   
					<li><a href="">Why Organic</a></li>  |  
					<li><a href="">Delivery</a></li>  |  
					<li><a href="">Help</a></li>  |  
					<li><a href="">Contact Us</a></li>
				</ul>
			</div>
		</div>
		<!-- Banner: END --> 

		<!-- Main content -->
		<div class="main_content">
			<com:TContentPlaceHolder ID="content" />
		</div>
		<!-- Main content: END -->

		<div id="footer">
			Copyright © 2009 <a href="#">The Organic Grocer</a>. All rights reserved. Designed by AsiaBlaze Design.
		</div>
	</div>
	</com:TForm>
</center>
</body>
</html>