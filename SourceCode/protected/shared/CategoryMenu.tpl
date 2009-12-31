<ul>
	<li><a href="">Best Sellers</a></li>
	<li><a href="">New Arrivals</a></li>
	<li><a href="">Promotions</a></li>
	<li style="border-bottom:none;"><a href=""><b>Shop by brands</b></a></li>
	<li class="title">Shop by products</li>
	<com:TRepeater ID="rptCategoryMenu">
		<prop:ItemTemplate>
			<li><a href="###"><%= $this->Data->Name %></a></li>
		</prop:ItemTemplate>
	</com:TRepeater>
</ul>