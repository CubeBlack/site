<?php
class Sessao{
	static function lista(){
		//Organizar valores a serem usados e retornados
		$data = [
			'acao'=>'Sessao/lista', //Identificação da ação
			'msg'=>'Empty!', //mensagem que pode ser usada intenamente ou mostrada para o usuario final
			'return'=>false,
			'lista'=>[] // a lista a ser retornada
			
		];

		$sql = 'SELECT * FROM usuario_sessao';
		
		$dbh = conect();
		$sth = $dbh->prepare($sql);
		$sth->execute();
		
		$lista = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		//Verificar se ouve erro no MySQL
		if($sth->errorInfo()[1]!=0) {
			$data['msg'] = 'MySQL error '.$sth->errorInfo()[1].': '.$sth->errorInfo()[2];
			return $data;
		}
		
		$data['lista'] = $lista;
		$data['return'] = true;
		return $data;
	}

	static function _encerrar(&$data){
		//Parametros
		Core::dataInit($data);
		Core::parametroObrigatorio($data, 'token');
		if(!Core::temParametros($data)){
			return $data;
		}

		//Pegar sesssao
		$data['detalhe'] = Sessao::detalhe($data['token']);
		if(!$data['detalhe']){
			$data['msg'] = 'Sessao não encontrada';
			return $data;
		}

		//Atualizar o fium da sessao
		$dbh = Core::Conect();
		$sth = $dbh->prepare("UPDATE
			usuario_sessao set
			fim = now()
		");

		$sth->execute();

		Core::bdError($sth, 'Usuario/Sessao::encerar()');

		//Concluir
		$data['msg'] = 'Sessao Encerrrada.';
		return $data;
	}	

	static function adicionar($usuario, $senha, $aplicacao=0){
		$dbh = Core::conect();
		$sth = $dbh->prepare("SELECT codigo, nick, email from usuario
			WHERE senha = :senha
			and (
				   (nick   = :nick) 
				or (email  = :email)
				or (codigo = :codigo)
			)
		");
		
		$sth->execute([
			"nick"=>  $usuario,
			"email"=> $usuario,
			"codigo"=>$usuario,
			"senha"=> $senha
		]);
		
		//Verificar se ouve erro no MySQL
		if($sth->errorInfo()[1]!=0) 
			die("usuario/Sesao::adicioanr() MySQL error {$sth->errorInfo()[1]}: {$sth->errorInfo()[2]}");
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		if(empty($result)){
			return;
		}

		//Pegar o codigo do usuario e subistituir o parametro
		$usuario = $result[0]['codigo'];
		
		//Criar a chave da sessao
		$chave = uniqid($aplicacao.'_'.rand(111,999).'_');
				
		//Salavar sessao no banco
		$dbh = Core::conect();
		$sth = $dbh->prepare("INSERT `usuario_sessao` SET 
			inicio = now(),
			/*fim = now(),*/
			usuario = :usuario ,
			ip = :ip ,
			chave = :chave
		");

		$sth->execute([
			"usuario"=>$usuario,
			"chave"=>$chave,
			"ip"=>$_SERVER["REMOTE_ADDR"]
		]);
	
		//Verificar se ouve erro no MySQL
		if($sth->errorInfo()[1]!=0) 
			die("usuario/Sesao::adicioanr() MySQL error {$sth->errorInfo()[1]}: {$sth->errorInfo()[2]}");
		

		return $chave;
	}
	static function _adicionar(&$data){
		//Verificar parametros
		$data = Core::dataInit($data); //iniciar $data
		$data = Core::parametroOpcional($data, 'aplicacao', 'none');
		$data = Core::parametroObrigatorio($data, 'usuario');
		$data = Core::parametroObrigatorio($data, 'senha');
		$senha = $data['senha'];
		$data['senha'] = '***';

		//Pegar o usuario segundo as credenciais
		$dbh = Core::conect();
		$sth = $dbh->prepare("SELECT codigo, nick, email from usuario
			WHERE senha = :senha
			and (
				   (nick   = :nick) 
				or (email  = :email)
				or (codigo = :codigo)
			)
		");
		
		$sth->execute([
			"nick"=>  $data['usuario'],
			"email"=> $data['usuario'],
			"codigo"=>$data['usuario'],
			"senha"=> $senha
		]);
		
		//Verificar se ouve erro no MySQL
		//var_dump($sth->errorInfo());
		if($sth->errorInfo()[1]!=0) {
			$data['msg'] = 'MySQL error '.$sth->errorInfo()[1].': '.$sth->errorInfo()[2];
			return $data;
		}
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		if(empty($result)){
			$data['msg'] = 'Não foi possivel efetuar login, tente novamente.';
			return $data;
		}
		
		$data['usuario'] = $result[0];

		//
		$data['chave'] = uniqid($data['aplicacao'].'_'.rand(111,999).'_');
				
		//Inserir valores
		$sql  = 'insert `usuario_sessao` set ';
		$sql .= 'inicio = now(), ';
		$sql .= 'fim = now(), ';
		$sql .= 'usuario = :usuario , ';
		$sql .= 'ip = :ip , ';
		$sql .= 'chave = :chave ';
		
		$dbh = Core::conect();
		$sth = $dbh->prepare($sql);
		$sth->execute([
			"usuario"=>$data['usuario']['codigo'],
			"chave"=>$data['chave'],
			"ip"=>$_SERVER["REMOTE_ADDR"]
		]);
	
		//Verificar se ouve erro no MySQL
		if($sth->errorInfo()[1]!=0) {
			$data['msg'] = 'MySQL error '.$sth->errorInfo()[1].': '.$sth->errorInfo()[2];
			return $data;
		}

		//Postar na URL da da aplicacao

		//Concluir		
		$data['result'] = true;
		$data['msg'] = 'Sessao iniciada com sucesso'; //Pode ser usado como retorno de um formlario
		return $data;
	}
	
	static function detalhe($chave){
		$dbh = Core::conect();
		$sth = $dbh->prepare("SELECT
				usuario_sessao.*,
				usuario.nick as usuario_nick,
				usuario.nome as usuario_nome,
				usuario.email as usuario_email,
				usuario.xp as usuario_xp
			from usuario_sessao 
			inner join usuario on usuario_sessao.usuario = usuario.codigo
			where 
				usuario_sessao.chave = :chave		
				and (
						usuario_sessao.fim  > now() 
						or usuario_sessao.fim is null
					)		
		");
		$sth->execute(["chave"=>$chave]);
	
		if($sth->errorInfo()[1]!=0) 
			die("usuario/Sesao::adicioanr() MySQL error {$sth->errorInfo()[1]}: {$sth->errorInfo()[2]}");
		
		
		$resposta = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(empty($resposta)) return;
		
		//junta as arrays
		return $resposta[0];
	}
	
	static function r_detalhe($chave){
		$dbh = conect();
		$sth = $dbh->prepare('SELECT
				usuario_sessao.*,
				usuario.tipo
		  	from usuario_sessao
			inner join usuario on
				usuario.codigo = usuario_sessao.usuario
			where chave = :chave
			limit 1
		');
		
		$sth->execute(["chave"=>$chave]);
	
		//Verificar se ouve erro no MySQL
		if($sth->errorInfo()[1]!=0) {
			die('Usuariosessao/r_detalhe: MySQL error '.$sth->errorInfo()[1].': '.$sth->errorInfo()[2]);
		}
		$retorno = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(empty($retorno)) return false;
		return $retorno[0];
	}
	
	static function apagar(){ //deveria ser finalizar
		
	}
	
	static function atualizar(){ //atualizar
		
	} 

	static function r_existeByCodigo($codigo){
		
	}
	static function sair($data){
		return $data;
	}
}
