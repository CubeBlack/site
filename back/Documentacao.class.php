<?php
class Documentacao{
    static function detalhe($data){
        if(!isset($data['codigo'])){
            $data['msg'] = 'Codigo invalido';
            return $data;
        }

        $dbh = conect();
        $sth = $dbh->prepare("
            SELECT * 
            FROM documentacao
            where codigo = :codigo
        ");
        $sth->execute(['codigo'=>$data['codigo']]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result)){
            $data['msg'] = 'Documento não encontrado';
            return $data;
        }

        $data['detalhe'] = $result[0];
        $data['msg'] = 'Detalhado';
        $data['result'] = true;
        return $data;
    }

    static function adicionar($data){
        if(!isset($data['posicao'])){
            $data['msg'] = 'posicao invalida';
            return $data;
        }

        if(!isset($data['titulo'])){
            $data['msg'] = 'Titulo invalid';
            return $data;
        }

        if(!isset($data['valor'])){
            $data['msg'] = 'Valor invalid';
            return $data;
        }

        $dbh = conect2('documentacao');
        $sth = $dbh->prepare("
            insert into documentacao set
            posicao = :posicao,
            titulo = :titulo,
            valor = :valor
        ");

        $sth->execute([
            "posicao"=>$data['posicao'],
            "titulo"=>$data['titulo'],
            "valor"=>$data['valor'],
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
        $dbh = conect();
        $sth = $dbh->prepare("SELECT codigo, posicao, titulo FROM documentacao");
        $sth->execute();
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        $data['lista'] = $result;
        $data['msg'] = 'Listado';
        $data['result'] = true;
        return $data;
    }

    static function settitulo($data){
        if(!isset($data['codigo'])){
            $data['msg'] = 'Codigo invalido';
            return $data;
        }

        if(!isset($data['titulo'])){
            $data['msg'] = 'Titulo invalido';
            return $data;
        }

        $dbh = conect2('documentacao');
        $sth = $dbh->prepare("
            update documentacao
            set titulo = :titulo
            where codigo = :codigo
        ");

        $sth->execute([
            "titulo"=>$data['titulo'],
            "codigo"=>$data['codigo']
        ]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $data['msg'] = 'Excluido';
        $data['atualizados'] = $sth->rowCount();
        $data['result'] = true;
        return $data;
    }

    static function setvalor($data){
        if(!isset($data['codigo'])){
            $data['msg'] = 'Codigo invalido';
            return $data;
        }

        if(!isset($data['valor'])){
            $data['msg'] = 'Valor invalido';
            return $data;
        }

        $dbh = conect2('documentacao');
        $sth = $dbh->prepare("
            update documentacao
            set valor = :valor
            where codigo = :codigo
        ");

        $sth->execute([
            "valor"=>$data['valor'],
            "codigo"=>$data['codigo']
        ]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }

        $data['msg'] = 'Excluido';
        $data['atualizados'] = $sth->rowCount();
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
