<div class="box" style="float:left;">
	<div class="cart_title"><h2>Your Cart</h2></div>
	<div class="content" style="text-align:center;">
		Welcome <com:THyperLink ID="lblName" Text="Guest" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.profile.Index') %>" />&nbsp;<com:THyperLink ID="lblAdmin" Text="(Admin)" NavigateUrl="<%= $this->Service->ConstructUrl('admincp.Index') %>" Visible="<%= $this->Application->User->Roles && $this->Application->User->Roles[0]=='Administrator' %>"/>, <br />
		<com:TLinkButton ID="btnLogout" Text="Not you? Logout" OnClick="btnLogout_Clicked" CausesValidation="false"/>
		<com:TPanel ID="GuestPane">
			<com:THyperLink ID="lnkLogin" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Login') %>" Text="Login" /> | 
			<com:THyperLink ID="lnkRegister" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Register') %>" Text="Register" />
		</com:TPanel>
	</div>
</div>
<div class="shoppingcart_box">
	<a href="<%= $this->Service->ConstructUrl("shop.cart.Index") %>" class="btn_cart">VIEW CART</a>
	<a href="<%= $this->Service->ConstructUrl("shop.checkout.Index") %>" class="btn_cart">CHECKOUT</a>
</div>