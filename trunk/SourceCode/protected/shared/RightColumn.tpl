<div class="box">
	<div class="cart_title"><h2>Your Cart</h2></div>
	<div class="content" style="text-align:center;">
		<a href="<%= $this->Service->ConstructUrl("shop.account.Login") %>">Login</a> | <a href="<%= $this->Service->ConstructUrl("shop.account.Register") %>">Sign up</a> | <com:TLinkButton Text="Logout" OnClick="btnLogout_Clicked" CausesValidation="false"/>
	</div>
</div>
<div class="shoppingcart_box">
	<a href="<%= $this->Service->ConstructUrl("shop.cart.Index") %>" class="btn_cart">VIEW CART</a>
	<a href="<%= $this->Service->ConstructUrl("shop.checkout.Index") %>" class="btn_cart">CHECKOUT</a>
</div>
<div class="box">
	<h2>Order Schedule</h2>
	<div class="content" style="border-bottom:solid 1px #503a1d;padding-bottom:10px;">
		Order by Tuesday 5 pm<br />
		Delivery this Friday/Saturday
	</div>
</div>
<div class="box">
	<img src="<%= $this->Page->Theme->BaseUrl %>/images/logo_1.png" alt=""/>&nbsp;&nbsp;
	<img src="<%= $this->Page->Theme->BaseUrl %>/images/logo_2.png" alt="" />
</div>