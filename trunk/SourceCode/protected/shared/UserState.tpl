<div class="box" style="float:left;">
	<div class="my_account">
    	<div class="top">my account</div>
        <div class="center">
        	Welcome <com:THyperLink Style="text-transform:capitalize;" ID="lblName" Text="Guest" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.profile.Index') %>" />,<com:THyperLink ID="lblAdmin" Text="&nbsp;[Admin Panel]" NavigateUrl="<%= $this->Service->ConstructUrl('admincp.Index') %>" Visible="<%= $this->Application->User->Roles && $this->Application->User->Roles[0]=='Administrator' %>"/><br />
            <com:TLinkButton ID="btnLogout" Text="Logout" OnClick="btnLogout_Clicked" CausesValidation="false"/>
            <com:TPanel ID="GuestPane">
                <com:THyperLink ID="lnkLogin" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Login') %>" Text="Login" /> | 
                <com:THyperLink ID="lnkRegister" NavigateUrl="<%= $this->Service->ConstructUrl('shop.account.Register') %>" Text="Register" />
            </com:TPanel>
            
            <com:TPanel ID="userMenu" Style="text-align:left;" Visible="false">
            	<ul style="float:left;margin:0;padding:10px 0 0 5px;list-style-type:none;">
                    <li><a href="<%= $this->Service->ConstructUrl("shop.account.orders.Index") %>">My Purchase History</a></li>
                    <li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.MyFavourite") %>">My Favourites</a></li>
                    <li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.MyOrganicPoint") %>">My Organic Points</a></li>
                    <li><a href="<%= $this->Service->ConstructUrl("shop.account.addresses.Index") %>">Shipping Address & Contacts</a></li>
                    <li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.ChangeProfile") %>">Personal Particulars</a></li>
                    <li><a href="<%= $this->Service->ConstructUrl("shop.account.profile.ChangePassword") %>">Password Change</a></li>
                </ul>
            </com:TPanel>
        </div>
        <div class="bottom"><!-- --></div>
    </div>
</div>