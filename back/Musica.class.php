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

    static function _sourcebycodigo(){
        //Parametros
        $codigo = isset($_REQUEST['codigo'])?$_REQUEST['codigo']:'';
        //var_dump($codigo);

        if($codigo == ''){
            die('Codigo invalido ou inexistente.');
        }

        //...
        $dbh = Core::conect();  
        $sth = $dbh->prepare("SELECT * FROM musicas
            where codigo = :codigo
            limit 1
        ");
        $sth->execute(['codigo'=>$codigo]);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result[0]);

        //$path = (isset($_REQUEST['path'])?$_REQUEST['path']:'');
        $filepath = LIVRARIA_LOCAL . $result[0]['local']; 

        //die($filepath);
        
        $total     = filesize($filepath);
        $blocksize = (2 << 20); //2M chunks
        $sent      = 0;
        $handle    = fopen($filepath, "r");
        
        
        //die('aqui');
        
        // Push headers that tell what kind of file is coming down the pike
        header('Content-type: audio/mpeg');
        //header('Content-Disposition: attachment; filename=source');
        header('Content-length: '.$total * 1024);
                       
        // Now we need to loop through the file and echo out chunks of file data
        // Dumping the whole file fails at > 30M!
        
        while($sent < $total){
            echo fread($handle, $blocksize);
            $sent += $blocksize;
        }

        //die('...');
    }

    

    
}