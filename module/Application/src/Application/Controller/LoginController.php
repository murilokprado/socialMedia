<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\LoginForm;
use Zend\View\Model\ViewModel;
use Zend\Db\TableGateway\TableGateway;
use Application\Form\RegisterForm;
use Zend\Db\Sql\Sql;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Validator\Identical;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Session\Container;

class LoginController extends AbstractActionController {
	
	protected $loginTable;
	
	public function indexAction() {
		$loginForm = new LoginForm();
		$this->layout('layout/layoutEmpty');
		$view = new ViewModel(array('form'=>$loginForm));
		return $view;
	}

	public function loginAction()
	{
		$form = new LoginForm();
		$request = $this->getRequest();
		$email = $request->getPost('e-mail');
		$senha = $request->getPost('senha');
	
		$senha = md5(md5($senha));
		$dados = $this->getLoginTable()->select(array('email'=>$email,'senha'=>$senha));
		$row = $dados->current();
		$sessao = new Container('Auth');
		if ($request->isPost()) {
			if ($row != null) {
				$sessao -> autenticado = true;
				$sessao -> idUsuario = $row['codUsuario'];
				$sessao -> nome = $row['nome'];
				$logger = new Logger();
				$write = new Stream('./data/log/access.log');
				$logger->addWriter($write);
				
				$logger->log(Logger::INFO, 'LOG');
				$logger->info('Informacao de login - '. date('H:m:s') . ' usuario: ' . $row['email'] . ' entrou.');
				
				// autenticação de login -- quando conseguir logar mandar para a timeline***
				return $this->redirect()->toRoute('timeline');
				
			} else {
				$sessao -> autenticado = false;
				$sessao -> idUsuario = null;
				$this->flashMessenger()->addErrorMessage(utf8_encode('Não foi possível conectar, e-mail ou senha inválido.'));
				return $this->redirect()->toRoute('login');
			}
		}
		$view = new ViewModel(array(
				'form' => $form
		));
		return  $view;
		 
	}
	
	public function sairAction()
	{
		$sessao = new Container('Auth');
		$sessao->getManager()->getStorage()->clear();
		return $this->redirect()->toRoute('login');
	}	
	
	public function registerAction()
	{
		$registerForm = new RegisterForm();
		$this->layout('layout/layoutEmpty');
		$view = new ViewModel(array('form'=>$registerForm));
		
		return $view;
	}
	
	public function createAction() {
		$form = new LoginForm ();
		$form->get('submit')->setValue('Cadastrar');
		
		$request = $this->getRequest();
		$senha = $request->getPost('senha');
		$email = $request->getPost('email');
		$nome = $request->getPost('email');
		$confirmaSenha = $request->getPost('confirmaSenha');
		
		$confirmaSenha = new Identical($confirmaSenha);
		if (!$confirmaSenha->isValid($senha)) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('A senha digitada no campo de confirmação é diferente do campo senha.'));
			return $this->redirect()->toRoute('register');
		}		
		$sql = new Sql( $this->getLoginTable()->adapter ) ;
		$select = $sql->select();
		$select -> from($this->getLoginTable()->getTable());
		$select->where(array('email' => $email));
		$result = $this->getLoginTable()->selectWith($select);
		if(count($result) == 0)
		{
			if ($request->isPost()) {
				$form->setData($request->getPost());
				
				$senha = md5(md5($senha));
				
				if ($form->isValid ()) {
					
					$this->getLoginTable()->insert( array(
						'email' => $email,
						'senha' => $senha,
						'nome' => $nome	
					));
				}
			}
			$this->flashMessenger()->addMessage('Cadastrado com sucesso');
			return $this->redirect()->toRoute('login');
				
		} else {
			$this->flashMessenger()->addErrorMessage(utf8_encode('E-mail já cadastrado, tente novamente com outro e-mail.'));
			return $this->redirect()->toRoute('register');
		}
	}	
	
	public function getLoginTable()
	{
		if (!$this->LoginTable) {
			$this->LoginTable = new TableGateway(
					'usuario',
					$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->LoginTable;
	}	
}
