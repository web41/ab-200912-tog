<ul id="left_category">
	<li><a href="">Best Sellers</a></li>
	<li><a href="">New Arrivals</a></li>
	<li><a href="">Promotions</a></li>
	<li style="border-bottom:none;"><a href=""><b>Shop by brands</b></a></li>
	<li class="title">Shop by products</li>
	<com:TRepeater ID="rptCategoryMenu" OnItemCreated="rptCategoryMenu_ItemCreated">
		<prop:ItemTemplate>
			<li><a href="<%# $this->Data ? $this->Service->ConstructUrl('shop.Index',array('id'=>$this->Data->ID,'alias'=>$this->Data->Alias)) : "" %>" <%# $this->Data ? ($this->Request["id"]==$this->Data->ID?"class='active'":"") : "" %>><%= $this->Data ? $this->Data->Name : "" %></a>
				<com:TRepeater ID="ChildCategory">
					<prop:HeaderTemplate><ul></prop:HeaderTemplate>
					<prop:ItemTemplate>
						<li><a href="<%# $this->Data ? $this->Service->ConstructUrl('shop.Index',array('id'=>$this->Data->Parent->ID,'alias'=>$this->Data->Parent->Alias,'subid'=>$this->Data->ID,'subalias'=>$this->Data->Alias)) : "" %>" <%= $this->Data ? ($this->Request["subid"]==$this->Data->ID?"class='active'":"") : "" %>><%= $this->Data ? $this->Data->Name : "" %></a></li>
					</prop:ItemTemplate>
					<prop:FooterTemplate></ul></prop:FooterTemplate>
				</com:TRepeater>
			</li>
		</prop:ItemTemplate>
	</com:TRepeater>
</ul>