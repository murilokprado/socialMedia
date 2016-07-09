<?php

namespace Application\Form;

use Zend\Form\Form;

class RegisterForm extends Form {
	function __construct($name = null) {
		parent::__construct('register');
		
		parent::setAttribute('method', 'post');
		parent::setAttribute('action', 'create');
		
		//criando os campos
		
		$this->add(array(
			'name' => 'nome', 
			'attributes' => array(
				'type' => 'text',
				'required' => 'required',
				'placeholder' => 'nome',
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'Insira seu nome: ',
				'label_attributes' => array('class' => 'control-label'),
			),
		));

		$this->add(array(
				'name' => 'email',
				'attributes' => array(
					'type' => 'email',
					'required' => 'required',
					'placeholder' => 'e-mail',
					'class' => 'form-control',
				),
				'options' => array(
					'label' => 'Insira seu email: ',
					'label_attributes' => array('class' => 'control-label'),
				),
		));
		
		$this->add(array(
				'name' => 'senha',
				'attributes' => array(
					'type' => 'password',
					'required' => 'required',
					'placeholder' => 'senha',
					'class' => 'form-control',
				),
				'options' => array(
					'label' => 'Insira uma senha: ',
					'label_attributes' => array('class' => 'control-label'),
				),
		));		

		$this->add(array(
			'name' => 'confirmaSenha',
			'attributes' => array(
				'type' => 'password',
				'required' => 'required',
				'placeholder' => 'redigitar senha',
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'Confirme a senha: ',
				'label_attributes' => array('class' => 'control-label'),
			),
		));
		
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
					'type' => 'submit',
					'required' => 'required',
					'value' => 'Cadastrar',
					'class' => 'btn btn-lg btn-primary btn-block',
				),
		));
		
	}
}	