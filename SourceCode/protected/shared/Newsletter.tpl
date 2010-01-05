<div class="box">
	<h2>newsletter</h2>
	<div class="content">
		Enter your email address to receive our FREE newsletter on the organic market
		<com:TTextBox ID="txtEmail" CssClass="textbox" Width="125px" Text="Enter your email" Attributes.onclick="this.select()"/>
		<com:TButton ID="btnSubmit" CssClass="button" Text="Submit" OnClick="btnSubmit_Clicked" /><br />
		<com:TRequiredFieldValidator ID="val3"
			ControlToValidate="txtEmail"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('ITEM_REQUIRED','Email') %>"
			Display="Dynamic"
			FocusOnError="True"/>
		<com:TEmailAddressValidator ID="val4"
			ControlToValidate="txtEmail"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('ITEM_INVALID','Email') %>"
			Display="Dynamic"
			FocusOnError="True"/>
		<com:TCustomValidator ID="val5"
			ControlToValidate="txtEmail"
			OnServerValidate="uniqueCheck_ServerValidated"
			ErrorMessage="<%= $this->Application->getModule('message')->translate('ITEM_EXISTS','Email') %>"
			Display="Dynamic"
			FocusOnError="True"/>
	</div>
</div>