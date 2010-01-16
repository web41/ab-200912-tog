<div class="box" style="float:left;margin-bottom:10px;">
	<div class="cart_title"><h2 style="font-size:13px;">your shopping bag</h2></div>
	<div class="content" style="text-align:center;">
		Welcome <com:THyperLink Style="text-transform:capitalize;" ID="lblName" Text="Guest" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.profile.Index') %>" />&nbsp;<com:THyperLink ID="lblAdmin" Text="(Admin)" NavigateUrl="<%= $this->Service->ConstructUrl('admincp.Index') %>" Visible="<%= $this->Application->User->Roles && $this->Application->User->Roles[0]=='Administrator' %>"/>, <br />
		<com:TLinkButton ID="btnLogout" Text="Not you? Logout" OnClick="btnLogout_Clicked" CausesValidation="false"/>
		<com:TPanel ID="GuestPane">
			<com:THyperLink ID="lnkLogin" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Login') %>" Text="Login" /> | 
			<com:THyperLink ID="lnkRegister" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Register') %>" Text="Register" />
		</com:TPanel>
	</div>
</div>