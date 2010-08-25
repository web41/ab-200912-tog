<tr>
	<td width="40%" style="border:0;border-bottom:1px dashed #dddddd;text-align:left;">
		<a href="<%= $this->Data && $this->Data->Product ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->Product->ID,"alias"=>$this->Data->Product->Alias)) : "#" %>"><%= $this->Data && $this->Data->Product ? ($this->Data->Product->Brand?'<strong>'.$this->Data->Product->Brand->Name.'</strong> - ':"").$this->Data->Product->Name : "" %></a>
	</td>
	<td width="8%" style="border:0;border-bottom:1px dashed #dddddd;">
		<com:TLabel Text="<%# $this->Data ? $this->Data->Quantity : 0 %>" />
	</td>
	<td width="16%" style="border:0;border-bottom:1px dashed #dddddd;text-align:left;">
		<%# ($this->Data->Property?$this->Data->Property->Name:"") %>/unit
	</td>
	<td width="12%" style="border:0;border-bottom:1px dashed #dddddd;">
		<com:TNumberFormat Type="currency" Currency="USD" Value="<%= $this->Data->UnitPrice %>" />
	</td>
	<td width="12%" style="border:0;border-bottom:1px dashed #dddddd;">
		<com:TNumberFormat Type="<%# $this->Data->DiscountIsPercent?'percentage':'currency' %>" Culture="en_US" Currency="USD" Value="<%# $this->Data->DiscountAmount %>" />
	</td>
	<td width="12%" style="border:0;border-bottom:1px dashed #dddddd;">
		<com:TNumberFormat Type="currency" Currency="USD" Value="<%# $this->Data ? $this->Data->Subtotal : 0 %>" />
	</td>
</tr>