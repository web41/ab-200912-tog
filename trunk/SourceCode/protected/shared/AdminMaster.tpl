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
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/chkboxgroup.js"></script>
	<script type="text/javascript">
		var main_menu = "<%= $this->pmlMainMenu->ClientID %>";
	</script>
	<script type="text/javascript" src="<%= $this->UrlPrefix %>/scripts/javascript.js"></script>
	<com:TContentPlaceHolder ID="script" />
</com:THead>
<body>
<center>
	<com:TForm>
		<div id="banner">
			<div id="logo">
				<a href="<%= $this->Service->ConstructUrl("admincp.Index") %>"><b>TheOrganicGrocer.com</b> Administration</a>
			</div>
            <div id="top_box">
            	<table border="0" cellpadding="0" cellspacing="0" width="auto" height="100%">
                	<tr>
                    	<td style="vertical-align:bottom">
                        	<div class="box">
                            	<div class="top">
                                	<div><div><!-- --></div></div>
                                </div>
                                <div class="content">
                                	Hello, <%= $this->Application->User->Email %> ::<com:TLinkButton Text="Logout" OnClick="btnLogout_Clicked" />::<a href="<%= $this->Service->ConstructUrl("shop.Index") %>" target="_blank">Homepage</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
		</div>
		<div id="main_menu">
			<com:PMenuList ID="pmlMainMenu" />
		</div>
		<div id="container">
			<com:TContentPlaceHolder ID="content" />
		</div>
	</com:TForm>
</center>
</body>
</html>