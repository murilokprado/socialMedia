<?php

namespace Application\Controller;

use Zend\Db\ResultSet\ResultSet;
use Application\Form\TimelineForm;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class TimelineController extends AbstractActionController {
	
	protected $TimelineTable;
	
	public function indexAction() {
		// TIMEDIFF()
		//DATE_FORMAT(dataMensagem, '%d/%m/%Y - %H:%i:%S')
		$sessao = new Container('Auth');
		$idUsuario = $sessao->idUsuario;
		$db = $this->getTimelineTable()->adapter;
		$sql = "select usuario.codUsuario, mensagem.descricaoMensagem mensagemAm, usuario.nome nomeAmigo, mensagem.codUsuario as codigoUsuario, dataMensagem as data from amigo
				inner join mensagem on (amigo.idSolicitante = mensagem.codUsuario || amigo.idSolicitado = mensagem.codUsuario)
				inner join usuario on ((amigo.idSolicitante = usuario.codUsuario || amigo.idSolicitado = usuario.codUsuario ) && mensagem.codUsuario = usuario.codUsuario) 
				where (amigo.idSolicitado = $idUsuario || amigo.idSolicitante = $idUsuario) && amigo.situacao = 'A' ORDER BY data desc";
		try {
			$statement = $db->query ( $sql );
			$mensagem = $statement->execute();
			
			$mensagens = array();
		} catch (Exception $e) {
			$this->flashMessenger()->addErrorMessage(utf8_encode('Consulta as mensagens não está disponivel no momento.'));
			return $this->redirect()->toRoute('timeline');
		}
		
		if(is_array($mensagem)) {
			try {
				$sql = "select usuario.codUsuario, mensagem.descricaoMensagem mensagemAm, usuario.nome nomeAmigo, mensagem.codUsuario as codigoUsuario, dataMensagem as data
						 from mensagem inner join usuario on (mensagem.codUsuario = usuario.codUsuario) where mensagem.codUsuario = $idUsuario";
				$statement = $db->query ( $sql );
				$mensagem = $statement->execute();
			} catch (Exception $e) {
				$this->flashMessenger()->addErrorMessage(utf8_encode('Consulta as mensagens não está disponivel no momento.'));
				return $this->redirect()->toRoute('timeline');
			}
		}
		
		foreach ($mensagem as $open) {
			$data = $this->tempoData(date('d/m/Y H:i:s', strtotime($open['data'])), date("d/m/Y H:i:s"));
			array_push($mensagens, array('codigoUsuario'=>$open['codigoUsuario'],'mensagemAm'=>$open['mensagemAm'],'data'=>$data,'nomeAmigo'=>$open['nomeAmigo']));
		}
		
		$timelineForm = new TimelineForm();
		
		$view = new ViewModel ( array (
				'form' => $timelineForm,
				'mensagem' => $mensagens
		) );
		return $view;		
	}
	
	public function criamensagemAction() {
		$timelineForm = new TimelineForm();
		$timelineForm->get('submit')->setValue('Cadastrar');
		$sessao = new Container('Auth');
		$idUsuario = $sessao->idUsuario;
		
		$request = $this->getRequest();
		$mensagem = $request->getPost('mensagem');
		
		$this->getTimelineTable()->insert( array(
				'descricaoMensagem' => $mensagem,
				'codUsuario' => $idUsuario,
				'dataMensagem' => date("Y-m-d H:i:s")
		));
		
		$this->flashMessenger()->addMessage('mensagem enviada com sucesso');
		return $this->redirect()->toRoute('timeline');
		
		
		$view = new ViewModel ( array (
				'form' => $timelineForm,
				'mensagem' => $mensagem
		) );
		return $view;		
	}	
	
	
	public function getTimelineTable()
	{
		if (!$this->TimelineTable) {
			$this->TimelineTable = new TableGateway(
					'mensagem',
					$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->TimelineTable;
	}
	
	public function tempoData($dataini, $datafim) {
	
		// Split para dia, mes, ano, hora, minuto e segundo da data inicial
		$_split_datehour = explode(' ',$dataini);
		$_split_data = explode("/", $_split_datehour[0]);
		$_split_hour = explode(":", $_split_datehour[1]);
		// Coloquei o parse (integer) caso o timestamp nao tenha os segundos, dai ele fica como 0
		$dtini = mktime ($_split_hour[0], $_split_hour[1], (integer)$_split_hour[2], $_split_data[1], $_split_data[0], $_split_data[2]);
	
		// Split para dia, mes, ano, hora, minuto e segundo da data final
		$_split_datehour = explode(' ',$datafim);
		$_split_data = explode("/", $_split_datehour[0]);
		$_split_hour = explode(":", $_split_datehour[1]);
		$dtfim = mktime ($_split_hour[0], $_split_hour[1], (integer)$_split_hour[2], $_split_data[1], $_split_data[0], $_split_data[2]);
	
		// Diminui a datafim que é a maior com a dataini
		$time = ($dtfim - $dtini);
	
		// Recupera os dias
		$days  = floor($time/86400);
		// Recupera as horas
		$hours = floor(($time-($days*86400))/3600);
		// Recupera os minutos
		$mins  = floor(($time-($days*86400)-($hours*3600))/60);
		// Recupera os segundos
		$secs  = floor($time-($days*86400)-($hours*3600)-($mins*60));
	
		// Monta o retorno no formato
		// 5d 10h 15m 20s
		// somente se os itens forem maior que zero
		$retorno  = "";
		$retorno .= ($days>0)  ?  $days .'d ' : ""  ;
		$retorno .= ($hours>0) ?  $hours .'h ': ""  ;
		$retorno .= ($mins>0)  ?  $mins .'m ' : ""  ;
		$retorno .= ($secs>0)  ?  $secs .'s ' : ""  ;
	
		return $retorno . " atr&aacute;s";
	
	}
}
