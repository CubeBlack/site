<?php
Core::includeBack('Musicaplaylist');

$lista = [
    'status_publico'=>true,
    'status_rascunho'=>$global['sessao']<> false
];

Musicaplaylist::lista($lista);

//var_dump($lista);

//var_dump($lista['lista']);
$data['itens'] = [
    'tipo'=>Template::TIPO_LISTA,
    'template'=>'item',
    'data'=>$lista['lista']
];
