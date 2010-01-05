<div class="box">
	<h2>newsletter</h2>
	<div class="content">
		Enter your email address to receive our FREE newsletter on the organic market
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