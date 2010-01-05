<?php

class ApplyDiscount extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->cboDiscountSelector->DataSource = DiscountRecord::finder()->getAllItems();
			$this->cboDiscountSelector->DataBind();
			$this->listBrand->DataSource = BrandRecord::finder()->getAllItems();
			$this->listBrand->DataBind();
			$this->listProduct->DataSource = ProductRecord::finder()->getAllItems();
			$this->listProduct->DataBind();
			$this->listCategory->DataSource = CategoryRecord::finder()->getCategoryTree(true);
			$this->listCategory->DataBind();
			
			$this->radApplyToProduct->Checked = true;
		}
	}
	
	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			try
			{
				$action = "update-success";
				$msg = $this->Application->getModule("message")->translate("APPLY_DISCOUNT_SUCCESS");
				if ($this->radApplyToProduct->Checked)
				{
					foreach($this->listProduct->SelectedValues as $id)
					{
						$activeRecord = ProductRecord::finder()->findByPk($id);
						if ($activeRecord instanceof ProductRecord)
						{
							$activeRecord->DiscountID = $this->cboDiscountSelector->SelectedValue;
							$activeRecord->save();
						}
					}
					$this->Response->redirect($this->Service->ConstructUrl("admincp.ProductManager",array("action"=>$action, "msg"=>$msg)));
				}
				else if ($this->radApplyToBrand->Checked)
				{
					foreach($this->listBrand->SelectedValues as $id)
					{
						$activeRecord = BrandRecord::finder()->withProducts()->findByPk($id);
						if ($activeRecord instanceof BrandRecord)
						{
							foreach($activeRecord->Products as $product)
							{
								$product->DiscountID = $this->cboDiscountSelector->SelectedValue;
								$product->save();
							}
						}
					}
					$this->Response->redirect($this->Service->ConstructUrl("admincp.BrandManager",array("action"=>$action, "msg"=>$msg)));
				}
				else if ($this->radApplyToCategory->Checked)
				{
					foreach($this->listBrand->SelectedValues as $id)
					{
						$activeRecord = CategoryRecord::finder()->withProducts()->findByPk($id);
						if ($activeRecord instanceof CategoryRecord)
						{
							foreach($activeRecord->Products as $product)
							{
								$product->DiscountID = $this->cboDiscountSelector->SelectedValue;
								$product->save();
							}
						}
					}
					$this->Response->redirect($this->Service->ConstructUrl("admincp.CategoryManager",array("action"=>$action, "msg"=>$msg)));
				}
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("UNKNOWN_ERROR");
			}
		}
	}
}

?>