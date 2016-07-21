<?php

namespace Application\Controller;

use Application\Form\FriendForm;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Sql;
use Zend\Http\PhpEnvironment\Request;
use Zend\Db\Sql\Predicate\Like;

class FriendController extends AbstractActionController {
	protected $FriendTable;
	protected $UserTable;
	
	public function indexAction() {
		// pesquisa todos os usuario pegando por filtro o passado por post name="searchPer"
		$request = $this->getRequest();
		$searchPer = $request->getPost('searchPer');
		$sessao = new Container('Auth');
		$idUsuario = $sessao->idUsuario;
		
		try {
			$sql = new Sql( $this->getUserTable()->adapter ) ;
			$select = $sql->select();
			$select -> from($this->getUserTable()->getTable());
			$select -> join('status', 'usuario.codStatus = status.codStatus',array('descricaoStatus' => 'descricaoStatus'));
			$select ->where->notIn("usuario.codUsuario", array($idUsuario));
			$select->where->like('nome', "%". $searchPer . "%");
			$select->order('nome');
			
			$result = $this->getUserTable()->selectWith($select);
			$amigos = array();
			foreach($result as $r) {
				
				$sql = new Sql( $this->getFriendTable()->adapter ) ;
				$select = $sql->select();
				$select -> from($this->getFriendTable()->getTable());
				$select ->where->NEST->equalTo("idSolicitado", $idUsuario)->AND->equalTo("idSolicitante", $r['codUsuario'])->OR->equalTo("idSolicitante", $idUsuario)->AND->equalTo("idSolicitado", $r['codUsuario']);
				$resultF = $this->getFriendTable()->selectWith($select);				
				$row = $resultF->current();
				
				if($row['situacao'] != null && $row['situacao'] != '')
					$situ = $row['situacao'];
				else 
					$situ = false;
				array_push($amigos, array('codUsuario'=>$r['codUsuario'],'email'=>$r['email'],'descricaoStatus'=>$r['descricaoStatus'],'nome'=>$r['nome'],'situacao'=>$situ, 'idSolicitado' => $row['idSolicitado'], 'codAmigo' => $row['codAmigo']));
			}
		} catch(Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('A pesquisa de pessoas não está disponivel no momento.'));
			return $this->redirect()->toRoute('timeline');
		}
		$friendForm = new FriendForm();
		//var_dump($result);
		$view = new ViewModel ( array (
				'form' => $friendForm,
				'pessoas' => $amigos,
		) );
		return $view;		
	}

	public function friendsAction() {
		$sessao = new Container('Auth');
		$idUsuario = $sessao->idUsuario;
	
		try {
			$sql = new Sql( $this->getUserTable()->adapter ) ;
			$select = $sql->select();
			$select -> from($this->getUserTable()->getTable());
			$select -> join('status', 'usuario.codStatus = status.codStatus',array('descricaoStatus' => 'descricaoStatus'));
			$select -> join('amigo', 'usuario.codUsuario = amigo.idSolicitante OR usuario.codUsuario = amigo.idSolicitado',array('situacao' => 'situacao', 'codAmigo' => 'codAmigo', 'idSolicitado' => 'idSolicitado'));
			$select ->where->NEST->equalTo("situacao", "A")->OR->equalTo("situacao", "P");
			$select ->where->notIn("usuario.codUsuario", array($idUsuario));
			$select ->where->NEST->equalTo("idSolicitado", $idUsuario)->OR->equalTo("idSolicitante", $idUsuario);
			$select->order('nome');
			$result = $this->getUserTable()->selectWith($select);
		} catch(Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('A visualização de amigos não está disponivel no momento.'));
			return $this->redirect()->toRoute('timeline');
		}
		
		$friendForm = new FriendForm();
		$view = new ViewModel ( array (
				'form' => $friendForm,
				'pessoas' => $result,
		) );
		
		return $view->setTemplate("application/friend/index.phtml");
	}
	
	public function addAction() {
		
		$sessao = new Container('Auth');
		$idUsuario = $sessao->idUsuario;
		$idSolicitado = $this->params('id');
		$situacao = 'P';
		
		try {
			$this->getFriendTable()->insert( array(
					'idSolicitante' => $idUsuario,
					'idSolicitado' => $idSolicitado,
					'dataSolicitacao' => date("Y-m-d H:i:s"),
					'situacao' => $situacao,
			));
			
			$this->flashMessenger()->addSuccessMessage('Pedido de Amizade enviado com sucesso');
		} catch (\Exception $e) {
			$this->flashMessenger()->addErrorMessage('Pedido de Amizade não enviado, tente novamente');
		}
		
		return $this->redirect()->toRoute('person');
	}
	
	public function addpendAction() {
		$idAmigo = $this->params('id');
		$situacao = 'A';
		
		try {
			$data = array('situacao' => $situacao, 'dataConfirmacao' => date("Y-m-d H:i:s"));
			$where = array('codAmigo = ?' => $idAmigo);
			$this->getFriendTable()->update($data, $where);
			
			$this->flashMessenger()->addSuccessMessage('Amigo adicionado com sucesso.');
		} catch (\Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('Não foi possível adicionar este amigo, por favor tente mais tarde.'));
		}
	
		return $this->redirect()->toRoute('person');
	}	

	public function addagainAction() {
		$idAmigo = $this->params('id');
		$situacao = 'P';
	
		try {
			$data = array('situacao' => $situacao, 'dataSolicitacao' => date("Y-m-d H:i:s"));
			$where = array('codAmigo = ?' => $idAmigo);
			$this->getFriendTable()->update($data, $where);
			$this->flashMessenger()->addSuccessMessage('Pedido de amizade enviado com sucesso.');
		} catch (\Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('Não foi possível enviar o pedido de amizade, por favor tente mais tarde.'));
		}
		return $this->redirect()->toRoute('person');
	}

	public function recAction() {
		$idAmigo = $this->params('id');
		$situacao = 'R';
		try {
			$data = array('situacao' => $situacao, 'dataConfirmacao' => date("Y-m-d H:i:s"));
			$where = array('codAmigo = ?' => $idAmigo);
			$this->getFriendTable()->update($data, $where);
			$this->flashMessenger()->addMessage('Pedido de amizade recusado.');
		} catch (\Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('Não foi possível recusar no momento, por favor tente mais tarde.'));
		}
		return $this->redirect()->toRoute('person');
	}

	public function removeAction() {
		$idAmigo = $this->params('id');
		try {
			$where = array('codAmigo = ?' => $idAmigo);
			$this->getFriendTable()->delete($where);
			$this->flashMessenger()->addMessage('Amizade desfeita.');
		} catch (\Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('Não foi possível excluir, tente novamente mais tarde.'));
		}
		return $this->redirect()->toRoute('person');
	}
	
	public function getUserTable() {
		if (!$this->UserTable) {
			$this->UserTable = new TableGateway(
					'usuario',
					$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->UserTable;
	}
	
	public function getFriendTable() {
		if (!$this->FriendTable) {
			$this->FriendTable = new TableGateway(
					'amigo',
					$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->FriendTable;
	}
}
