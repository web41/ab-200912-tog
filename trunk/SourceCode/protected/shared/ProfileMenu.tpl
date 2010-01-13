<ul id="left_category" style="float:left;">
	<li><b>your account</b></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.orders.Index") %>">My Purchase History</a></li>
    <li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.MyFavourite") %>">My Favourites</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.MyOrganicPoint") %>">My Organic Points</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.addresses.Index") %>">Shipping Address & Contacts</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.ChangeProfile") %>">Personal Particulars</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.ChangePassword") %>">Password Change</a></li>
</ul>
