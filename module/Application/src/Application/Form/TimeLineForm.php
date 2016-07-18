<?php

namespace Application\Form;

use Zend\Form\Form;

class TimelineForm extends Form {
	function __construct($name = null, $options = null) {
		parent::__construct('timeline');
		
		parent::setAttribute('method', 'post');
		parent::setAttribute('action', 'index');
		
		//criando os campos
		$this->add(array(
			'name' => 'mensagem', 
			'attributes' => array(
				'type' => 'textarea',
				'required' => true,
				'placeholder' => 'insira sua mensagem',
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'Mensagem: ',
				'label_attributes' => array('class' => 'col-sm-2 control-label'),
			),
		));
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'required' => 'required',
				'value' => 'Enviar',
				'class' => 'btn btn-primary',
			),
		));		
	}
}	