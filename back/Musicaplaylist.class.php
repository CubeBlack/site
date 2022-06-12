<?php
class Musicaplaylist {
    static function lista(&$data){
        Core::dataInit($data);
        Core::parametroOpcional($data, 'banda');
        Core::parametroOpcional($data, 'album');
        $dbh = Core::conect();  

        $sql = "SELECT 
            musicas_playlist.codigo,
            musicas.codigo as musica,
            musicas.local as musica_local

        FROM `musicas_playlist` 
        inner join musicas on musicas_playlist.musica = musicas.codigo
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
            order by codigo 
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

    

    static function itemdetalhe(&$data){
        Core::dataInit($data);
        Core::parametroObrigatorio($data,'codigo');
        
        if(!Core::temParametros($data)) return;

        $dbh = Core::conect();  
        $sth = $dbh->prepare("SELECT 
            musicas_playlist.codigo,
            musicas.codigo as musica,
            musicas.local as musica_local

            FROM `musicas_playlist` 
            inner join musicas on musicas_playlist.musica = musicas.codigo
            where musicas_playlist.codigo = :codigo"
        );

        $sth->execute(['codigo'=>$data['codigo']]);
        
        if($dbh->errorInfo()[0]!='00000'){
            $data['msg'] = "Mysql erro({$dbh->errorInfo()[1]}): {$dbh->errorInfo()[2]}";
            return $data;
        }
        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result)){
            $data['msg'] = "Item não encontrada";
            return;
            
        }

        $data['detalhe'] = $result[0];
        $data['result'] = true;
    }

    //Atalho para acesar via rpc
    static function _itemdetalhe(&$data){
        Musicaplaylist::itemdetalhe($data);
    }   
    /**
     * @data = {musica, }
     */
    static function _itemadicionar(&$data){
        Core::parametroObrigatorio($data,'musica');
        if(!Core::temParametros($data)) return;

        $dbh = Core::conect();  
        $sth = $dbh->prepare("INSERT musicas_playlist set
            musica = :musica
            
        ");

        $sth->execute([
            'musica'=>$data['musica']
        ]);

        Core::bdError($sth, $data['local']);
        $data['msg'] = "Musica {$data['musica']} adicionada a Playlist";

        return;
    }
    
    static function _itemproximo(&$data){
        Core::parametroObrigatorio($data,'atual');
        if(!Core::temParametros($data)) return;

        //Procurar o proximo da lista
        $dbh = Core::conect();  
        $sth = $dbh->prepare("SELECT codigo
            from musicas_playlist
            where codigo > :atual
            order by codigo
            limit 1
        ");

        $sth->execute([
            'atual'=>$data['atual']
        ]);

        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($result)){
            $data['item'] = $result[0]['codigo'];
            $data['result'] = true;
            return;
        }

        //Não tendo o proximo, procurar o primeiro
        $dbh = Core::conect();  
        $sth = $dbh->prepare("SELECT codigo
            from musicas_playlist
            order by codigo
            limit 1
        ");

        $sth->execute();

        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result)){
            $data['msg'] = 'Playlist vazia';
            return;
        }
        
        $data['item'] = $result[0]['codigo'];
        $data['result'] = true;

        return;
    }

    static function _itemanterior(&$data){
        Core::parametroObrigatorio($data,'atual');
        if(!Core::temParametros($data)) return;

        //Procurar o item anterior na lista
        $dbh = Core::conect();  
        $sth = $dbh->prepare("SELECT codigo
            from musicas_playlist
            where codigo < :atual
            order by codigo desc
            limit 1
        ");

        $sth->execute([
            'atual'=>$data['atual']
        ]);

        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($result)){
            $data['item'] = $result[0]['codigo'];
            $data['result'] = true;
            return;
        }

        //Não tendo o proximo, procurar o ultimo
        $dbh = Core::conect();  
        $sth = $dbh->prepare("SELECT codigo
            from musicas_playlist
            order by codigo desc
            limit 1
        ");

        $sth->execute();

        Core::bdError($sth, $data['local']);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result)){
            $data['msg'] = 'Playlist vazia';
            return;
        }
        
        $data['item'] = $result[0]['codigo'];
        $data['result'] = true;

        return;
    }
    
}