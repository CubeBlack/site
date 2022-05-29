var gadgetUsuario = {
    htmlRetorno:function(){
        document.querySelector('.gadget_user').innerHTML = this.responseText;
    },

    iniciar:function(){
        //Pegar HTML
        var oReq = new XMLHttpRequest();
        oReq.onload = this.htmlRetorno;
        oReq.open("get", "http://localhost/main/resources/usuario/estrutura.html", true);
        oReq.send();
    },

    popupShow:function(){
        document.querySelector('.popup').style.display = 'block';
        document.querySelector('.popup_base').style.display = 'block';
    },

    popupClose:function(){
        document.querySelector('.popup').style.display = 'none';
        document.querySelector('.popup_base').style.display = 'none';
    }
};
//Carregar css


//Carregar gedGed
function reqListener () {
    //console.log(this.responseText);
    //document.querySelector('.gadget_user').innerHTML = this.responseText;
};
  

document.write("<div class=\"gadget_user\">[gadget_user]</div>");
gadgetUsuario.iniciar();
