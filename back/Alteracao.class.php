<?php
class Alteracao{
    static function detalhe($data){
        if(!isset($data['codigo'])){
            $data['msg'] = 'Codigo invalido';
            return $data;
        }


        $dbh = conect();
        $sth = $dbh->prepare("
            SELECT * 
            FROM alteracao
            where codigo = :codigo
        ");
        $sth->execute(['codigo'=>$data['codigo']]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result)){
            $data['msg'] = 'Detalhe não encontrado';
            return $data;
        }

        $data['detalhe'] = $result[0];
        $data['msg'] = 'Detalhado';
        $data['result'] = true;
        return $data;
    }

    static function adicionar($data){
  
        if(!isset($data['projeto'])){
            $data['msg'] = 'Projeto invalida';
            return $data;
        }
        
        if(!isset($data['status'])){
            $data['msg'] = 'status invalido';
            return $data;
        }
        
        if(!isset($data['local'])){
            $data['msg'] = 'local invalido';
            return $data;
        }
        
        if(!isset($data['descricao'])){
            $data['msg'] = 'descicao invalida';
            return $data;
        }
        
        if(!isset($data['tipo'])){
            $data['msg'] = 'tipo invalido';
            return $data;
        }

        if(!isset($data['responsavel'])){
            $data['msg'] = 'Responsavel invalido';
            return $data;
        }

        $dbh = conect('documentacao');
        $sth = $dbh->prepare("
            insert into alteracao set
            projeto = :projeto,
            status = :status,
            local = :local,
            descricao = :descricao,
            tipo = :tipo,
            responsavel = :responsavel,
            registro = now()          
            
        ");

        $sth->execute([
            "projeto"=>$data['projeto'],
            "status"=>$data['status'],
            "local"=>$data['local'],
            "descricao"=>$data['descricao'],
            "tipo"=>$data['tipo'],
            "responsavel"=>$data['responsavel']
        ]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $data['codigo'] = $dbh->lastInsertId();
        $data['msg'] = 'Adicioando';
        $data['result'] = true;
        return $data;
    }

    static function lista($data){
    	$data['pesquisa'] = isset($data['pesquisa'])?$data['pesquisa']:"";
    	$data['responsavel'] = isset($data['responsavel'])?$data['responsavel']:"";
    	$data['projeto'] = isset($data['projeto'])?$data['projeto']:"";
    	
        /*
        SELECT
            alteracao.*,
            responsavel.nome as responsavel_nome,
            projeto.nome as projeto_nome
        FROM alteracao 
        left join usuario responsavel on alteracao.responsavel = responsavel.codigo
        left join projeto on alteracao.projeto = projeto.codigo
        where true 
        and alteracao.status in ('pendente') 
        and alteracao.tipo in ('correcao','implementacao','otimizacao')
        order by codigo desc;
        */

    	$sql = 'SELECT
            alteracao.*,
            responsavel.nome as responsavel_nome,
            projeto.nome as projeto_nome
        FROM alteracao 
        left join usuario responsavel on alteracao.responsavel = responsavel.codigo
        left join projeto on alteracao.projeto = projeto.codigo
        where true 
        ';
    	$parametros = [];
    	
        //Seletivo
    	if($data['pesquisa']!=''){
    		$sql .= 'and (alteracao.descricao like :pesquisa ';
            $sql .= 'or alteracao.local like :pesquisa ) ';
    		$parametros["pesquisa"] = "%{$data['pesquisa']}%";
    		
    	}
    	
    	 if($data['responsavel']!=''){
    		$sql .= 'and alteracao.responsavel = :responsavel ';
    		$parametros["responsavel"] = $data['responsavel'];
    		
    	}
    	
    	if($data['projeto']!=''){
    		$sql .= 'and alteracao.projeto = :projeto ';
    		$parametros["projeto"] = $data['projeto'];
    		
    	}

        //Restrintivo
        $in = implode("','",$data['filtro']['status']);
        $sql .= "and alteracao.status in ('$in') ";
    	
        $in = implode("','",$data['filtro']['tipo']);
        $sql .= "and alteracao.tipo in ('$in') "; 
        
        //Ordem
        $sql .= 'order by codigo desc;';

        //executar
        $dbh = conect();
        $sth = $dbh->prepare($sql);
        $data['sql'] = $sql;
        $sth->execute($parametros);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        //Concluir
        $data['lista'] = $result;
        $data['msg'] = 'Listado';
        $data['result'] = true;
        return $data;
    }

    static function atualizar($data){
        if(!isset($data['codigo'])){
            $data['msg'] = 'Código inválido';
            return $data;
        }
        
        if(!isset($data['projeto'])){
            $data['msg'] = 'Projeto invalido';
            return $data;
        }
        
        if(!isset($data['status'])){
            $data['msg'] = 'Status invalido';
            return $data;
        }
        
        if(!isset($data['local'])){
            $data['msg'] = 'local invalido';
            return $data;
        }
        
        if(!isset($data['descricao'])){
            $data['msg'] = 'descicao invalida';
            return $data;
        }
        
        if(!isset($data['tipo'])){
            $data['msg'] = 'tipo invalido';
            return $data;
        }

        if(!isset($data['responsavel'])){
            $data['msg'] = 'Responsavel invalido';
            return $data;
        }
        //Deveria verificar se o responsavel existe

        
        $dbh = conect('documentacao');
        $sth = $dbh->prepare("
		    Update alteracao set
		    
		    projeto = :projeto,
		    status = :status,
		    local = :local,
		    descricao = :descricao,
		    tipo = :tipo,
            responsavel = :responsavel
		    
		    where codigo = :codigo
		    
            
        ");

        $sth->execute([
            "projeto"=>$data['projeto'],
            "status"=>$data['status'],
            "local"=>$data['local'],
            "descricao"=>$data['descricao'],
            "tipo"=>$data['tipo'],
            "codigo"=>$data['codigo'],
            "responsavel"=>$data['responsavel']
        ]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $data['codigo'] = $dbh->lastInsertId();
        $data['msg'] = 'Atualizado';
        $data['result'] = true;
        return $data;
    }

    static function apagar($data){
        if(!isset($data['codigo'])){
            $data['msg'] = 'Codigo invalido';
            return $data;
        }

        $dbh = conect2('documentacao');
        $sth = $dbh->prepare("
            delete from documentacao
            where codigo = :codigo;
        ");

        $sth->execute([
            "codigo"=>$data['codigo']
        ]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $data['msg'] = 'Excluido';
        $data['excluidos'] = $sth->rowCount();
        $data['result'] = true;
        return $data;
    }

}
