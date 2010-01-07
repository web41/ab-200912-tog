<div class="cart_item" style="margin:0;">
	<div class="prd_item" style="width:380px;"><a href="<%= $this->Data && $this->Data->Product ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->Product->ID,"alias"=>$this->Data->Product->Alias)) : "#" %>"><%= $this->Data && $this->Data->Product ? $this->Data->Product->Name.' - '.$this->Data->Property->Name : "" %></a></div>
	<div class="qty"><com:TLabel Text="<%# $this->Data ? $this->Data->Quantity : 0 %>" /><com:THiddenField ID="txtID" Value="<%= $this->Data ? $this->Data->HashID : '' %>" /></div>
	<div class="price"><com:TNumberFormat Type="currency" Currency="USD" Value="<%= $this->Data ? $this->Data->Subtotal : 0 %>" /></div>
</div>