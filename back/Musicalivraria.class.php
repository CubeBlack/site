<?php
class Musicalivraria {
    static function detalhe(&$data){
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

    static function verificar(&$data){
        Core::dataInit($data);
        //Limpar playlist
        $dbh = Core::conect();  
        $sth = $dbh->prepare("TRUNCATE musicas_playlist");
        $sth->execute();

        //Limpar livraria
        $dbh = Core::conect();  
        $sth = $dbh->prepare("DELETE FROM musicas WHERE true");
        $sth->execute();
        
        //Verificar e adicionar musicas
        Musicalivraria::verificar_pasta(LIVRARIA_LOCAL);

        $data['msg'] = 'Verificado.';
    }

    static function verificar_pasta($raiz){
        $diretorio = dir($raiz);
        while ($end_node = $diretorio -> read()) {
            //Ignorar diretorios 'virtuais'
            if($end_node == '.' || $end_node == '..'){
                continue;
            }
            //Criar diretorio completo
            $iDir = $raiz.DIRECTORY_SEPARATOR.$end_node;
            //echo "<p>$iDir</p>";
            
            //Sendo uma pasta, verificar conteudo
            if(is_dir($iDir)){
                Musicalivraria::verificar_pasta($iDir);
            }
            
            //sendo uma musica, adicioar a livria
            if(is_readable($iDir) && pathinfo($iDir, PATHINFO_EXTENSION) == 'mp3'){
                $dbh = Core::conect();  
                $sth = $dbh->prepare("INSERT musicas SET
                    local = :local
                ");

                $sth->execute([
                    'local'=>str_replace(LIVRARIA_LOCAL, '', $iDir)
                ]);
            }
        }
        $diretorio -> close(); 
    }

    
    
}