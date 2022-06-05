<?php

$data['conteudo'] = View::byNo(
    1,                       //no
    './view:musica/player',         //padrão
    './view:musica/', //prefixo
    './view:404'             //se nao existir
); 


if($data['conteudo'] == './view:musica/' || $data['conteudo'] == './view:musica'){
    $data['conteudo'] = './view:musica/player';
    
}

//var_dump($data);


