<?php
//Pegar conteudo da request
$request = str_replace('embad/', '', SYS_REQUEST);
$data['conteudo'] = './view:'.($request==''?'inicio':$request);

//echo $data['conteudo'];

//se não existir, mandar para 404
if(!View::existe($data['conteudo'])){
    $data['conteudo'] = './view:404';
}