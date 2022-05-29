<?php

Core::includeBack('Artigo');

$lista = [
    'status_publico'=>true,
    'status_rascunho'=>$global['sessao']<> false
];

Artigo::lista($lista);

//var_dump($lista['lista']);
$data['artigos'] = [
    'tipo'=>Template::TIPO_LISTA,
    'template'=>'item',
    'data'=>$lista['lista']
];

$data['paginas'] = '';

