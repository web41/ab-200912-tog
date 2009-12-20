<com:TPanel CssClass="<%= $this->CssClass %>" Visible="<%= strlen($this->Text)>0 %>">
	<div class="top"><div><div><!-- --></div></div></div>
	<div class="center">
		<img src="<%= $this->Page->Theme->BaseUrl %>/images/<%= $this->IconImage %>" alt=""/>
		<%= $this->Text %>
	</div>
	<div class="bottom"><div><div><!-- --></div></div></div>
</com:TPanel>