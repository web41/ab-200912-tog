<div class="box">
	<h2>newsletter signup</h2>
	<div class="content">
        Enter your email address to receive FREE updates and information about The Organic Grocer and our products
        <com:TTextBox ID="txtName" Text="Enter your name" CssClass="textbox" Width="110px" Attributes.onclick="this.select()"/><br />
		<com:TRequiredFieldValidator ID="val1"
			CssClass="newsletter_error"
			ControlToValidate="txtName"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('ITEM_REQUIRED','Name') %>"
			Display="Dynamic"
			FocusOnError="True"
			InitialValue="Enter your name"
			ValidationGroup="Newsletter"/>
		<com:TRequiredFieldValidator ID="val2"
			CssClass="newsletter_error"
			ControlToValidate="txtName"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('ITEM_REQUIRED','Name') %>"
			Display="Dynamic"
			FocusOnError="True"
			ValidationGroup="Newsletter"/><br />
		<com:TTextBox ID="txtEmail" CssClass="textbox" Width="110px" Text="Enter your email" Attributes.onclick="this.select()"/>
		<com:TButton ID="btnSubmit" CssClass="button" Style="margin:10px 0 0 0" Text="Submit" OnClick="btnSubmit_Clicked" ValidationGroup="Newsletter"/><br />
		
		<com:TRequiredFieldValidator ID="val3"
			CssClass="newsletter_error"
			ControlToValidate="txtEmail"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('ITEM_REQUIRED','Email') %>"
			Display="Dynamic"
			FocusOnError="True"
			ValidationGroup="Newsletter"/>
		<com:TEmailAddressValidator ID="val4"
			CssClass="newsletter_error"
			ControlToValidate="txtEmail"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('ITEM_INVALID','Email') %>"
			Display="Dynamic"
			FocusOnError="True"
			ValidationGroup="Newsletter"/>
		<com:TCustomValidator ID="val5"
			CssClass="newsletter_error"
			ControlToValidate="txtEmail"
			OnServerValidate="uniqueCheck_ServerValidated"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('NEWSLETTER_EXISTS') %>"
			Display="Dynamic"
			FocusOnError="True"
			ValidationGroup="Newsletter"/>
	</div>
</div>