<ul id="left_category" style="float:left;">
	<li><b>your account</b></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.Index") %>">Account Information</a></li>
    <li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.ChangePassword") %>">Change Password</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.ChangeProfile") %>">Change Information</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.addresses.Index") %>">Addresses</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.orders.Index") %>">Orders History</a></li>
</ul>
