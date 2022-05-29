var reqPilha = [];
var reqEstado = 0;

function reqProximo(){
    reqEstado = 1;
    var req = reqPilha.shift();
    var oReq = new XMLHttpRequest();
    //oReq.overrideMimeType("text/plain; charset=x-user-defined");
    
    //RPC
    if(req.tipo == 'rpc'){
        oReq.onload = function(){
            req.action(JSON.parse(oReq.responseText));
            reqEstado = 0;
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

    if (reqEstado == 0){
        reqProximo();
    }
}
