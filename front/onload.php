<?php
$global['pagina_titulo'] = 'Daniel Lima';

//Sessao
$global['sessao'] = false;
if(!isset($_SESSION[SESSAO_CHAVE])) session_start();
if(isset($_SESSION[SESSAO_CHAVE])){
   $sessao_chave = $_SESSION[SESSAO_CHAVE];
   Core::includeBack('usuario/Sessao');
   $global['sessao'] = Sessao::detalhe($sessao_chave);
}

//Escolher a pagina master
//$data['master'] = View::byNo(0,'./view:master/principal', './view:master/', './view:404');

$master = (isset(SYS_REQUEST_PARTES[0]))?SYS_REQUEST_PARTES[0]:'';
switch ($master) {
   case 'embad':
      $data['master'] = './view:master/embad';
      break;
   case 'dashboard':
         $data['master'] = './view:master/dashboard';
         break;
   
   default:
      $data['master'] = './view:master/principal';
      break;
}



