<?php
Core::includeBack("Usuario");
class Quest{
    const SITUACAO_PENDENTE =  'PENDENTE';
    const SITUACAO_CONCLUIDO = 'CONCLUIDO';
    const SITUACAO_CANCELADO = 'CANCELADO';
    static function lista(){
        //Pegar valores da pagina
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT * FROM quest order by label');
        $sth->execute();
        Core::bdError($sth, 'Quest/lista');

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    static function _lista(&$data){
        Core::dataInit($data);
        Core::parametroOpcional($data,'pagina', 1);
        Core::parametroOpcional($data,'itens', 0);
        Core::parametroOpcional($data,'itens_total', 0);
        //$data[''label]=
        
        //
        /*
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT count(codigo) c FROM acao');
        $sth->execute();
        Core::bdError($sth, $data['local']);

        $data['itens_total'] = $sth->fetchAll(PDO::FETCH_ASSOC)[0]['c'];
        */
        //Pegar valores da pagina
        $dbh = Core::conect();
        $query = "SELECT * FROM quest where true ";
        $parametros = [];
        
        if(isset($data['label'])){
            if($data['label']!=''){
                $query .= " and label like :label ";
                $parametros["label"] = "%${data['label']}%";
            }
        }
        
        if(isset($data['situacao'])){
            foreach ($data['situacao'] as $key => $situacao) {
                //So permitir as situações de uma lista
                //echo 
                if($situacao == false){
                    $query .= " and situacao <> :situacao_{$key} ";
                    $parametros["situacao_{$key}"] = $key;
                }
            }
        }

        $query .= ' order by label, codigo ';
        
        //echo $query;
        $sth = $dbh->prepare($query);
        $sth->execute($parametros);
        Core::bdError($sth, $data['local']);

        $data['lista'] = $sth->fetchAll(PDO::FETCH_ASSOC);
        //$data['itens'] = count($data['lista']);
        
        $data['msg'] = 'Listado';
        $data['result'] = true;
    }

    static function detalhe(&$data){
        Core::dataInit($data);
        Core::parametroObrigatorio($data, 'codigo');
        if(!Core::temParametros($data)) return;

        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT * FROM quest where codigo = :codigo');
        $sth->execute(['codigo'=>$data['codigo']]);
        
        Core::bdError($sth, 'Quest/lista');
        $resposta = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($resposta)){
            $data['msg'] = 'Quest não encontrada.';
            return;
        }

        $data['detalhe'] = $resposta[0];

        $data['msg']= 'Detalhado.';
        $data['result'] = true;
    }

    static function _adicionar(&$data){
        Core::dataInit($data);
        Core::parametroObrigatorio($data, 'quantidade');
        //Core::parametroObrigatorio($data, 'acao');
        if(!Core::temParametros($data)) return;
        
        $dbh = Core::conect();
        $sth = $dbh->prepare('INSERT acao set
            quantidade = :quantidade,
            datahora = now();
        ');
        
        $sth->execute([
            "quantidade"=>$data['quantidade']
        ]);

        Core::bdError($sth, $data['local']);

        $data['result'] = true;
    }

    static function atualizar(&$data){
        //Tratar parametros
        Core::dataInit($data);
        Core::parametroObrigatorio($data, 'label');
        Core::parametroObrigatorio($data, 'descricao');
        Core::parametroObrigatorio($data, 'xp');

        //Executar
        $dbh = Core::conect();
        $sth = $dbh->prepare('UPDATE quest set
                label    = :label,
                descricao = :descricao,
                xp = :xp
            where codigo = :codigo
        ');
        
        $sth->execute([
            "label"=>$data['label'],
            "descricao"=>$data['descricao'],
            "xp"=>$data['xp'],
            "codigo"=>$data['codigo']
        ]);

        Core::bdError($sth, 'Quest::atualizar()');

        $data['result'] = true;
    }

    static function adicionar(&$data){
        //Tratar parametros
        Core::dataInit($data);
        Core::parametroObrigatorio($data, 'label');
        Core::parametroObrigatorio($data, 'descricao');
        Core::parametroObrigatorio($data, 'xp');
        $dbh = Core::conect();

        //Executar
        $sth = $dbh->prepare('INSERT quest set
            label     = :label,
            descricao = :descricao,
            xp        = :xp,
            situacao  = :situacao
        ');
        
        $sth->execute([
            "label"=>       $data['label'],
            "descricao"=>   $data['descricao'],
            "xp"=>          $data['xp'],
            "situacao"=>    Quest::SITUACAO_PENDENTE
        ]);

        Core::bdError($sth, 'Quest::adicionar()');

        //Finalizar
        $data['msg']    = "Adicionado.";
        $data['codigo'] = $dbh->lastInsertId();
        $data['result'] = true;
    }

    static function setStatus(&$data){
        //Tratar paramentros
        Core::dataInit($data);
        Core::parametroObrigatorio($data, 'usuario');
        Core::parametroObrigatorio($data, 'quest');
        Core::parametroObrigatorio($data, 'situacao');
        if(!Core::temParametros($data)) return;

        //Pegar a quest, para saber o status e o xp
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT situacao, xp FROM quest where codigo = :codigo');
        $sth->execute(['codigo'=>$data['quest']]);
        
        Core::bdError($sth, 'Quest/lista');
        $resposta = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($resposta)){
            $data['msg'] = 'Usuario não encontrado';
            return;
        }
        
        $data['situacao_anterior'] = $resposta[0]['situacao'];
        $data['quest_xp'] = $resposta[0]['xp'];

        //Validar situação
        if($data['situacao'] == Quest::SITUACAO_CONCLUIDO||$data['situacao'] == Quest::SITUACAO_CANCELADO){
            if($data['situacao_anterior'] != Quest::SITUACAO_PENDENTE){
                $data['msg'] = "a situação não esta pendente";
                return;
            }
            
        }else{
            $data['msg'] = "Situação invalida";
            return;
        }

        //Atualizar quest
        $dbh = Core::conect();
        $sth = $dbh->prepare('UPDATE quest set
                situacao = :situacao
            where codigo = :codigo
        ');
        
        $sth->execute([
            "situacao"=>$data['situacao'],
            "codigo"=>$data['quest']
        ]);

        Core::bdError($sth, 'Quest::atualizar()');

        //Adicionar Xp ao usuario
        if($data['situacao'] == Quest::SITUACAO_CANCELADO) $incrementeXp = $data['quest_xp'] *= -1;
        else $incrementeXp = $data['quest_xp'];
        $setXp = ['usuario'=>$data['usuario'],"xp"=>$incrementeXp];
        Usuario::setXp($setXp);

        Log::add(0,$incrementeXp, "Quest");

        //Concluir
        $data['result'] = true;

    }
}