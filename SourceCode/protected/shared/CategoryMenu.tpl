<ul id="left_category">
	<li><a href="<%= $this->Service->ConstructUrl("shop.Index",array("best_seller"=>1)) %>"><b style="padding:0;margin:0;color:#41A317;">Best Sellers</b></a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.Index",array("new_arrival"=>1)) %>"><b style="padding:0;margin:0;color:#41A317;">New Arrivals</b></a></li>
	<!--li><a href="<%= $this->Service->ConstructUrl('shop.Index',array('c'=>74,'calias'=>'gift-hampers')) %>"><b style="padding:0;margin:0;">Gift Hampers</b></a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.Index",array("promotion"=>1)) %>">Promotions</a></li>
	<li style="border-bottom:none;"><a href="<%= $this->Service->ConstructUrl("shop.ShopByBrand") %>"><b style="padding:0;margin:0;">Shop by brands</b></a></li-->
	<li class="title">Shop by categories</li>
	<com:TRepeater ID="rptCategoryMenu" OnItemCreated="rptCategoryMenu_ItemCreated">
		<!--prop:HeaderTemplate>
			<li><a href="<%= $this->Service->ConstructUrl("shop.Index") %>">All Categories</li>
		</prop:HeaderTemplate-->
		<prop:ItemTemplate>
			<li><a href="<%# $this->Data ? $this->Service->ConstructUrl('shop.Index',array('c'=>$this->Data->ID,'calias'=>$this->Data->Alias)) : "" %>" <%# $this->Data ? ($this->Request["c"]==$this->Data->ID?"class='active'":"") : "" %>><%= $this->Data ? $this->Data->Name : "" %></a>
				<com:TRepeater ID="ChildCategory">
					<prop:HeaderTemplate><ul></prop:HeaderTemplate>
					<prop:ItemTemplate>
						<li><a href="<%# $this->Data ? $this->Service->ConstructUrl('shop.Index',array('c'=>$this->Data->Parent->ID,'calias'=>$this->Data->Parent->Alias,'subc'=>$this->Data->ID,'subcalias'=>$this->Data->Alias)) : "" %>" <%= $this->Data ? ($this->Request["subc"]==$this->Data->ID?"class='active'":"") : "" %>><%= $this->Data ? $this->Data->Name : "" %></a></li>
					</prop:ItemTemplate>
					<prop:FooterTemplate></ul></prop:FooterTemplate>
				</com:TRepeater>
			</li>
		</prop:ItemTemplate>
	</com:TRepeater>
</ul>