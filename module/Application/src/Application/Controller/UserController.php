<?php

namespace Application\Controller;

use Application\Form\UserForm;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Sql;
use Zend\Http\PhpEnvironment\Request;
use Zend\Validator\File\Size;
use Zend\XmlRpc\Value\String;

class UserController extends AbstractActionController {
	
	protected $UserTable;
	protected $StatusTable;
	
	public function indexAction() {
		$sql = new Sql( $this->getStatusTable()->adapter ) ;
		$select = $sql->select();
		$select -> from($this->getStatusTable()->getTable());
		$result = $this->getStatusTable()->selectWith($select);
		$selectData = array();
		foreach ($result as $res) {
			$selectData[$res['codStatus']] = $res['descricaoStatus'];
		}
		
		$sessao = new Container('Auth');
		$idUsuario = $sessao->idUsuario;
		$dados = $this->getUserTable()->select(array('codUsuario'=>$idUsuario));
		$row = $dados->current();
		$userForm = new UserForm(null, $selectData, $row['codStatus']);
		$userForm->prepare();
		$userForm->setData($row);
		
		$view = new ViewModel ( array (
				'form' => $userForm,
		) );
		return $view;		
	}
	
	public function updateAction() {
		$request = new Request();
		if ($request->isPost ()) {
			$data = $request->getFiles();
			$data2 = $request->getPost();
			$sessao = new Container('Auth');
			$idUsuario = $sessao->idUsuario;
			$msg = "";
			
			if(!empty($data2)) {
				try {
					$dados = $this->getUserTable()->select(array('codUsuario'=>$idUsuario));
					$row = $dados->current();
					if($row != 0)
					{
						if($row['nome'] != $data2['nome'] && $row['codStatus'] != $data2['status']) {
							$sql = $this->getUserTable()->update(array('nome'=>$data2['nome'],'codStatus'=>$data2['status']),array('codUsuario'=>$idUsuario));
							$sessao->nome = $data2['nome'];
						} else if($row['nome'] != $data2['nome']) {
							$sql = $this->getUserTable()->update(array('nome'=>$data2['nome']),array('codUsuario'=>$idUsuario));
							$sessao->nome = $data2['nome'];
						} else if($row['codStatus'] != $data2['status']) {
							$sql = $this->getUserTable()->update(array('codStatus'=>$data2['status']),array('codUsuario'=>$idUsuario));
						}
						if(!empty($sql)) {
							$msg = "Usuário alterado, ";
						}
					}
				} catch(Exception $e) {
					$msg = "Não foi possível alterar o usuário, ";
				}
			}
				
			if(!empty($data['file']['name']) && ($data['file']['type'] == 'image/jpeg')) {
				$filter = new \Zend\Filter\File\Rename(array(
						"target"    => "./public/img/fotoperfil_".$idUsuario.".jpg",
						"overwrite" => true,
				));
				$filter->filter($data['file']);

				$this->flashMessenger()->addSuccessMessage(utf8_encode($msg.' Imagem alterada com sucesso.'));
				return $this->redirect()->toRoute('timeline');
			} else if(!empty($data['file']['name'])){
				$this->flashMessenger()->addErrorMessage(utf8_encode($msg . ' A extensão da imagem deve ser JPG.'));
				return $this->redirect()->toRoute('user');
			} elseif(!empty($msg)&& $msg != "") {
				$this->flashMessenger()->addMessage(utf8_encode(substr($msg, 0, -2)));
				return $this->redirect()->toRoute('timeline');
			}
			
			return $this->redirect()->toRoute('user');
		}
		return $this->redirect()->toRoute('timeline');
	}
	
	public function getUserTable() {
		if (!$this->UserTable) {
			$this->UserTable = new TableGateway(
					'usuario',
					$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->UserTable;
	}
	
	public function getStatusTable() {
		if (!$this->StatusTable) {
			$this->StatusTable = new TableGateway(
					'status',
					$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->StatusTable;
	}
}
