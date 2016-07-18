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
		//echo $searchPer;
		$sessao = new Container('Auth');
		$idUsuario = $sessao->idUsuario;
		
		try {
			//$consulta = "SELECT idSolicitante FROM amigo WHERE amigo.idSolicitado = $idUsuario OR amigo.idSolicitante = $idUsuario 
				//	AND amigo.situacao != 'A'" ;
			$amigo = "A";
			$sql = new Sql( $this->getFriendTable()->adapter ) ;
			$select = $sql->select();
			$select -> from($this->getFriendTable()->getTable());
			$select ->where->equalTo("idSolicitado", $idUsuario);
			$select ->where->OR->equalTo("idSolicitante", $idUsuario);
			$select ->where->equalTo("situacao", $amigo);
			$resultF = $this->getFriendTable()->selectWith($select);
			$resultFri = array();
				
			foreach($resultF as $r) {
				echo "SOLICITADO: " . $r['idSolicitado'] . " SOLICITANTE: " . $r['idSolicitante'];
				if(!in_array($r['idSolicitante'], $resultFri)) {
					echo '<br> ENTREI </br>';
					array_push($resultFri, $r['idSolicitante']);
				} 
				if (!in_array($r['idSolicitado'], $resultFri)) {
					echo '<br> ENTREI 2</br>';
					array_push($resultFri, $r['idSolicitado']);
				}
			}
			
			if(!in_array($idUsuario, $resultFri)) {
				array_push($resultFri, $idUsuario);
			}
			
			$sql = new Sql( $this->getUserTable()->adapter ) ;
			$select = $sql->select();
			$select -> from($this->getUserTable()->getTable());
			$select -> join('status', 'usuario.codStatus = status.codStatus',array('descricaoStatus' => 'descricaoStatus'));
			$select ->where->notIn("usuario.codUsuario", $resultFri);
			$select->where->like('nome', "%". $searchPer . "%");
			$select->order('nome');
			
			$ww = $select->getSqlString();
			echo "$ww\n";			
			
			$result = $this->getUserTable()->selectWith($select);
			
		} catch(Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('A pesquisa de pessoas não está disponivel no momento.'));
			return $this->redirect()->toRoute('timeline');
		}
		$friendForm = new FriendForm();
		//var_dump($result);
		$view = new ViewModel ( array (
				'form' => $friendForm,
				'pessoas' => $result,
		) );
		return $view;		
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
