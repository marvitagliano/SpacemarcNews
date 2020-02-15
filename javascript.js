/*****************************************************************
*  Spacemarc News
*  Author and copyright (C): Marcello Vitagliano
*  Web site: www.spacemarc.it
*  License: GNU General Public License
*
*  This program is free software: you can redistribute it and/or 
*  modify it under the terms of the GNU General Public License 
*  as published by the Free Software Foundation, either version 3 
*  of the License, or (at your option) any later version.
*****************************************************************/

//mostro-nascondo l'anteprima della notizia
function getEl(id) {
    element = document.getElementById(id);
    return element;
}
function hideEl(id) {
    var element = getEl(id);
    element.style.display = 'none';
}
function showEl(id) {
    var element = getEl(id);

    element.style.display = '';
}
function ShowHide() {
    var el = 'preview';
    if(getEl(el).style.display == 'none') {
        showEl(el);
        showEl('preview_n');
        hideEl('preview_y');
    } else {
        hideEl(el);
        showEl('preview_y');
        hideEl('preview_n');
    }
}

//mostro messaggi help per BBcode
var form_name = 'input_form';
b_help = "[b]text[/b]";
i_help = "[i]text[/i]";
u_help = "[u]text[/u]";
e_help = "[e]text[/e]";
g_help = "[img]http://urlimage[/img]";
a_help = "[email]email@isp.tld[/email] - [email=email@isp.tld]text[/email]";
w_help = "[url]http://www.web.tld[/url] - [url=http://web.tld]text[/url] - ftp:// - https://";
v_help = "Skype: [callto]callto:account[/callto] - [callto=callto:account]text[/callto]";
y_help = "Video YouTube: [yt]xxxxxxxxxxx[/yt]";
l_help = "Lista: [ul][li]oggetto1[/li][li]oggetto2[/li][/ul]";
q_help = "[quote]text[/quote]";
c_help = "[code]codice[/code]";
p_help = "Google Map: [gmap]url[/gmap]";
s_help = "Font: [size=?]text[/size]";
m_help = "Instant messaging";
r_help = "Text Color";


//visualizzo il messaggio di help per ciascun bbcode
function helpline(help) {
	document.forms[form_name].helpbox.value = eval(help + "_help");
}


//conto i caratteri nel campo Interessi dell'utente
function checklength(theform) {
	alert(theform.hobby.value.length);
}

//conto i caratteri in Note Email nelle impostazioni
function checklength2(theform) {
	alert("Hai scritto "+theform.note_email.value.length+" caratteri");
}

//seleziono tutti i checkbox
function checkTutti() {
  with (document.admin) {
    for (var i=0; i < elements.length; i++) {
        if (elements[i].type == 'checkbox' && elements[i].name == 'cb_id[]')
           elements[i].checked = true;
    }
  }
}


//deseleziono tutti i checkbox
function uncheckTutti() {
  with (document.admin) {
    for (var i=0; i < elements.length; i++) {
        if (elements[i].type == 'checkbox' && elements[i].name == 'cb_id[]')
           elements[i].checked = false;
    }
  }
}

//impedisco di inserire lettere o caratteri speciali nei campi letture
function getkey(e) {
	if (window.event)
		 return window.event.keyCode;
	else if (e)
		 return e.which;
	else
		 return null;
}

function onlynumbers(e, goods) {
	var key, keychar;
	key = getkey(e);
	if (key == null) return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();
	goods = goods.toLowerCase();

	if (goods.indexOf(keychar) != -1)
		return true;
	if (key==null || key==0 || key==8 || key==9 || key==13 || key==27)
		 return true;

	return false;
}


// nascode/mostra le immagini nella versione stampabile
function img_hide() {
	for(var i=0; i<document.images.length; i++) {
        document.images[i].style.display = 'none';
        document.getElementById('mostra').style.display = 'inline';
        document.getElementById('nascondi').style.display = 'none';
	}
}
function img_show() {
	for(var i=0; i<document.images.length; i++) {
        document.images[i].style.display = 'inline';
        document.getElementById('mostra').style.display = 'none';
        document.getElementById('nascondi').style.display = 'inline';
	}
}


//inserisce il testo in un punto preciso della textarea
function addText(instext) {
    var mess = document.input_form.testo;
        //IE support
        if (document.selection) {
            mess.focus();
            sel = document.selection.createRange();
            sel.text = instext;
            document.input_form.focus();
        }
        //MOZILLA/NETSCAPE support
        else if (mess.selectionStart || mess.selectionStart == "0") {
            var startPos = mess.selectionStart;
            var endPos = mess.selectionEnd;
            var chaine = mess.value;

            mess.value = chaine.substring(0, startPos) + instext + chaine.substring(endPos, chaine.length);

            mess.selectionStart = startPos + instext.length;
            mess.selectionEnd = endPos + instext.length;
            mess.focus();
        } else {
            mess.value += instext;
            mess.focus();
        }
    }

//conferma operazione
function confirmSubmit() {
  var agree=confirm("OK?");
    if (agree)
      return true;
    else
      return false;
}

//seleziono/deseleziono tutti le email
function listbox_selectall(email, isSelect) {
  var listbox = document.getElementById(email);
	 for(var count=0; count < listbox.options.length; count++) {
	   listbox.options[count].selected = isSelect;
	 }
}

//chiude la popup e va alla pagina madre
function close_and_go() {
window.opener.location=window.opener.location.href;
	window.close();
}

//verifico al login se il caps lock è abilitato
function capsLock(e){
 kc = e.keyCode?e.keyCode:e.which;
 sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
 if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
	for(i=0; i<3; i++) {
	document.getElementById("spanCaps"+i).style.visibility = 'visible';
	}
 else
	for(i=0; i<3; i++) {
	document.getElementById("spanCaps"+i).style.visibility = 'hidden';
	}
}

//aggiunge i file nel testo della notizia
function addFile(y) {
	espressione = window.opener.document.input_form.testo.value;
	new_espressione = espressione + y;
	window.opener.document.input_form.testo.value = new_espressione;
}


//mostro-nascondo la segnalazione commenti
function reportComment(id) {
	var state = document.getElementById(id).style.display;
		if (state == 'block') {
			document.getElementById(id).style.display = 'none';
		} else {
			document.getElementById(id).style.display = 'block';
		}
	}
	
//inserisce citazione commento
function InserisciQuote(testo){
	document.getElementById("commento").value += testo;
	}


//abilito il campo per la data futura di una notizia
function datafutura() {
	if(document.input_form.cb_datafutura.checked) {
		document.input_form.data_pubb.disabled=false;
	} else {
		document.input_form.data_pubb.disabled=true;
	}
}

//disabilito altre select nella sostituzione tags e commenti
function chgSelect(which) {
	if (document.getElementById('dove').selectedIndex == 3 || document.getElementById('dove').selectedIndex == 4) {
		document.getElementById('time').disabled = true;
		document.getElementById('autore').disabled = true;		
		document.getElementById('categoria').disabled = true;
    } else {
		document.getElementById('time').disabled = false;
		document.getElementById('autore').disabled = false;		
		document.getElementById('categoria').disabled = false;
	}
}