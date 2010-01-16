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

<div class="shoppingcart_box">
	<!-- Ajax cart -->
    <div class="shopping_bag">
        <div class="product">
        	<b>Nordic Naturals Arctic Cod Liver Oil 1000 mg - Lemon, 90 sgls.</b><br />
            Quantity: 1 &nbsp;-&nbsp; Price: <span>$10</span>
        </div>
        <div class="product">
        	<b>BELLAMY'S Toothiepegs 100g</b><br />
            Quantity: 1 &nbsp;-&nbsp; Price: <span>$15</span>
        </div>
        <div class="product">
        	<b>Follow-on Formula 900g</b><br />
            Quantity: 1 &nbsp;-&nbsp; Price: <span>$24</span>
        </div>
        <div class="product">
        	<b>Peppermint, Spearmint & Strawberry 1.5g</b><br />
            Quantity: 1 &nbsp;-&nbsp; Price: <span>$22</span>
        </div>
        <div class="product">
        	<b>Natural Sea Scallop V/P</b><br />
            Quantity: 1 &nbsp;-&nbsp; Price: <span>$34</span>
        </div>
    </div>
    <!-- Ajax cart: END -->
	<a href="<%= $this->Service->ConstructUrl("shop.cart.Index") %>" class="btn_cart">view shopping bag</a>
	<a href="<%= $this->Service->ConstructUrl("shop.checkout.Index") %>" class="btn_cart2">checkout</a>
</div>