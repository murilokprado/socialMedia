<?php

namespace Application\Form;

use Zend\Form\Form;
class UserForm extends Form {
	
	function __construct($name = null, $statusArray = null, $statusSelecionado = array()) {
		parent::__construct('user');
		//parent::__construct('method', 'post');
		$this->setAttribute('enctype','multipart/form-data');
		$this->add(array(
				'name' => 'file',
				'type' => 'File',
				'options' => array(
						'label' => 'Upload File '
				),
		));
		
		//criando os campos
		$this->add(array(
			'name' => 'nome', 
			'attributes' => array(
				'type' => 'text',
				'required' => true,
				'placeholder' => 'insira seu nome',
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'Nome: ',
				'label_attributes' => array('class' => 'col-sm-2 control-label'),
			),
		));
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'status',
			'options' => array(
				'label' => 'Status:',
				'value_options' =>	$statusArray,
			),
			'attributes' => array(
				'value' => $statusSelecionado, //set selected to '1'
				'class' => 'form-control',
			),
		));	
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'required' => 'required',
				'value' => 'Enviar',
				'class' => 'btn btn-primary pull-right',
			),
		));
	}
}	