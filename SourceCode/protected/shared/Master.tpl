<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<com:THead>
	<title><com:TContentPlaceHolder ID="title" /></title>
    <meta name="description" content="The Organic Grocer is committed to delivering fresh organic produce as well as the best organic brands to our customers each week, while focusing on convenience and value." />
    <meta name="keywords" content="organic , health food , health and living , organic living , organics you , feel good food , farm box , weekly fresh produce , special gifts , duchy festive hampers , hampers , mother and child , special dietary needs , free of synthetic chemicals , free from pesticides , gluten free , wheat free , dairy free , free range , organic stores , non peroxide hair dye" />
	<noscript><meta http-equiv="refresh" content="0;URL=noscript.html" /></noscript>
	<com:TContentPlaceHolder ID="meta" />
	<com:TContentPlaceHolder ID="script" />
    <link href="<%= $this->UrlPrefix %>/scripts/a.m/a.m.css" rel="stylesheet" type="text/css" />
	<link href="<%= $this->UrlPrefix %>/scripts/calendar/jquery.calendar.css" rel="stylesheet" type="text/css" />
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
	
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-6257281-7']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</com:THead>
<body>
<center>
	<com:TForm>
	<div id="container">
		<!-- Banner -->
		<div id="banner">
			<div id="logo">
				<a href="<%= $this->Service->ConstructUrl("Index") %>"><img src="<%= $this->Page->Theme->BaseUrl %>/images/logo.png" width="364" height="119" alt="logo" /></a>
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
					<com:TDropDownList ID="cboBrandSelector" PromptText="Search all Brands" PromptValue="0" DataTextField="Name" DataValueField="ID" />
					<com:TTextBox ID="txtSearchText" CssClass="textbox" Text="<%= $this->DEFAULT_SEARCH_TEXT %>" />
					<script type="text/javascript">
						Event.observe($('<%= $this->txtSearchText->ClientID %>'), 'focus', function(e){
							if (e.element().value == "<%= $this->DEFAULT_SEARCH_TEXT %>")
								e.element().value = "";
						});
						Event.observe($('<%= $this->txtSearchText->ClientID %>'), 'blur', function(e){
							if (e.element().value == "")
								e.element().value = "<%= $this->DEFAULT_SEARCH_TEXT %>";
						});
					</script>
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
            <a href="<%= $this->Service->ConstructUrl("shop.Help") %>">Help</a></li>  |  
			<a href="<%= $this->Service->ConstructUrl("shop.ContactUs") %>">Contact Us</a></li> |
			<a href="<%= $this->Service->ConstructUrl("shop.TermandCondition") %>">Terms and Conditions</a><br />
			Copyright &copy; 2010 <a href="<%= $this->Service->ConstructUrl("shop.Index") %>">The Organic Grocer</a>. All rights reserved.<br />
            Designed by <a href="http://www.asiablaze.com">AsiaBlaze Design</a><br />
            <img src="<%= $this->Page->Theme->BaseUrl %>/images/paypal.png" alt="" /> &nbsp;
            <img src="<%= $this->Page->Theme->BaseUrl %>/images/visa.png" alt="" /> &nbsp;
            <img src="<%= $this->Page->Theme->BaseUrl %>/images/master.png" alt="" /> &nbsp;
            <img src="<%= $this->Page->Theme->BaseUrl %>/images/amex.png" alt="" /> &nbsp;
            <img src="<%= $this->Page->Theme->BaseUrl %>/images/circus.png" alt="" />
		</div>
	</div>
	</com:TForm>
</center>
</body>
</html>