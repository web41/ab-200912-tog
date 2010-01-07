<ul id="left_category" style="float:left;">
	<li><b>your account</b></li>
    <li><a href="<%= $this->Service->ConstructUrl("shop.Index",array("best_seller"=>1)) %>">change password</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.Index",array("new_arrival"=>1)) %>">change info</a></li>
	<li><a href="<%= $this->Service->ConstructUrl("shop.Index",array("promotion"=>1)) %>">addresses</a></li>
</ul>