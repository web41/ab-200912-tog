<!-- Product box -->
<div class="product_box">
	<a href="<%= $this->Data ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->ID,"alias"=>$this->Data->Alias)) : "#" %>">
		<img src="<%# $this->Page->Master->UrlPrefix %>/useruploads/images/product/thumbs/<%# $this->Data ? $this->Data->ThumbnailPath : "" %>" alt="<%= $this->Data ? $this->Data->Name : "" %>" style="width:124px;"/>
	</a>
	<div class="title">
		<h2><%# $this->Data ? $this->Data->Brand->Name : "" %></h2>
		<div class="price">Price: <b><com:TActiveLabel ID="lblPrice" /></b> <com:TActiveLabel ID="lblDiscountPrice" /></div>
	</div>
	<div class="title" style="padding-bottom:5px;">
		<h3>
			<a href="<%= $this->Data ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->ID,"alias"=>$this->Data->Alias)) : "#" %>"><%= $this->Data ? $this->Data->Name : "" %>
			<!---com:TActiveLabel ID="lblProperty" /--->
			</a>
		</h3>
	<div class="price" style="margin-top:3px;">
		<a href="<%= $this->Data ? $this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->ID,"alias"=>$this->Data->Alias)) : "#" %>">
		<input type="button" value="View Details" class="button" />
		</a>
	</div>		
	</div>

	<div class="content">
		
		<!--div><com:TLabel ID="lblDesc" /></div-->
		<div><b><%= $this->Data ? $this->Data->FreeText1 : "" %></b></div>
		<!--div><b>Size</b>: 200g</div-->
		<div><b><%= $this->Data ? $this->Data->FreeText2 : "" %></b></div>
		<div style="margin-top:5px;">
			<div style="float:left;">
				Quantity: 
				<com:TActiveDropDownList ID="cboQuantitySelector" AutoPostBack="false"/>
				<div style="margin-top:5px;">
				Selection: <com:TActiveDropDownList ID="cboPropertySelector" PromptValue="0" PromptText="Select..." OnCallBack="cboPropertySelector_CallBack" Style="font-size:11px;margin-top:5px;font-family:Tahoma;" />
                </div>
			</div>
			<div style="float:right;">
				<com:TActiveImage ID="imgLoading" CssClass="imgloading" ImageUrl="<%= $this->Page->Theme->BaseUrl %>/images/loading2.gif" Width="16px" Height="16px" />
				<com:TActiveLinkButton ID="btnAddToCart" Text="Add to bag" CssClass="btn_addtocart" OnClick="btnAddToCart_Clicked" Attributes.onclick="return validateDropDownList(this,'<%= $this->cboPropertySelector->ClientID %>', '<%= $this->imgLoading->ClientID %>');"/>
			</div>
		</div>
	</div>
	<com:THiddenField ID="txtID" Value="<%= $this->Data ? $this->Data->ID : 0 %>" />
</div>
<!-- Product box: END -->