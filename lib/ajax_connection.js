function createXMLHTTP(){
try{
    if(window.XMLHttpRequest){
        var oRequest = new XMLHttpRequest();
        return oRequest;
    }else{
        var oRequest = new ActiveXObject ("Microsoft.XMLHTTP");
        return oRequest;
    }
}catch(exception){}
    return null;
}

function synchronousConnectToUrl(url){
    var oRequest = createXMLHTTP();
    if(oRequest != null){
        oRequest.open("get", url,false);
        oRequest.send(null);
    }
}