<div>
	Name: <com:TTextBox ID="txtName" CssClass="textbox" Width="200px" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Price: <com:TTextBox CssClass="textbox" ID="txtPrice" Width="75px" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Cost Price: <com:TTextBox CssClass="textbox" ID="txtCostPrice" Width="75px" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	In Stock: <com:TTextBox CssClass="textbox" ID="txtInStock" Width="75px" />
	&nbsp;&nbsp;&nbsp;
	<com:TImageButton ID="btnDelete" 
		ImageAlign="Middle" ImageUrl="<%# $this->Page->Theme->BaseUrl %>/images/btn_delete_2.png"
		OnClick="btnDelete_Clicked"/>
	<com:THiddenField ID="txtID" />
</div>
<br />