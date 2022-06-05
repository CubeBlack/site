<?php
class Musica {
    static function lista(&$data){
        Core::dataInit($data);
        Core::parametroOpcional($data, 'banda');
        Core::parametroOpcional($data, 'album');
        $dbh = Core::conect();  

        $sql = "SELECT 
        *
        from musicas
        where true
        ";

        //Filtros
/*        if (!$data['status_publico']){
            $sql .= "AND status <> 'PUBLICO' ";
        }
        if (!$data['status_rascunho']){
            $sql .= "AND status <>  'RASCUNHO' ";
        }
        */

        
        $sth = $dbh->prepare($sql . "
            order by codigo desc
        ");
        $sth->execute();
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }
        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);


        $data['lista'] = $result;
        $data['result'] = true;
    }

    static function adicionar(&$data){
        //Validar parametros
        Core::dataInit($data);
        Core::parametroObrigatorio($data,'titulo');
        Core::parametroObrigatorio($data,'status');
        Core::parametroObrigatorio($data,'texto');
        
        if(!Core::temParametros($data)) return;

        if($data['titulo'] == ''){
            $data['msg'] = 'Titulo não pode ser vazio';
            return;
        }

        if($data['texto'] == ''){
            $data['msg'] = 'Texto não pode ser vazio';
            return;
        }

        //criar label
        $data['label'] = preg_replace('/[\@\;\" "]+/', '_', $data['titulo']); // sem caractesres especiais
        $data['label'] = strtolower($data['label']); //Tudo menusculo
        //die($data['label']);

        //Registrar 
        $dbh = Core::conect();  
        $sth = $dbh->prepare("INSERT artigo set
            titulo = :titulo,
            status = :status,
            texto = :texto,
            label = :label
        ");
        $sth->execute([
            'titulo'=>$data['titulo'],
            'status'=>$data['status'],
            'texto'=>$data['texto'],
            'label'=>$data['label']
        ]);
        
        Core::bdError($sth, $data['local']);

        $data['result'] = true;
    }

    static function detalhe(&$data){
        Core::dataInit($data);
        Core::parametroObrigatorio($data,'codigo');
        
        if(!Core::temParametros($data)) return;

        $dbh = Core::conect();  
        $sth = $dbh->prepare("
            SELECT * 
            FROM artigo
            where codigo = :codigo
        ");
        $sth->execute(['codigo'=>$data['codigo']]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }
        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        $data['detalhe'] = $result[0];
        $data['result'] = true;
    }

    static function atualizar(&$data){
        Core::dataInit($data);
        Core::parametroObrigatorio($data,'codigo');
        Core::parametroObrigatorio($data,'titulo');
        Core::parametroObrigatorio($data,'status');
        Core::parametroObrigatorio($data,'texto');
        
        if(!Core::temParametros($data)) return;

        $dbh = Core::conect();  
        $sth = $dbh->prepare("UPDATE artigo SET
            titulo = :titulo,
            status = :status,
            texto = :texto
            where codigo = :codigo
        ");
        $sth->execute([
            'codigo' => $data['codigo'],
            'titulo' => $data['titulo'],
            'status' => $data['status'],
            'texto' => $data['texto']
        ]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }
        Core::bdError($sth, $data['local']);
        $data['msg']= 'Artigo atualizado';
        $data['result'] = true;
    }

    static function detalheByLabel(&$data){
        Core::dataInit($data);
        Core::parametroObrigatorio($data,'label');
        
        if(!Core::temParametros($data)) return;

        $dbh = Core::conect();  
        $sth = $dbh->prepare("
            SELECT 
            *
            from artigo
            where label = :label
            
            limit 1
        ");
        $sth->execute(['label'=>$data['label']]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }
        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result)){
            $data['msg'] = 'Artigo não encontrado';
            return;
        }

        $data['detalhe'] = $result[0];
        $data['result'] = true;
    }

    
}