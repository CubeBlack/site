<?php
class Log{
    static function add($usuario, $xp, $msg){
        Core::dataInit($data);
        
        //Listar
        $dbh = Core::conect();
        $sth = $dbh->prepare("INSERT log set
            usuario = :usuario,
            xp = :xp,
            datahora = now(),
            msg = :msg
        
        ");

        $sth->execute([
            "usuario"=>$usuario,
            "xp"=>$xp,
            "msg"=>$msg
        ]);

        Core::bdError($sth, $data['local']);
        return true;
    }

    static function lista(&$data){
        Core::dataInit($data);
        
        //Listar
        $dbh = Core::conect();
        $sth = $dbh->prepare("SELECT 
            log.*,
            'user' as usuario_nick
            FROM log
            /*
            left join atividade_conclusao on 
                atividade.codigo = atividade_conclusao.atividade 
                AND atividade_conclusao.conclusao = CURDATE()*/
            order by codigo desc
            limit 20
        ");
        $sth->execute();
        Core::bdError($sth, $data['local']);

        $data['lista'] = $sth->fetchAll(PDO::FETCH_ASSOC);

        //Finalizar
        $data['result'] = 'true';
        $data['msg']    = 'Listado';

    }
}