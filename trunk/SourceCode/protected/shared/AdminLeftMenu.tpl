<!-- Box -->
<div class="box">
	<div class="title">
		<div class="title_bg"><h2>Quick Links</h2></div>
		<!-- div class="title_border"></div -->
	</div>
	<div class="content nopadding">
		<ul id="sidebar_menu">
			<li><a href="<%= $this->Service->ConstructUrl("admincp.MailingListManager") %>">Mailing List</a></li>
            <li><a href="<%= $this->Service->ConstructUrl("admincp.SalesReport") %>">Sales Report</a></li>
			<li><a href="<%= $this->Service->ConstructUrl("admincp.CustomerReport") %>">Customer Report</a></li>
			<li><a href="<%= $this->Service->ConstructUrl("admincp.ProductReport") %>">Product Report</a></li>
			<li><a href="<%= $this->Service->ConstructUrl("admincp.OrderToSupplier") %>">Export To Supplier</a></li>
		</ul>
	</div>
</div>
<!-- Box: END -->