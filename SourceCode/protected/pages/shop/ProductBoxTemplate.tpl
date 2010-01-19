<!-- Product box -->
<div class="product_box">
	<a href="<%= $this->Data ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->ID,"alias"=>$this->Data->Alias)) : "#" %>">
		<img src="<%# $this->Page->Master->UrlPrefix %>/useruploads/images/product/thumbs/<%# $this->Data ? $this->Data->ThumbnailPath : "" %>" alt="<%= $this->Data ? $this->Data->Name : "" %>" style="width:124px;"/>
	</a>
	<div class="title">
		<h2><%# $this->Data ? $this->Data->Brand->Name : "" %></h2>
		<div class="price">Price: <b><com:TActiveLabel ID="lblPrice" /></b> <com:TActiveLabel ID="lblDiscountPrice" /></div>
	</div>
	<div class="title">
		<h3><a href="<%= $this->Data ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->ID,"alias"=>$this->Data->Alias)) : "#" %>"><%= $this->Data ? $this->Data->Name : "" %></a></h3>
	</div>
	<div class="content">
		<div><%= $this->Data ? $this->Data->Brief : "" %></div>
		<div><b><%= $this->Data ? $this->Data->FreeText1 : "" %></b></div>
		<!--div><b>Size</b>: 200g</div-->
		<div style="color:#eb0010"><%= $this->Data ? $this->Data->FreeText2 : "" %></div>
		<div style="margin-top:20px;">
			<div style="float:left;">
				Quantity: 
				<com:TActiveDropDownList ID="cboQuantitySelector" AutoPostBack="false"/>
				&nbsp;&nbsp;
				<com:TActiveDropDownList ID="cboPropertySelector" PromptValue="0" PromptText="Select..." OnCallBack="cboPropertySelector_CallBack" />
			</div>
			<div style="float:right;">
				<com:TActiveLinkButton ID="btnAddToCart" Text="Add to bag" CssClass="btn_addtocart" OnClick="btnAddToCart_Clicked" Attributes.onclick="return validateDropDownList('<%= $this->cboPropertySelector->ClientID %>');"/>
			</div>
		</div>
	</div>
	<com:THiddenField ID="txtID" Value="<%= $this->Data ? $this->Data->ID : 0 %>" />
</div>
<!-- Product box: END -->