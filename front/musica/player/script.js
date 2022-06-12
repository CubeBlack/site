//Pegar a tag audio
var player = document.querySelector('.player audio');

//numero do item da plylist executando atualmente
player.atual = 20;

//Pegar a tag de conteudo
player.conteudo = document.querySelector('.embed');

//pegar tag do nome da musica em execução
player.musica_tag = document.querySelector('.player .musica');

//pegar tag de progresso
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

//Função do botão, para parar e executar
player.playpause = function(){
    if(player.paused){
        player.play();
    }else{
        player.pause();
    }

    
}

//Proximo
player.proximo = function(){
    reqRPCAdd(SYS_URL+'/rpc/musicaplaylist/itemproximo', 
        {'atual':player.atual},
        function(resp){
            if(resp.result == true){
                player.atual = resp.item;
                player.setSourceAtual();
                return;
            }
        
            //console.log('player.proximo[msg]: ' + resp.msg);
        }
    );
}


/**
 * Anterior
 */
 player.anterior = function(){
    reqRPCAdd(SYS_URL+'/rpc/musicaplaylist/itemanterior', 
        {'atual':player.atual},
        function(resp){
            if(resp.result == true){
                player.atual = resp.item;
                player.setSourceAtual();
                return;
            }
        
            //console.log('player.proximo[msg]: ' + resp.msg);
        }
    );
}

//setConteudo
function embad(local){
    /*
    function reqListener () {
        //console.log(this.responseText);
        player.conteudo.innerHTML = this.responseText;
    };
      
    var oReq = new XMLHttpRequest();
    oReq.onload = reqListener;
    oReq.open("get", SYS_URL+"/embad/" + local, true);
    oReq.send();
    */
    reqAdd(SYS_URL+"/embad/" + local, function  (resposta) {
        //console.log(resposta.responseText);
        //player.conteudo.innerHTML = this.responseText;
        player.conteudo.innerHTML = resposta.responseText;
    });
}

//pegar a musica atual
player.setSourceAtual = function(){
    reqRPCAdd(SYS_URL+'/rpc/musicaplaylist/itemdetalhe', 
        {'codigo':player.atual},
        function(resp){
            if(resp.result == false){
                player.musica_tag.innerHTML = player.atual +' - ' +  'Erro!';
                return;
            }

            player.src = SYS_URL + '/rpc/musica/sourcebycodigo?codigo=' + resp.detalhe.musica;
            player.musica_tag.innerHTML = resp.detalhe.codigo +' - ' +  resp.detalhe.musica_local;
            player.play(); //da erro, caso o usuario não tenha dado play antes
                
        }
    );
}

//
player.playlistItemAdicionar = function(musica){
    reqRPCAdd(SYS_URL+'/rpc/musicaplaylist/itemadicionar', 
        {'musica':musica},
        function(resp){
            //notificar(resp.msg);
        }
    );
}

//
player.playlistItemRemover = function(item){
    reqRPCAdd(SYS_URL+'/rpc/musicaplaylist/itemremover', 
        {'item':item},
        function(resp){
            //notificar(resp.msg);
        }
    );
}

//
player.playlistItemExecutar = function(musica){
    reqRPCAdd(SYS_URL+'/rpc/musicaplaylist/itemadicionar', 
        {'musica':musica},
        function(resp){
            //notificar(resp.msg);
        }
    );
}

//Loop de verificaçõa
player.frame = function(){
    player.get_time();
    

    //Proxima verificação
    window.setTimeout(player.frame, 1000);
}

//Inicialização
player.frame();

//Embad inicial
embad('musica/playlist');

//Puxar o a musica
player.setSourceAtual();



