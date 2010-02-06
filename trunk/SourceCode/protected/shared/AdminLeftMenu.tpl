<!-- Box -->
<div class="box">
	<div class="title">
		<div class="title_bg"><h2>reports</h2></div>
		<!-- div class="title_border"></div -->
	</div>
	<div class="content nopadding">
		<ul id="sidebar_menu">
            <li><a href="<%= $this->Service->ConstructUrl("admincp.SalesReport") %>">Sales Report</a></li>
			<li><a href="<%= $this->Service->ConstructUrl("admincp.CustomerReport") %>">Customer Report</a></li>
			<li><a href="<%= $this->Service->ConstructUrl("admincp.ProductReport") %>">Product Report</a></li>
			<li><a href="<%= $this->Service->ConstructUrl("admincp.ViewItemsByOrder",array("type"=>"by-order")) %>">Purchased Items By Order</a></li>
			<li><a href="<%= $this->Service->ConstructUrl("admincp.ViewItemsBySupplier",array("type"=>"by-supplier")) %>">Purchased Items By Supplier</a></li>
		</ul>
	</div>
</div>
<!-- Box: END -->