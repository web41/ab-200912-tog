<ul id="left_category">
	<li><a href="">Best Sellers</a></li>
	<li><a href="">New Arrivals</a></li>
	<li><a href="">Promotions</a></li>
	<li style="border-bottom:none;"><a href=""><b>Shop by brands</b></a></li>
	<li class="title">Shop by products</li>
	<com:TRepeater ID="rptCategoryMenu">
		<prop:ItemTemplate>
			<li><a href="###"><%= $this->Data->Name %></a>
            	<ul>
                	<li><a class="active">sub cateogory 1</a></li>
                    <li><a>sub cateogory 2</a></li>
                    <li><a>sub cateogory 3</a></li>
                    <li><a>sub cateogory 4</a></li>
                </ul>
            </li>
		</prop:ItemTemplate>
	</com:TRepeater>
</ul>