<?php
Core::includeBack('Musica');

$lista = [
    'status_publico'=>true,
    'status_rascunho'=>$global['sessao']<> false
];

Musica::lista($lista);

//var_dump($lista);

//var_dump($lista['lista']);
$data['itens'] = [
    'tipo'=>Template::TIPO_LISTA,
    'template'=>'item',
    'data'=>$lista['lista']
];
