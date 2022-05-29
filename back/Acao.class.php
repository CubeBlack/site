<?php
class Acao{
    static function _lista(&$data){
        Core::dataInit($data);
        Core::parametroOpcional($data,'pagina', 1);
        Core::parametroOpcional($data,'itens', 0);
        Core::parametroOpcional($data,'itens_total', 0);
        
        //Pegar total de itens
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT count(codigo) c FROM acao');
        $sth->execute();
        Core::bdError($sth, $data['local']);

        $data['itens_total'] = $sth->fetchAll(PDO::FETCH_ASSOC)[0]['c'];

        //Pegar valores da pagina
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT * FROM acao order by codigo desc limit 100');
        $sth->execute();
        Core::bdError($sth, $data['local']);

        $data['detalhe'] = $sth->fetchAll(PDO::FETCH_ASSOC);
        $data['itens'] = count($data);
        
        $data['msg'] = 'Listado';
    }

    static function _listaPorDia(&$data){
        Core::dataInit($data);
        Core::parametroOpcional($data,'pagina', 1);
        Core::parametroOpcional($data,'itens', 0);
        Core::parametroOpcional($data,'itens_total', 0);
        
        //Pegar total de itens
        /*
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT 
                cast(datahora as date), 
                count(datahora) FROM `acao`
            group by cast(datahora as date)
        ');
        $sth->execute();
        Core::bdError($sth, $data['local']);

        $data['itens_total'] = $sth->fetchAll(PDO::FETCH_ASSOC)[0]['c'];
        */
        //Pegar valores da pagina
        $dbh = Core::conect();
        $sth = $dbh->prepare('SELECT 
                cast(datahora as date) as dia, 
                /*WEEKDAY(datahora) as diadasemana,*/
                sum(quantidade) as acoes 
            FROM `acao`
            group by cast(datahora as date)
            order by cast(datahora as date) desc
        ');
        $sth->execute();
        Core::bdError($sth, $data['local']);

        $data['detalhe'] = $sth->fetchAll(PDO::FETCH_ASSOC);
        $data['itens'] = count($data);
        
        $data['msg'] = 'Listado';
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


}