//This sciript measures time, when Moodle tab is active in browser

window.addEventListener('load', function () {
var window_flag = true;
var popup_window_active = false; //is popup window active?
var interval = 5;
//var time_without_device=30000;
//var timeout_flag;
//var timeout_addMouse;

var active_time = 0; //counted time (seconds)
setInterval(set_alltime, interval * 1000);

//addMouse();

if(window.addEventListener){ //DOM browsers (Firefox,Opera,Chrome)
    window.addEventListener('focus', focus_setflag,false);
    window.addEventListener('blur', blur_setflag,false);
    //window.addEventListener('keydown',setFlagsOnActive,true);
   
}else if(window.attachEvent){ //IE
    document.attachEvent('onfocusin', focus_setflag);
    document.attachEvent('onfocusout', blur_setflag);
    //document.attachEvent('onkeydown',setFlagsOnActive);
   
}else{ //other browsers
    window['onfocus'] = focus_setflag;
    window['onblur'] = blur_setflag;
    //window['onblur']=setFlagsOnActive;
}

//Add couted time to database, connection is defined in ajax_connection.js, $start_of_url is defined in timestatlib.php
function add_time(){
    if(active_time == 0) {
        return;
    }
    var sUrl = start_of_url + active_time;
    active_time = 0;
    synchronousConnectToUrl(sUrl);
}

//Function counts active time
function set_alltime(){
    if(window_flag){
        active_time+=interval;
        add_time();
        popup_window_active = false;
    }else{ //popup - chat
        if(popup_window_active) {
        active_time+=interval;
        add_time();
        }
    }

    //javascript chat
    if( (parent.parent.opener != null && parent.parent.opener.closed == false) && isPopup){
        try{
        if(window_flag){
            parent.parent.opener.popup_window_active = true;
        }else{
            parent.parent.opener.popup_window_active = false;
        }
        }catch(exception){parent.parent.close();}

    }else{
        if(isPopup && (parent.parent.opener == null || parent.parent.opener.closed) ){parent.parent.close();}
    }

}

function focus_setflag(){
    window_flag = true;
}



function blur_setflag(evt){
    //addMouse();
    window_flag = false;
}
}, false);
