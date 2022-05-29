<?php
/*
*
* G2 - Daniel A. Lima
*/
class Usuario{
    static function lista(){
		$sql = 'select codigo, nome, email, tipo from usuario;';
		$dbh = conect();
		$sth = $dbh->prepare($sql);
		//$sth->bindParam(':codigo', $sUser['id']);		
		$sth->execute();
		$lista = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		//Organizar retorno
		return [
			'count'=>count($lista),
			'check'=>md5(serialize($lista)),
			'lista'=>$lista
		];
    }
	
	static function me(){
		//var_dump($_REQUEST); die('começo de Usuario/me');
		
		if(!isset($_REQUEST['token'])) return [
			"value"=>false,
			"msg" => "Usuario/me: Token invalido"
		];

		if(!isset($_REQUEST['app'])) return [
			"value"=>false,
			"msg" => "Usuario/me: App invalido"
		];

		//Pegar dados do usuario
		$sql = "SELECT 
				usuario.codigo, 
				usuario.nick, 
				usuario.nivel 
			FROM usuario_sessao 
			INNER JOIN usuario on usuario.codigo = usuario_sessao.usuario			
			WHERE 
				app = :app AND
				token = :token
			";
		
		$dbh = conect();
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':app', $_REQUEST['app']);		
		$sth->bindParam(':token', $_REQUEST['token']);		
		$sth->execute();

		//var_dump($sth->errorInfo()); die();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		//Retornar nova sessao anonima
		if(empty($result)){
			$token = 'a:'. md5(rand(0,1000).rand(0,1000).rand(0,1000));
			$sql = "INSERT usuario_sessao SET 
			`usuario` = 0, 
			`token` = :token, 
			`app` = :app, 
			`ip` = :ip
			";
			$sth = $dbh->prepare($sql);
			
			$sth->bindParam(':app', $params["app"]);
			$sth->bindParam(':token', $token);
			$sth->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
			
			$sth->execute();
			
			return [
				"value"=>true,
				"anonimo"=>true,
				"msg" => "Usuario/me: Nova sessao anonima",
				"nick"=>"anonimo",
				"nivel"=>"0"
					
			];
		}
		
		$me = $result[0];
		$me['value'] = true;
		
		return $me;
	}
	
	static function logar(){
		$params = $_REQUEST;
		//verificar se existe os parametros
		if(!isset($params['usuario'])||!isset($params['senha']))return [
			"value"=>false,
			"msg"=>'Valores insuficientes'
		];
		
		//Procurar usuario
		$dbh = conect();
		$sql = "SELECT 
				codigo 
			FROM usuario 
			WHERE 
				(nick = :nick OR email =  :email) and 
				senha = :senha";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':nick', $params["usuario"]);
		$sth->bindParam(':email', $params["email"]);
		$passMd5 = md5($params["senha"]);
		$sth->bindParam(':senha', $passMd5);
		$sth->execute();
		$user = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		
		//Caso falhe o loguin
		if(empty($user)) return[
			"value"=>false,
			"msg"=>'Não foi posivel efetuar login. Verifique suas credenciais'
		];
		$usuarioCodigo = $user[0]['codigo'];
		
		//Criar token
		$token = md5(rand(0,1000).rand(0,1000).rand(0,1000));
		
		//Sair da sessão anterior(porr enquanto, so web)
		if($_REQUEST['app'] == 'web'){
			$sql = "DELETE FROM usuario_sessao WHERE app = :app";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':app', $params["app"]);
			$sth->execute();
		}

		//Inerir nova sessão
		$sql = "INSERT usuario_sessao SET 
			`usuario` = :usuario, 
			`token` = :token, 
			`app` = :app, 
			`ip` = :ip
			";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':usuario', $usuarioCodigo);
		$sth->bindParam(':app', $params["app"]);
		$sth->bindParam(':token', $token);
		$sth->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$sth->execute();
		
		//Salvar a seção
		return[
			"value"=>true,
			"msg"=>'Efetuado login com sucesso',
			'token'=>$token
		];
	}
	
	static function sair($params){
		//$_SESSION = array();
	}

	static function setXp(&$data){
        //Tratar paramentros
        Core::dataInit($data);
        Core::parametroObrigatorio($data, 'usuario');
        Core::parametroObrigatorio($data, 'xp');
        if(!Core::temParametros($data)) return;

        $dbh = Core::conect();
        $sth = $dbh->prepare('UPDATE usuario set
                xp = xp + :ixp
            where codigo = :codigo
        ');
        
        $sth->execute([
            "codigo"=>$data['usuario'],
            "ixp"=>$data['xp']
        ]);

        Core::bdError($sth, "Usuario::setXp({$data['usuario']},{$data['xp']})");
	}
}
