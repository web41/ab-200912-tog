<div class="cart_item" style="margin:0;">
	<div class="prd_item" style="width:230px;"><a href="<%= $this->Data && $this->Data->Product ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->Product->ID,"alias"=>$this->Data->Product->Alias)) : "#" %>"><%= $this->Data && $this->Data->Product ? $this->Data->Product->Name.' - '.($this->Data->Property?$this->Data->Property->Name:"") : "" %></a></div>
	<div class="qty"><com:TDropDownList ID="cboQtySelector" CssClass="textbox" Height="auto" Width="35px" /><com:TActiveButton ID="btnUpdate" CssClass="btn_refresh" OnClick="btnUpdate_Clicked" /></div>
    <div class="price"><com:TActiveLabel ID="lblUnitPrice" /></div>
	<div class="price"><com:TActiveLabel ID="lblSubtotal" /></div>
	<div class="remove"><com:TButton ID="btnDelete" CssClass="btn_delete" OnClick="btnDelete_Clicked" Attributes.onclick="if (!confirm('Are you sure you want to delete this item?')) return false;"/><com:THiddenField ID="txtID" Value="<%= $this->Data ? $this->Data->HashID : '' %>" /></div>
</div>