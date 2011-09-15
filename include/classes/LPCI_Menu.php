<?php

class LPCI_Menu extends LPC_Menu
{
	function populateStructure()
	{
		$this->structure=array(
			'permissions'=>array(
				'label'=>'Permissions',
				'url'=>'?a=ph',
				'children'=>array(
					'users'=>array(
						'label'=>'Users',
						'url'=>'?a=ul',
						'permission'=>'LPC_Access_Users',
					),
					'groups'=>array(
						'label'=>'Groups',
						'url'=>'?a=gl',
						'permission'=>'LPC_Access_Groups',
					),
				),
			),
			'application'=>array(
				'label'=>'Back to application',
				'url'=>LPC_url,
			),
		);
	}
}
