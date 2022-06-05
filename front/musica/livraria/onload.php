<?php

$data['conteudo'] = View::byNo(
    2,                       //no
    '',         //padrão
    './view:musica/livraria/', //prefixo
    './view:404'             //se nao existir
); 


if($data['conteudo'] == './view:musica/livraria/' || $data['conteudo'] == './view:musica/livraria'){
    $data['conteudo'] = '';
    
}

//var_dump($data);


