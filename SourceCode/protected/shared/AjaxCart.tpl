<div class="box" style="float:left;margin:0;">
	<div class="cart_title" style="margin:0;"><h2 style="font-size:13px;">your shopping bag</h2></div>
</div>
<div class="shoppingcart_box">
	<!-- Ajax cart -->
	<com:TActivePanel ID="ajaxCartPanel">
	</com:TActivePanel>
    <div class="product" style="height:5px;"></div>
    <com:TActiveImage ID="imgLoading" ImageUrl="<%= $this->Page->Theme->BaseUrl %>/images/loading2.gif" />
    
	<com:TTimeTriggeredCallback ID="load" StartTimerOnLoad="true" Interval="1000" OnCallBack="load_TriggerCallBack"/>
	<!-- Ajax cart: END -->
	<a href="<%= $this->Service->ConstructUrl("shop.cart.Index") %>" class="btn_cart">view shopping bag</a>
	<a href="<%= $this->Service->ConstructUrl("shop.checkout.Index") %>" class="btn_cart">&nbsp;&nbsp;&nbsp;&nbsp;checkout now</a>
</div>