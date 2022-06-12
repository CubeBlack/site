var reqPilha = [];
var reqEstado = 0;

/* Pinha de requisições para o RPC */
function reqProximo(){
    if(reqPilha.length < 1){
        return;
    }
    reqEstado = 1;
    var req = reqPilha.shift();
    var oReq = new XMLHttpRequest();
    //oReq.overrideMimeType("text/plain; charset=x-user-defined");
    
    //RPC
    if(req.tipo == 'rpc'){
        oReq.onload = function(){
            let data = JSON.parse(oReq.responseText);
            req.action(data);
            if(data.result == false){
                console.log(data.msg);
            }
            reqEstado = 0;
            reqProximo();
        }
        oReq.open("post", encodeURI(req.location), true);
        oReq.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        oReq.send(JSON.stringify(req.params));
        //console.log(req.params);
        return;
    }

    //Http
    oReq.onload = function(){
        req.action(oReq);
        reqEstado = 0;
        reqProximo();
    }
    oReq.open("get", encodeURI(req.location), true);
    //oReq.setRequestHeader("Content-Type", "text/html;charset=UTF-8");
    //oReq.setRequestHeader("Content-Type", "text/plain");
    
    oReq.send();
  
    
}

function reqAdd(location, action){
    reqPilha.push({
        "location":location,
        "tipo":"http",
        "params":null,
        "action":action
    });

    if (reqEstado == 0){
        reqProximo();
    }
}

function reqRPCAdd(location, params, action){
    reqPilha.push({
        "location":location,
        "tipo":"rpc",
        "params":params,
        "action":action
    });

    if (reqEstado == 0){
        reqProximo();
    }

}


/**
 * Nofiticações
 * https://developer.mozilla.org/pt-BR/docs/Web/API/Notification
 */

function notificar(nota) {
    // Verifica se o browser suporta notificações
    if (!("Notification" in window)) {
      alert("Este browser não suporta notificações de Desktop");
    }
  
    // Let's check whether notification permissions have already been granted
    else if (Notification.permission === "granted") {
      // If it's okay let's create a notification
      var notification = new Notification(nota);
    }
  
    // Otherwise, we need to ask the user for permission
    else if (Notification.permission !== 'denied') {
      Notification.requestPermission(function (permission) {
        // If the user accepts, let's create a notification
        if (permission === "granted") {
          var notification = new Notification(nota);
        }
      });
    }
  
    // At last, if the user has denied notifications, and you
    // want to be respectful there is no need to bother them any more.
  }
