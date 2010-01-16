<div class="shoppingcart_box">
	<!-- Ajax cart -->
	<com:TActivePanel ID="ajaxCartPanel">
		
	</com:TActivePanel>
	<com:TActiveImage ID="imgLoading" ImageUrl="<%= $this->Page->Theme->BaseUrl %>/images/loading2.gif" />
	<com:TTimeTriggeredCallBack ID="load" StartTimerOnLoad="true" Interval="1000" OnCallBack="load_TriggerCallBack"/>
	<!-- Ajax cart: END -->
	<a href="<%= $this->Service->ConstructUrl("shop.cart.Index") %>" class="btn_cart">view shopping bag</a>
	<a href="<%= $this->Service->ConstructUrl("shop.checkout.Index") %>" class="btn_cart2">checkout</a>
</div>