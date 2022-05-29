<?php
$global['pagina_titulo'] = 'Adicionar Artigo';
if($global['sessao']== false){
    //header('location: '.SYS_URL);
    die('Você não deveria estar aqui.');
}

Core::includeBack("Artigo");

//valor padrão do formulario
$data['msg'] = 'Adicone novo item';
$data['titulo'] = '';
$data['label'] = 'Empty!';
$data['status'] = 'RASCUNHO';
$data['codigo'] = '00';
$data['texto'] = 'Empty!';

//Atualizar
if(isset($_REQUEST['titulo']) && isset($_REQUEST['codigo'])){
    $artigo = [
        'titulo'=>$_REQUEST['titulo'],
        'status'=>$_REQUEST['status'],
        'texto'=>$_REQUEST['texto'],
        'codigo'=>$_REQUEST['codigo']
        
    ];
    Artigo::atualizar($artigo);
    $data['msg'] = $artigo['msg'];

    //header('location: ' . SYS_URL . '/artigo/'.$artigo['detalhe']['label']);
}

//Adicionar 
if(isset($_REQUEST['titulo']) && !isset($_REQUEST['codigo'])){
    $artigo = [
        'titulo'=>$_REQUEST['titulo'],
        'status'=>$_REQUEST['status'],
        'texto'=>$_REQUEST['texto']
    ];
    Artigo::adicionar($artigo);
    $data['msg'] = $artigo['msg'];
    //var_dump($artigo);
    header('location: ' . SYS_URL . '/artigo/'.$artigo['label']);

}


//Mostrar para editar
if(isset($_REQUEST['codigo'])){
    $global['pagina_titulo'] = 'Editar Artigo';
    $artigo = [
        'codigo'=>$_REQUEST['codigo'],
    ];
    Artigo::detalhe($artigo);
    $data['msg'] = 'Edite o artigo';
    $data['titulo'] = $artigo['detalhe']['titulo'];
    $data['label'] = $artigo['detalhe']['label'];
    $data['status'] = $artigo['detalhe']['status'];
    $data['codigo'] = $artigo['detalhe']['codigo'];
    $data['texto'] = $artigo['detalhe']['texto'];

    $data['status_racunho_selected'] = ($artigo['detalhe']['status'] == 'RASCUNHO')?'selected':'';
    $data['status_publico_selected'] = ($artigo['detalhe']['status'] == 'PUBLICO')?'selected':'';
    $data['status_oculto_selected']  = ($artigo['detalhe']['status'] == 'OCULTO')?'selected':'';
    
}



