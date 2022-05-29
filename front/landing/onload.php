<?php
$global['pagina_titulo'] = 'Bem-vindo ao meu mundo!';
Core::includeBack('Artigo');

$lista = [
    'status_publico'=>true,
    'status_rascunho'=>false
];

Artigo::lista($lista);

//var_dump($lista['lista']);
$data['cards'] = [
    'tipo'=>Template::TIPO_LISTA,
    'template'=>'item',
    'data'=>$lista['lista']
];

