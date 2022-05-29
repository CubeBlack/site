<?php
Core::includeBack('usuario/Sessao');
$data['msg'] = 'Entre com suas credenciais';
$data['usuario'] = '';
//$data['redirect'] = isset($_GET['redirect']) ?$_GET['redirect']:'';
//var_dump($_REQUEST);
//die($data['redirect']);

//Verificar se o usuaio ja esta logado

//Tratar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    //Tratar parametros;
    $usuario   = isset($_POST['usuario'])    ?$_POST['usuario']:'';
    $senha     = isset($_POST['senha'])      ?$_POST['senha']:'';
    $aplicacao = isset($_GET['aplicacao'])   ?$_GET['aplicacao']:'';
    $aplicacao = isset($_GET['request_code'])?$_GET['request_code']:'';

    //echo "usuario:$usuario;senha:$senha<br>";

    $resposta = Sessao::adicionar($usuario, $senha, $aplicacao);

    if(!$resposta){
        $data['msg'] = 'Não foi posivel efetuar login';
    }else{
        if(!isset($_SESSION)) session_start();
        $_SESSION[SESSAO_CHAVE] = $resposta;
        header("location: ".SYS_URL."dashboard");
        $data['msg'] = 'Login efetuado com sucessos';
    }
}