<?php
class Projeto{
    static function lista(){
		$data = [
			'acao'=>'Projeto/lista',
			'msg'=>'Empty',
			'result'=>null,
			'lista'=>[]
		];
		/*
        $me = Usuario::me();

        if(!$me["value"]) return $me;
		
        if($me['nivel'] < 10) return [
			"value"=>false,
			"msg" => "Você não tem permição para visualizar os projetos"
        ];
		
		*/
		
        $sql = "SELECT 
                *
            FROM projeto 

            ";

        $dbh = conect();
        $sth = $dbh->prepare($sql);

        $sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		$data['lista'] = $result;
		$data['result']	=true;
		$data["check"]=md5(serialize($result));
		
		return $data;
    }
	
    static function detalhe(){
        //Validar parametro
        if(!isset($_REQUEST['codigo'])) return [
            "valor"=>false,
            "msg" => "Projeto/detalhe: Codigo invalido"
        ];

        //altenticar
        //$me = Usuario::me();
        //if(!$me["value"]) return $me;
		/* existe algo que não possa ser visto aqui?
        if($me['nivel'] < 10) return [
			"value"=>false,
			"msg" => "Projeto/get: Você não tem permição para visualizar os projetos"
        ];
		*/

        //Pesquisar
        $sql = "SELECT * FROM projeto WHERE
            codigo = :codigo
        ";

        $dbh = conect();
        $sth = $dbh->prepare($sql);
        $sth->execute([
            ":codigo"=>$_REQUEST['codigo']
        ]);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result))  return [
            "valor"=>false,
            "msg" => "Projeto/detalhe: Projeto desconhecido"
        ];
        $result = $result[0];
        $result['valor'] = true;
        return $result;
    }
	
	static function adicionarvercao(){
		//Tratar dados
		$data = [
			'acao'=>'Projeto/adicionarvercao',
			'valor'=>false,
			'msg'=>'Empty',
			'codigo'=>isset($_REQUEST['codigo'])?$_REQUEST['codigo']:''
		];
		
		//Altenticar usuario
		//Pegar verssao projeto
		$dbh = conect();
        $sth = $dbh->prepare("
			SELECT versao FROM projeto where codigo = :codigo
		");
        $sth->execute([
			'codigo'=>$data['codigo']
		]);
        
        if($sth->errorInfo()[0]!='00000'){
			$data['msg'] = "MySQL Error ({$sth->errorInfo()[1]}): {$sth->errorInfo()[2]}";
			return $data;
		}
        
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(empty($result)){
			$data['msg'] = 'Projeto não encontrado';
			return $data;
		}
		
		$data['versao'] = (int)$result[0]['versao'];
		$data['versao']++;	
		
		
		//Atualizar verssao
		$dbh = conect();
        $sth = $dbh->prepare("
			UPDATE projeto 
			SET versao = :versao
			where codigo = :codigo
		");
        $sth->execute([
			'codigo'=>$data['codigo'],
			'versao'=>$data['versao']
		]);
        
        if($sth->errorInfo()[0]!='00000'){
			$data['msg'] = "MySQL Error ({$sth->errorInfo()[1]}): {$sth->errorInfo()[2]}";
			return $data;
		}
		
		$data['msg'] ='Nova versao adicionada';
		$data['valor'] = true;
		
		return $data;
	}
	
}

