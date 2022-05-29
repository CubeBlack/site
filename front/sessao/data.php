<?php

/*
$no = isset(SYS_REQUEST_PARTES[2])?SYS_REQUEST_PARTES[2]:'';
switch ($no) {
   case "login":
      $data['conteudo'] = './view:sessao/form';
      break;
   case "sair":
      $data['conteudo'] = './view:sessao/sair';
      break;
   default:
      $data['conteudo'] = './view:404';
      break;
}
*/

$data['conteudo'] = View::byNo(1, './view:sessao/adicionar', './view:sessao/');