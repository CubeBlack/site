<?php 
Core::includeBack('Log');
Core::includeBack('Usuario');
Class Atividade{
    static function lista(&$data){
        Core::dataInit($data);
        
        //Listar
        $dbh = Core::conect();
        $sth = $dbh->prepare("SELECT 
            atividade.*, 
            atividade_conclusao.conclusao,
            if(atividade_conclusao.conclusao, 'CONCLUIDO', 'PENDENTE') as situacao
            FROM atividade
            left join atividade_conclusao on 
                atividade.codigo = atividade_conclusao.atividade 
                AND DATE(atividade_conclusao.conclusao) = CURDATE()
        ");
        $sth->execute();
        Core::bdError($sth, $data['local']);

        $data['lista'] = $sth->fetchAll(PDO::FETCH_ASSOC);

        //Finalizar
        $data['result'] = 'true';
        $data['msg'] = 'Listado';
    }

    static function concluir(&$data){
        Core::dataInit($data);
        Core::parametroObrigatorio($data,'codigo');
        if(!Core::temParametros($data)){
            return;
        }

        //Carregar atividade
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT
                atividade.*, 
                atividade_conclusao.conclusao,
                if(atividade_conclusao.conclusao, "CONCLUIDO", "PENDENTE") as situacao
            from atividade
            left join atividade_conclusao on 
                atividade.codigo = atividade_conclusao.atividade 
                AND DATE(atividade_conclusao.conclusao) = CURDATE()
            where atividade.codigo = :codigo
            limit 1
            
        ');
        $sth->execute(['codigo'=>$data['codigo']]);
        Core::bdError($sth, $data['local']);

        $resposta = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($resposta)){
            $data['msg'] = 'Atividade não encontrada';
            return;
        }

        $data['atividade'] = $resposta[0];

        //Verificar se ja foi concluido
        if($data['atividade']['situacao'] == 'CONCLUIDO'){
            $data['msg'] = "Atividade '{$data['atividade']['label']}' ({$data['codigo']}) já concluida para esse periodo.";
            return;
        }

        //registrar conclusão
        $dbh = Core::conect();
        $sth = $dbh->prepare("INSERT atividade_conclusao set
            conclusao = now(),
            atividade = :atividade
        
        ");
        $sth->execute([
            "atividade"=>$data['atividade']['codigo']
        ]);
        Core::bdError($sth, $data['local']);

        $data['lista'] = $sth->fetchAll(PDO::FETCH_ASSOC);
        
        //Calcular XP
        $setxp = ["usuario"=>1,"xp"=>10];
        Usuario::setXP($setxp);
        
        //Adicionar no log
        Log::add(0,10, "Atividade '{$data['atividade']['label']}' Concluida");
        $data['msg'] = "Atividade '{$data['atividade']['label']}' ({$data['codigo']}) Concluido.";
    }
}
