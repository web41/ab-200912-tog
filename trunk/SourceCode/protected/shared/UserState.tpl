<div class="box">
	<div class="cart_title"><h2>Your Cart</h2></div>
	<div class="content" style="text-align:center;">
		Welcome <com:THyperLink ID="lblName" Text="Guest" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.profile.Index') %>" />, <br />
		<com:TLinkButton ID="btnLogout" Text="Not you? Logout" OnClick="btnLogout_Clicked" CausesValidation="false"/>
		<com:TPanel ID="GuestPane">
			<com:THyperLink ID="lnkLogin" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Login') %>" Text="Login" /> | 
			<com:THyperLink ID="lnkRegister" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Register') %>" Text="Register" />
		</com:TPanel>
	</div>
</div>