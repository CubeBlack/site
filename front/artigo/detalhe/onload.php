<?php

Core::includeBack('Artigo');


$artigo = ['label'=>(isset(SYS_REQUEST_PARTES[1]))?SYS_REQUEST_PARTES[1]:''];

Artigo::detalheByLabel($artigo);

$global['pagina_titulo'] = $artigo['detalhe']['titulo'];

//var_dump($pData['global']['sessao']);
$adm = $global['sessao']?'_adm':'';
$data['conteudo'] = [
    'tipo'=>Template::TIPO_SOLO,
    'template'=>($artigo['result']?'artigo'.$adm:'erro'),
    'data'=>($artigo['result']?$artigo['detalhe']:$artigo)
];

