<?php

class SpecialOffer extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->Page->IsPostBack)
		{
			if ($this->Request->contains('new_arrival'))
			{					
				$activeRecord = $this->getItem(1);
				if ($activeRecord && $activeRecord->ID > 0)
				{
					// Populates the input controls with the existing post data
					$this->lblTitle1->Text = $activeRecord->Title;
					$this->lblContent1->Text = $activeRecord->Content;
					$this->img1->Visible = true;
					$this->img1->ImageUrl = $this->Request->UrlManagerModule->UrlPrefix."/useruploads/images/offer/".$activeRecord->ImagePath;
				}	
				
				$activeRecord = $this->getItem(2);
				if ($activeRecord && $activeRecord->ID > 0)
				{
					// Populates the input controls with the existing post data
					$this->lblTitle2->Text = $activeRecord->Title;
					$this->lblContent2->Text = $activeRecord->Content;
					$this->img2->Visible = true;
					$this->img2->ImageUrl = $this->Request->UrlManagerModule->UrlPrefix."/useruploads/images/offer/".$activeRecord->ImagePath;
				}
				
				$this->specialOfferBox->Visible=True;
			}		
		}
	}
	
	protected function getItem($Sid)
	{
		$activeRecord = SpecialOfferRecord::finder()->findByspecial_offer_id($Sid);
		return $activeRecord;		
	}
}

?>