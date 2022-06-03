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

//Novo sistema de paginação
$data['conteudo'] = View::byNo(0,'./view:landing', './view:', './view:404');


//Tema
$data['style_tema'] = "
<style>

:root {
      --color-a:rgba(40,40,40);
      --color-b:rgba(213,181,156);
      --color-c:rgba(100,100,255);
      --color-d:rgba(255,100,100);
      --color-e:rgba(250,250,250);
   }
</style>
";

$menu = [
   ['label'=>'Incio','url'=>''],
   ['label'=>'Artigos','url'=>'artigo']
];


//Caso eteja logado
if($global['sessao'] <> false){
   $menu[] = ['label'=>'Novo artigo','url'=>'artigo/form'];
   $menu[] = ['label'=>'Musicas','url'=>'musica/'];
   $menu[] = ['label'=>'sair','url'=>'sair'];
}

$data['nav_itens'] = [
   'tipo'=>Template::TIPO_LISTA,
   'template'=>'nav_item',
   'data'=>$menu
];





