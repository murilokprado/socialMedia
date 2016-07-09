<?php

namespace Application\Form;

use Zend\Form\Form;

class LoginForm extends Form {
	function __construct($name = null, $options = null) {
		parent::__construct('login');
		
		parent::setAttribute('method', 'post');
		parent::setAttribute('action', 'login');
		
		//criando os campos
		$this->add(array(
			'name' => 'e-mail', 
			'attributes' => array(
				'type' => 'text',
				'required' => true,
				'placeholder' => 'insira seu e-mail aqui',
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'Insira seu e-mail: ',
				'label_attributes' => array('class' => 'control-label'),
			),
		));

		$this->add(array(
			'name' => 'senha',
			'attributes' => array(
				'type' => 'password',
				'required' => 'required',
				'placeholder' => 'insira uma senha aqui',
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'Insira uma senha: ',
				'label_attributes' => array('class' => 'control-label'),
			),
		));		

		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'required' => 'required',
				'value' => 'Entrar',
				'class' => 'btn btn-lg btn-primary btn-block',
					
			),
		));
		
	}
}	