var player = document.querySelector('.player audio');
player.progresso_bar = document.querySelector('.player .progresso .bar');

//Mostrar o progresso
player.get_time = function(){
    //console.log(player.currentTime + '/' + player.duration);
    let porcentagem = Math.round((player.currentTime / player.duration)*100);
    //console.log(porcentagem);
    player.progresso_bar.style.width = porcentagem + 'vw';
    if(player.paused){
        player.progresso_bar.style.backgroundColor = 'rgb(255,0,0)'
    }
    else{
        player.progresso_bar.style.backgroundColor = 'rgb(0,0,255)'
    }
}


//Loop de verificaçõa
player.frame = function(){
    player.get_time();

    //Proxima verificação
    window.setTimeout(player.frame, 1000);
}

//Função do botão, para parar e executar
player.playpause = function(){
    if(player.paused){
        player.play();
    }else{
        player.pause();
    }
}

//setConteudo
function set_conteudo(){
    
}

//Iniciar verificação
player.frame();