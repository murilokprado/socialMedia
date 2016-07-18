<?php

namespace Application\Form;

use Zend\Form\Form;

class FriendForm extends Form {
	function __construct($name = null, $options = null) {
		parent::__construct('friend');
		
		parent::setAttribute('method', 'post');
		//parent::setAttribute('action', 'login');
		
		$this->add(array(
				'name' => 'lat',
				'type' => 'hidden',
				'attributes'=>array(
						'id'=>'lat',
				)
		));
		
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type' => 'submit',
						'required' => 'required',
						'value' => 'Aceitar',
						'class' => 'btn btn-lg btn-success btn-block',
							
				),
		));		
	}
}	