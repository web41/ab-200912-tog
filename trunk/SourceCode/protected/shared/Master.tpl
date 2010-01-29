<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<com:THead>
	<title><com:TContentPlaceHolder ID="title" /></title>
	<noscript><meta http-equiv="refresh" content="0;URL=noscript.html" /></noscript>
	<com:TContentPlaceHolder ID="meta" />
	<com:TContentPlaceHolder ID="script" />
    <link href="<%= $this->UrlPrefix %>/scripts/a.m/a.m.css" rel="stylesheet" type="text/css" />
	<link href="<%= $this->UrlPrefix %>/scripts/calendar/jquery.calendar.css" rel="stylesheet" type="text/css" />
    <link href="<%= $this->UrlPrefix %>/scripts/d.p/css/redmond/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/jquery.js"></script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/jquery-ui.js"></script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/a.m/a.m.js"></script>
    
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/calendar/jquery.calendar.js"></script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/homepage.js"></script>
	<script type="text/javascript">
		function popup(name,url,focus)
		{
			var _popup = window.open(url,name);
			if (focus) _popup.focus();
		}
	</script>
</com:THead>
<body>
<center>
	<com:TForm>
	<div id="container">
		<!-- Banner -->
		<div id="banner">
			<div id="logo">
				<a href="<%= $this->Service->ConstructUrl("shop.Welcome") %>"><img src="<%= $this->Page->Theme->BaseUrl %>/images/logo.png" width="364" height="119" alt="logo" /></a>
			</div>
			<div class="banner_right">
				<ul>
					<li><a href="<%= $this->Service->ConstructUrl("shop.Welcome") %>">Home</a></li>  |  
					<li><a href="<%= $this->Service->ConstructUrl("shop.AboutUs") %>">About Us</a></li>  |   
					<li><a href="<%= $this->Service->ConstructUrl("shop.WhyOrganic") %>">Why Organic</a></li>  |  
					<li><a href="<%= $this->Service->ConstructUrl("shop.Delivery") %>">Delivery</a></li>  |  
					<li><a href="<%= $this->Service->ConstructUrl("shop.Help") %>">Help</a></li>  |  
					<li><a href="<%= $this->Service->ConstructUrl("shop.ContactUs") %>">Contact Us</a></li>
				</ul>
				<div class="search_container">
					<com:TButton ID="btnSearch" CssClass="button" Text="Search" OnClick="btnSearch_Clicked" IsDefaultButton="false" />
					<com:TDropDownList ID="cboBrandSelector" PromptText="Search All Brand" PromptValue="0" DataTextField="Name" DataValueField="ID" />
					<com:TTextBox ID="txtSearchText" CssClass="textbox" Text="e.g. apple juice" Attributes.onfocus="this.value=''" />
				</div>
			</div>
		</div>
		<!-- Banner: END --> 

		<!-- Main content -->
		<div class="main_content">
			<com:TContentPlaceHolder ID="content" />
		</div>
		<!-- Main content: END -->

		<div id="footer">
			Copyright &copy; 2010 <a href="<%= $this->Service->ConstructUrl("shop.Index") %>">The Organic Grocer</a>. All rights reserved.
            <a href="<%= $this->Service->ConstructUrl("shop.TermandCondition") %>">Terms and Conditions</a><br />
            Designed by <a href="http://www.asiablaze.com">AsiaBlaze Design</a>.
		</div>
	</div>
	</com:TForm>
</center>
</body>
</html>