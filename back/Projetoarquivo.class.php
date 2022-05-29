<?php
class Projetoarquivo{
	const upload_dir = "etc/projetoarquivo/";
	static function detalhe(){
		$data =[
			'acao'=>'Projetoarquivo/detalhe',
			'result'=>false,
			'msg'=>'Empty!',
			'codigo'=>isset($_REQUEST['codigo'])?$_REQUEST['codigo']:''
		];
		
		if($data['codigo'] == ''){
			$data['msg']='Codigo Invalido';
			return $data;
		}
		
        $dbh = conect();
        $sth = $dbh->prepare("SELECT * FROM projeto_arquivo where codigo = :codigo");
        $sth->execute(["codigo"=>$data['codigo']]);
       
	   if($sth->errorInfo()[0]!='00000'){
			$data['msg'] = "MySQL Error ({$sth->errorInfo()[1]}): {$sth->errorInfo()[2]}";
			return $data;
		}
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(empty($result)){
			$data['msg'] = 'Arquivo não encontrado';
			return $data;
		}
		
		$data = array_merge($data, $result[0]);
		$data['msg'] = 'Detalhe do aquivo';
		$data['result'] = true;
		
		return $data;
	}
    static function lista(){
		$data = [
			"projeto" =>(isset($_REQUEST["projeto"]))?$_REQUEST["projeto"]:"",
			"result" => false,
			"projeto_versao" => (isset($_REQUEST["projeto_versao"]))?$_REQUEST["projeto_versao"]:"",
			"acao" => "projetoarquivo/lista"
		];
		
		$paramentros =  array();
        $sql = "SELECT * FROM projeto_arquivo
			where projeto_versao = :projeto_versao
		";
		
		$paramentros[':projeto_versao'] = $data["projeto_versao"];

		if($data["projeto_versao"] != ''){
			$sql .= ' and projeto_versao = :projeto_versao ';
			$paramentros[':projeto_versao'] = $data["projeto_versao"];
		}

        $dbh = conect();
        $sth = $dbh->prepare($sql);
        $sth->execute($paramentros);
        
		//Verificar se ouve erro mysql
        if($sth->errorInfo()[0]!='00000'){
			$data['msg'] = "MySQL Error ({$sth->errorInfo()[1]}): {$sth->errorInfo()[2]}";
			return $data;
		}
        
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$data['result'] = true;
		$data['lista'] = $result;
		
		return $data;
    }

	static function atualizar(){
		$data = [
			'valor'=>false,
			'acao'=>'projetoarquivo/atualizar',
			'msg'=>'Enpty',
			'codigo'=>isset($_REQUEST['codigo'])?$_REQUEST['codigo']:'',
			'projeto'=>isset($_REQUEST['projeto'])?$_REQUEST['projeto']:'',
			'caminho'=>isset($_REQUEST['caminho'])?$_REQUEST['caminho']:'',
			'checksum'=>isset($_REQUEST['checksum'])?$_REQUEST['checksum']:'',
			'projeto_versao'=>0,
			'status'=>'Empty!'
		];
		
		if($data['codigo']==''){
			$data['msg']='Condigo Invalido';
			return $data;
		}
		
		//Pegar o arquivo
		$dbh = conect();
		$sth = $dbh->prepare("
			SELECT * FROM projeto_arquivo
			WHERE codigo = :codigo
		");
		
		$sth->execute([
			":codigo"=>$data['codigo']
		]);
		
		if($dbh->errorInfo()[0] != '00000'){
			$data['msg'] = "MySQL error({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";;
			return $data;
		}
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		if(empty($result)){
			$data['msg'] = 'Arquivo não encontrado';
			return $data;
		}
		$arquivo = $result[0];
		
		//pegar verssao do projeto
		if($arquivo['projeto_versao'] == '0'){
			if($data['projeto'] == ''){
				$data['msg'] = 'Projeto invalido';
				return $data;
			}
			
			if($data['checksum'] == ''){
				$data['msg'] = 'Checksum invalido';
				return $data;
			}
			
			//Pegar dados do projeto
			$dbh = conect();
			$sth = $dbh->prepare("
				SELECT * FROM projeto
				WHERE codigo = :codigo
			");
			
			$sth->execute([
				":codigo"=>$data['projeto']
			]);
			
			if($dbh->errorInfo()[0] != '00000'){
				$data['msg'] = "MySQL error({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";;
				return $data;
			}
			
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			if(empty($result)){
				$data['msg'] = 'Projeto não encontrado';
				return $data;
			}
			$projeto = $result[0];
			$data['projeto_versao'] = $projeto['versao'];
			
			//Verificar arquivo
			$diretorio = Projetoarquivo::upload_dir.$data['codigo'];
			$md5 = md5_file($diretorio);
			if($md5 =! $data['checksum']){
				$data['status']='Arquivo corrompido';
			}
		}
		
		//Atualizar		
		$sqlBase = "UPDATE projeto_arquivo SET \n";
		$sqlValores = '';
		$parametros = [];
		
		if($data['caminho']!=''){
			$sqlValores .= "caminho = :caminho ";
			$parametros[':caminho'] = $data['caminho'];
		}
		
		if($data['projeto']!=''){
			if($sqlValores != '')$sqlValores .= ", \n";
			$sqlValores .= "projeto = :projeto";
			$parametros[':projeto'] = $data['projeto'];
		}
		
		if($data['projeto_versao']!=''){
			if($sqlValores != '')$sqlValores .= ", \n";
			$sqlValores .= "projeto_versao = :projeto_versao";
			$parametros[':projeto_versao'] = $data['projeto_versao'];
		}
		if($data['checksum']!=''){
			if($sqlValores != '')$sqlValores .= ", \n";
			$sqlValores .= "checksum = :checksum, \n";
			$parametros[':checksum'] = $data['checksum'];
		}
				
		if($sqlValores == ''){
			$data['msg']='Valores insuficientes para atualizar';
			return $data;
		}
		
		$sqlValores .="status = :status \n";
		$parametros[':status'] = $data['status'];
				
		$sqlFiltro = "\nWHERE codigo = :codigo";
		$parametros['codigo'] = $data['codigo'];
			
		$dbh = conect();
        $sth = $dbh->prepare($sqlBase.$sqlValores.$sqlFiltro);
		//echo $sqlBase.$sqlValores.$sqlFiltro;
		//exit;
		$sth->execute($parametros);
		//echo $sqlBase.$sqlValores.$sqlFiltro;

		
		if($dbh->errorInfo()[0] != '00000'){
			$data['msg'] = "MySQL error({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";;
			return $data;
		}
		
		$data['msg']='Atualizado com sucesso';
		$data['valor']=true;
		return $data;
	}
		
	static function upload(){
		$data = [
			'valor'=>false,
			'acao'=>'projetoarquivo/upload',
			'msg'=>'Enpty'
		];
		
		
		//Inserir a tabela
		$dbh = conect();
        $sth = $dbh->prepare("
			INSERT INTO projeto_arquivo set codigo = null;
		");
		$sth->execute();
		
		if($dbh->errorInfo()[0]!='00000'){
			$data['msg'] = "MySQL Error({$sth->errorInfo()[1]}): {$sth->errorInfo()[2]}";
			return $data;
		}
		
		//Organizar informação do arquivo
		$codigo = $dbh->lastInsertid();
		$diretorio = Projetoarquivo::upload_dir.$codigo;
		
		//Upload do arquivo
		if(!isset($_FILE['arquivo'])){
			$data['msg'] = "Arquivo não enviado";
			return $data;
		}
		
		if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio)) {
			$data['msg'] = "Não foi posivel concluir o upload";
			return $data;
		}
		$data['valor'] = true;
		$data['msg'] = "Upload Concluido";
		$data['codigo'] = $codigo;
		return $data;

	}
	
	static function download(){
		$data = [
			"valor" => false,
			"acao" => 'Projetoarquivo/download',
			"msg" => 'Empty!',
			"codigo" => isset($_REQUEST['codigo'])?$_REQUEST['codigo']:''
		];
		
		if($data['codigo'] == ''){
			$data['msg'] = 'Codigo invalido';
			return $data;
		}
		
		//Contar a quantidade de dowloads
		
		//verificar se o aqruivo existe
		$diretorio = Projetoarquivo::upload_dir.$data['codigo'];
		//o tempo máximo de execução em 0 para as conexões lentas
		set_time_limit(0);

		if (!file_exists($diretorio)) {
			$data['msg'] = 'O arquivo não exite';
			return $data;
			die();
		}
		// Aqui você pode aumentar o contador de downloads
		// Definimos o novo nome do arquivo
		$novoNome = $data['codigo'];
		// Configuramos os headers que serão enviados para o browser
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="'.$novoNome.'"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($diretorio));
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Expires: 0');
		// Envia o arquivo para o cliente
		readfile($diretorio);	
	
		die();
	}
}
