<?php
class Pagina{
	static function acesso(){
		$data = [
			'action'=>'Pagina/acesso',
			'msg'=>'Empty!',
			'result'=>false,
			'url'=>isset($_REQUEST['url'])?$_REQUEST['url']:'',
			'codigo'=>null
		
		];
		
		$dbh = conect();
		$sth = $dbh->prepare('
			insert pagina set
			url = :url,
			acesso = now(),
			ip = :ip
		');
		
		$sth->execute([
			'url'=>$data['url'],
			'ip'=>$_SERVER["REMOTE_ADDR"]
		]);
		
        if($sth->errorInfo()[0]!='00000'){
			$data['msg'] = "MySQL Error ({$sth->errorInfo()[1]}): {$sth->errorInfo()[2]}";
			return $data;
		}
		
		$data['result'] = true;
		$data['codigo'] = $dbh->lastInsertid();
		$data['msg'] = 'inserido um novo acesso';
		return $data;
	}
}