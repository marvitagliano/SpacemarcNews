<?php

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
 
session_start();
header('Content-type: text/html; charset=ISO-8859-1');

//calcolo il tempo di generazione della pagina (1a parte)
$mtime1 = explode(" ", microtime());
$starttime = $mtime1[1] + $mtime1[0];

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');

$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);
check_login();

// se non sono un amministratore e voglio visualizzare questa pagina, redirigo all'elenco news personale

if ($_SESSION['livello_id'] == 1) {
    header('Location: ' . $dir_admin . '/profilo_admin.php');
    exit();
}

check_form();

//variabili e notice
$nome_cognome_errato = NULL;
$data_nascita_errata = NULL;
$facebook_errato = NULL;
$twitter_errato = NULL;
$instagram_errato = NULL;
$email_errata = NULL;
$email_esiste = NULL;
$user_password1_error = NULL;
$user_password2_error = NULL;
$user_password2_short = NULL;
$user_password2_empty = NULL;
$user_password3_error = NULL;
$update_msg = NULL;
$q_user_id = intval($_SESSION['user_id']);
$delete_news_msg = NULL;

//conto quanti file ha inserito l'utente loggato
$files = 0;

if ($aprodir = @opendir('../' . $file_dir . '/' . $q_user_id)) {
    
    while (false !== ($ifile = readdir($aprodir))) {
        
        if ($ifile != '.' && $ifile != '..' && $ifile != 'index.html') {
            ++$files;
        }
    }
    closedir($aprodir);
}
$files = ($files == 0) ? 0 : $files;
$link_files = ($files > 0) ? '(<a href="javascript:;" onclick="window.open(\'files.php?user_id=' . $q_user_id . '\', \'\', \'width=660, height=450, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['dettagli'] . '</a>)' : NULL;

//seleziono l'utente
$q_profilo = mysqli_query($db, "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.user_password, nu.attivo, nu.permessi, nu.autorizza_news, nu.mostra_link, nu.email_nascosta, nu.sito, nu.im, nu.im_num, nu.facebook, nu.twitter, nu.instagram, nu.data_nascita, nu.citta, nu.occupazione, nu.hobby, nu.ultimo_accesso, nu.data_registrazione, nu.cookie, nl.nome_livello, nt.data_pubb, (SELECT COUNT(id) FROM `$tab_news` WHERE user_id=$q_user_id) AS TotaleNews, (SELECT ROUND(COUNT(id) / (DATEDIFF(NOW(), MIN(FROM_UNIXTIME(data_pubb)))+1), 1) FROM `$tab_news` WHERE user_id=$q_user_id) AS MediaGiornaliera, (SELECT formato_data FROM `$tab_config`) AS FormatoData FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nu.user_id=nt.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id WHERE nu.user_id=$q_user_id ORDER BY nt.data_pubb DESC LIMIT 1") or die (mysqli_error($db));
$q_riga = mysqli_fetch_assoc($q_profilo);

//seleziono il formato data

switch ($q_riga['FormatoData']) {
    case 1:
        $data_registrazione = strftime("%a %d %b %Y, %H:%M", $q_riga['data_registrazione']);
        $ultimo_accesso = strftime("%a %d %b %Y, %H:%M", $q_riga['ultimo_accesso']);
        $ultima_inserita = strftime("%a %d %b %Y, %H:%M", $q_riga['data_pubb']);
    break;
    case 2:
        $data_registrazione = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $q_riga['data_registrazione']));
        $ultimo_accesso = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $q_riga['ultimo_accesso']));
        $ultima_inserita = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $q_riga['data_pubb']));
    break;
    case 3:
        $data_registrazione = strftime("%d/%m/%Y, %H:%M", $q_riga['data_registrazione']);
        $ultimo_accesso = strftime("%d/%m/%Y, %H:%M", $q_riga['ultimo_accesso']);
        $ultima_inserita = strftime("%d/%m/%Y, %H:%M", $q_riga['data_pubb']);
    break;
    case 4:
        $data_registrazione = strftime("%d %b %Y, %H:%M", $q_riga['data_registrazione']);
        $ultimo_accesso = strftime("%d %b %Y, %H:%M", $q_riga['ultimo_accesso']);
        $ultima_inserita = strftime("%d %b %Y, %H:%M", $q_riga['data_pubb']);
    break;
    case 5:
        $data_registrazione = strftime("%d %B %Y, %H:%M", $q_riga['data_registrazione']);
        $ultimo_accesso = strftime("%d %B %Y, %H:%M", $q_riga['ultimo_accesso']);
        $ultima_inserita = strftime("%d %B %Y, %H:%M", $q_riga['data_pubb']);
    break;
    case 6:
        $data_registrazione = strftime("%m/%d/%Y, %I:%M %p", $q_riga['data_registrazione']);
        $ultimo_accesso = strftime("%m/%d/%Y, %I:%M %p", $q_riga['ultimo_accesso']);
        $ultima_inserita = strftime("%m/%d/%Y, %I:%M %p", $q_riga['data_pubb']);
    break;
    case 7:
        $data_registrazione = strftime("%B %d, %Y %I:%M %p", $q_riga['data_registrazione']);
        $ultimo_accesso = strftime("%B %d, %Y %I:%M %p", $q_riga['ultimo_accesso']);
        $ultima_inserita = strftime("%B %d, %Y %I:%M %p", $q_riga['data_pubb']);
    break;
    case 8:
        $data_registrazione = strftime("%I:%M %p %B %d, %Y", $q_riga['data_registrazione']);
        $ultimo_accesso = strftime("%I:%M %p %B %d, %Y", $q_riga['ultimo_accesso']);
        $ultima_inserita = strftime("%I:%M %p %B %d, %Y", $q_riga['data_pubb']);
    break;
}

//estraggo i campi dal db
$data_pubb = ($q_riga['TotaleNews'] == 0) ? NULL : ' - ' . $lang['ultima'] . ' ' . $ultima_inserita;
$email_nascosta = ($q_riga['email_nascosta'] == 1) ? '<input type="checkbox" name="user_email_nascosta" id="user_email_nas" checked="checked" /><label for="user_email_nas" class="text2">' . $lang['emailnascosta'] . '</label>' : '<input type="checkbox" name="user_email_nascosta" id="user_email_nas" /><label for="user_email_nas" class="text2">' . $lang['emailnascosta'] . '</label>';
$user_news_perday = ($q_riga['MediaGiornaliera'] == 0) ? '' : ' (' . $q_riga['MediaGiornaliera'] . ' ' . $lang['per_giorno'] . ')';
$user_totale_news = $lang['totale'] . ' ' . $q_riga['TotaleNews'] . $user_news_perday;
$user_attivo = ($q_riga['attivo'] == 1) ? '- Status: ' . $lang['attivo'] . '' : $lang['status_disattivo'];
$user_autorizza_news = ($q_riga['autorizza_news'] == 1) ? '' : '<span style="color: rgb(255, 0, 0);">' . $lang['user_autorizza_news'] . '</span>';
$data_nascita = ($q_riga['data_nascita'] == 1) ? '' : $q_riga['data_nascita'];
$facebook = ($q_riga['facebook'] == NULL) ? '' : $q_riga['facebook'];
$twitter = ($q_riga['twitter'] == NULL) ? '' : $q_riga['twitter'];
$instagram = ($q_riga['instagram'] == NULL) ? '' : $q_riga['instagram'];
$user_ultimo_accesso = ($q_riga['ultimo_accesso'] != 0) ? ' - ' . $lang['ultimo_accesso'] . ': ' . $ultimo_accesso . '' : ' - ' . $lang['ultimo_accesso'] . ': N/A';
$livello = $lang['livello'] . ': ' . $q_riga['nome_livello'];
$cb_cookie = ($q_riga['cookie'] == 1 || isset($_COOKIE['accesso_news'])) ? '<input type="checkbox" name="cookie" id="cookie" checked="checked" /><label for="cookie" class="help" style="border-bottom: 1px dotted #000;" title="' . $lang['ricorda_title'] . '">' . $lang['ricorda'] . '</label>' : '<input type="checkbox" name="cookie" id="cookie" /><label for="cookie" class="help" style="border-bottom: 1px dotted #000;" title="' . $lang['ricorda_title'] . '">' . $lang['ricorda'] . '</label>';
$cbdelfile_dis = ($files == 0) ? 'disabled="disabled"' : NULL;

if ($q_riga['TotaleNews'] > 0) {
   $btn_cancella_news = '<input type="checkbox" name="cbdelnews" id="cbdelnews" title="' . $lang['cancella_news'] . ' " /><label for="cbdelnews">&#129152;</label> <input type="submit" name="btndelnews" id="btndelnews" value="' . $lang['cancella_news'] . ' &#9888;" onclick="alert(\'' . $lang['attenzione_news'] . '\'); return confirmSubmit()" />';    
}
else {
    $btn_cancella_news = NULL;
}
$link_invia_email = NULL;

//controllo Instant Messaging

switch ($q_riga['im']) {
    case 'whatsapp':
        $im_selected1 = NULL;
        $im_selected2 = 'selected="selected"';
        $im_selected3 = NULL;
        $im_selected5 = NULL;
        $im_selected6 = NULL;
        $im_selected7 = NULL;           
    break;
    case 'icq':
        $im_selected1 = NULL;
        $im_selected2 = NULL;
        $im_selected3 = 'selected="selected"';
        $im_selected5 = NULL;
        $im_selected6 = NULL;
        $im_selected7 = NULL;           
    break;
    case 'skype':
        $im_selected1 = NULL;
        $im_selected2 = NULL;
        $im_selected3 = NULL;
        $im_selected5 = 'selected="selected"';
        $im_selected6 = NULL;
        $im_selected7 = NULL;           
    break;
    case 'telegram':
        $im_selected1 = NULL;
        $im_selected2 = NULL;
        $im_selected3 = NULL;
        $im_selected5 = NULL;
        $im_selected6 = 'selected="selected"';
        $im_selected7 = NULL;        
    break;
    case 'signal':
        $im_selected1 = NULL;
        $im_selected2 = NULL;
        $im_selected3 = NULL;
        $im_selected5 = NULL;
        $im_selected6 = NULL;
        $im_selected7 = 'selected="selected"';        
    break;     
    default:
        $im_selected1 = 'selected="selected"';
        $im_selected2 = NULL;
        $im_selected3 = NULL;
        $im_selected5 = NULL;
        $im_selected6 = NULL;
        $im_selected7 = NULL;            
}

//controllo Opzioni autore

switch ($q_riga['mostra_link']) {
    case 'nome':
        $link_selected1 = 'checked="checked"';
        $link_selected2 = NULL;
        $link_selected3 = NULL;
        $link_selected4 = NULL;
        $link_selected5 = NULL;
        $link_selected6 = NULL;
        $link_selected7 = NULL;        
    break;
    case 'email':
        $link_selected1 = NULL;
        $link_selected2 = 'checked="checked"';
        $link_selected3 = NULL;
        $link_selected4 = NULL;
        $link_selected5 = NULL;
        $link_selected6 = NULL;
        $link_selected7 = NULL;        
    break;
    case 'sito':
        $link_selected1 = NULL;
        $link_selected2 = NULL;
        $link_selected3 = 'checked="checked"';
        $link_selected4 = NULL;
        $link_selected5 = NULL;
        $link_selected6 = NULL;
        $link_selected7 = NULL;        
    break;
    case 'profilo':
        $link_selected1 = NULL;
        $link_selected2 = NULL;
        $link_selected3 = NULL;
        $link_selected4 = 'checked="checked"';
        $link_selected5 = NULL;
        $link_selected6 = NULL;
        $link_selected7 = NULL;        
    break;
    case 'facebook':
        $link_selected1 = NULL;
        $link_selected2 = NULL;
        $link_selected3 = NULL;
        $link_selected4 = NULL;
        $link_selected5 = 'checked="checked"';
        $link_selected6 = NULL;
        $link_selected7 = NULL;        
    break;
    case 'twitter':
        $link_selected1 = NULL;
        $link_selected2 = NULL;
        $link_selected3 = NULL;
        $link_selected4 = NULL;
        $link_selected5 = NULL;
        $link_selected6 = 'checked="checked"';
        $link_selected7 = NULL;        
    break;
    case 'twitter':
        $link_selected1 = NULL;
        $link_selected2 = NULL;
        $link_selected3 = NULL;
        $link_selected4 = NULL;
        $link_selected5 = NULL;
        $link_selected6 = NULL;
        $link_selected7 = 'checked="checked"';        
    break;    
}

//se nel browser non c'è più il cookie ed invece è settato l'accesso automatico, ne invio un altro

if ($q_riga['cookie'] == 1 && !isset($_COOKIE['accesso_news']) && $q_user_id == $_SESSION['user_id']) {
    $expire = 2592000;
    $random = mt_rand(0, 40);
    $token = sha1($random . time());
    mysqli_query($db, "UPDATE `$tab_utenti` SET token='$token' WHERE user_id=" . intval($q_user_id));
    setcookie("accesso_news", sha1($token) , time() + $expire, "/" . $news_dir);
}

//servono come valori dei campi di testo, non per l'UPDATE
$nome_cognome_value = $q_riga['nome_cognome'];
$email_value = $q_riga['email'];
$sito_value = $q_riga['sito'];
$im_num_value = $q_riga['im_num'];
$occupazione_value = $q_riga['occupazione'];
$citta_value = $q_riga['citta'];
$hobby_value = $q_riga['hobby'];

if (isset($_POST['submit'])) {

    //richiamo le funzioni che controllano vari campi di testo
    passwords();
    nome_cognome();
    check_email();
    socialnet();
    data_nascita();
    altri_campi();
    post_im();

    //servono come valori dei campi di testo, non per l'UPDATE
    $nome_cognome_value = htmlspecialchars($_POST['nome_cognome'], ENT_QUOTES, "ISO-8859-1");
	$email_value = htmlspecialchars($_POST['email'], ENT_QUOTES, "ISO-8859-1");
	$sito_value = htmlspecialchars($_POST['sito'], ENT_QUOTES, "ISO-8859-1");
	$im_num_value = htmlspecialchars($_POST['im_num'], ENT_QUOTES, "ISO-8859-1");
	$occupazione_value = htmlspecialchars($_POST['occupazione'], ENT_QUOTES, "ISO-8859-1");
	$citta_value = htmlspecialchars($_POST['citta'], ENT_QUOTES, "ISO-8859-1");
	$hobby_value = htmlspecialchars($_POST['hobby'], ENT_QUOTES, "ISO-8859-1");

    //invio il cookie se scelgo di accedere automaticamente al sistema
    
    if (isset($_POST['cookie']) && !isset($_COOKIE['accesso_news']) && $q_user_id == $_SESSION['user_id']) {
        $expire = 2592000;
        $random = mt_rand(0, 40);
        $token = sha1($random . time());
        mysqli_query($db, "UPDATE `$tab_utenti` SET token='$token', cookie=1 WHERE user_id=" . intval($q_user_id));
        setcookie("accesso_news", sha1($token) , time() + $expire, "/" . $news_dir);
    }
    elseif (!isset($_POST['cookie']) && isset($_COOKIE['accesso_news']) && $q_user_id == $_SESSION['user_id']) {
        setcookie("accesso_news", "", time() - 3600, "/" . $news_dir);
        mysqli_query($db, "UPDATE `$tab_utenti` SET token=NULL, cookie=0 WHERE user_id=" . intval($q_user_id));
    }

    //aggiornamento profilo
    
	if ($user_password_ok == 1 && $user_nome_cognome_ok == 1 && $user_email_ok == 1 && $email_ok == 1 && $user_data_nascita_ok == 1 && $facebook_ok == 1 && $twitter_ok == 1 && $instagram_ok == 1) {

        //se scelgo di cancellare o disattivare un utente o le news
        
		$nome_cognome = mysqli_real_escape_string($db, $nome_cognome);
        $email = mysqli_real_escape_string($db, $email);
        $sito = mysqli_real_escape_string($db, $sito);
        $im_num = mysqli_real_escape_string($db, $im_num);
        $citta = mysqli_real_escape_string($db, $citta);
        $occupazione = mysqli_real_escape_string($db, $occupazione);
        $hobby = mysqli_real_escape_string($db, $hobby);

        //se l'utente ha lasciato commenti con la vecchia email la aggiorno
        $sql_email = mysqli_query($db, "SELECT COUNT(email_autore) AS TotEmail FROM `$tab_commenti` WHERE email_autore='" . $q_riga['email'] . "'");
		$row_email = mysqli_fetch_assoc($sql_email);    
			
			if ($row_email['TotEmail'] > 0 ) {
                mysqli_query($db, "UPDATE `$tab_commenti` SET autore='$nome_cognome', email_autore='$email', sito_autore='" . htmlspecialchars($sito, ENT_QUOTES, "ISO-8859-1") . "' WHERE email_autore='" . $q_riga['email'] . "'");
            }

			if (mysqli_query($db, "UPDATE `$tab_utenti` SET mostra_link='$rb_mostra_link', email_nascosta=$user_email_nascosta_val, nome_cognome='" . htmlspecialchars($nome_cognome, ENT_QUOTES, "ISO-8859-1") . "', user_password='$user_password_new', email='$email', sito='" . htmlspecialchars($sito, ENT_QUOTES, "ISO-8859-1") . "', im='" . $_POST['im'] . "', im_num='" . htmlspecialchars($im_num, ENT_QUOTES, "ISO-8859-1") . "', facebook='" . htmlspecialchars($facebook2, ENT_QUOTES, "ISO-8859-1") . "', twitter='" . htmlspecialchars($twitter2, ENT_QUOTES, "ISO-8859-1") . "', instagram='" . htmlspecialchars($instagram2, ENT_QUOTES, "ISO-8859-1") . "', data_nascita='$user_data_nascita2', citta='" . htmlspecialchars($citta, ENT_QUOTES, "ISO-8859-1") . "', occupazione='" . htmlspecialchars($occupazione, ENT_QUOTES, "ISO-8859-1") . "', hobby='" . htmlspecialchars($hobby, ENT_QUOTES, "ISO-8859-1") . "' WHERE user_id=" . intval($q_user_id))) {
                $update_msg = '<div id="success">' . $lang['edit_prof_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                
                //se ho modificato l'email la riscrivo nella sessione
                if ( $email != $_SESSION['email'] ) {
					$_SESSION['email'] = $email;    
				}
				
				header("Refresh: 2; url=profilo_utente.php");
				
            }
            else {
                $update_msg = '<div id="error">' . $lang['edit_prof_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        
        if (isset($_POST['cbdelfile'])) {
            full_rmdir('../' . $file_dir . '/' . $q_user_id);
        }
    }
    
} elseif ( isset($_POST['btndelnews']) ) {
			
	if ( isset($_POST['cbdelnews']) ) { 
		
		// rimuovo la relazione tags-news
		mysqli_query($db, "DELETE FROM `$tab_tags_rel` WHERE id_news IN (SELECT id FROM `$tab_news` WHERE user_id = $q_user_id)");

		mysqli_query($db, "DELETE FROM `$tab_news` WHERE user_id = $q_user_id");
		$delete_news_msg = '<div align="center"><span class="text"><b>' . $lang['canc_news_user_ok'] . '</b></span> <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
				
		header("Refresh: 2; url=profilo_utente.php");
		
	} else {
		$delete_news_msg = '<div id="error">' . $lang['attenzione_news_cb'] . '</div><br />';
	}
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>              
    <title><?php echo $lang['profilo_admin']; ?>
    </title>              
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>         
  </head>        
  <body>
<?php
require_once ("menu.php");
echo $update_msg;
echo operazioni_utente();
echo $delete_news_msg;
?>              
    <form action="profilo_utente.php" method="post" name="form_profilo">                    
      <table width="100%" align="center" style="border: 3px solid #DDDDDD;" cellpadding="4" cellspacing="2">	                                
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left" width="35%">                                      
            <b class="text"><?php echo $lang['account']; ?></b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">            
            <?php echo $livello . ' ' . $user_attivo . ' ' . $user_autorizza_news; ?></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['data_reg']; ?></b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">            
            <?php echo $data_registrazione . $user_ultimo_accesso; ?></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text">              
              <?php echo $lang['newsinserite']; ?></b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">            
            <?php echo $user_totale_news . $data_pubb; ?></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['nome_utente']; ?></b><br />                                      
            <span class="text2">              
              <?php echo $lang['nomecognomedescr']; ?>            
            </span></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="text" name="nome_cognome" value="<?php echo $nome_cognome_value; ?>" size="32" maxlength="40" /> <?php echo $cb_cookie; ?> 
            <?php echo $nome_cognome_errato; ?></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text">Email</b><br />                                      
            <span class="text2">              
              <?php echo $lang['emaildescr']; ?>            
            </span></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="text" name="email" value="<?php echo htmlspecialchars($email_value, ENT_QUOTES, "ISO-8859-1"); ?>" size="32" maxlength="50" />                                        
            <?php echo $email_nascosta; ?>                                           
            <?php echo $link_invia_email; ?>                                         
            <?php echo $email_errata; ?>                                           
            <?php echo $email_esiste; ?></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['pwd_attuale']; ?></b><br />                                      
            <span class="text2">              
              <?php echo $lang['passwordattdescr']; ?>            
            </span></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="password" name="user_password1" id="user_password1" size="32" maxlength="40" onkeypress="capsLock(event)" /> <input type="checkbox" onchange="document.getElementById('user_password1').type = this.checked ? 'text' : 'password'" id="showpwd1" /><label for="showpwd1" class="text2"><?php echo $lang['showpwd']; ?></label> <span id="spanCaps0" style="visibility:hidden;" class="text2"><b><?php echo $lang['capslock']; ?></b></span>
<?php echo $user_password1_error;
echo $user_password2_error; ?></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['new_pwd']; ?></b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="password" name="user_password2" id="user_password2" size="32" maxlength="40" onkeypress="capsLock(event)" /> <input type="checkbox" onchange="document.getElementById('user_password2').type = this.checked ? 'text' : 'password'" id="showpwd2" /><label for="showpwd2" class="text2"><?php echo $lang['showpwd']; ?></label> <span id="spanCaps1" style="visibility:hidden;" class="text2"><b><?php echo $lang['capslock']; ?></b></span>
<?php echo $user_password2_empty;
echo $user_password2_short; ?></td>                              
        </tr>  
		<tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['conferma_new_pwd']; ?></b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="password" name="user_password3" id="user_password3" size="32" maxlength="40" onkeypress="capsLock(event)" /> <input type="checkbox" onchange="document.getElementById('user_password3').type = this.checked ? 'text' : 'password'" id="showpwd3" /><label for="showpwd3" class="text2"><?php echo $lang['showpwd']; ?></label> <span id="spanCaps2" style="visibility:hidden;" class="text2"><b><?php echo $lang['capslock']; ?></b></span>
<?php echo $user_password3_error; ?></td>                              
        </tr>                                
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['mostrasito']; ?></b><br />                                      
            <span class="text2">              
              <?php echo $lang['sitowebdescr']; ?>            
            </span></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="text" name="sito" value="<?php echo $sito_value; ?>" size="32" maxlength="50" /></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text">Instant Messaging</b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <select name="im">                                                       
              <option value="-" <?php echo $im_selected1; ?>><?php echo $lang['scegli']; ?>          
              </option>                                                       
              <option value="icq" <?php echo $im_selected3; ?> title="ICQ">ICQ               
              </option>                                                       
              <option value="skype" <?php echo $im_selected5; ?> title="Skype">Skype               
              </option> 
              <option value="whatsapp" <?php echo $im_selected2; ?> title="Whatsapp">Whatsapp               
              </option>    
			  <option value="telegram" <?php echo $im_selected6; ?> title="Telegram">Telegram               
              </option>    
			  <option value="signal" <?php echo $im_selected7; ?> title="Signal">Signal               
              </option> 
            </select> Nick/Num                                        
            <input type="text" name="im_num" value="<?php echo $im_num_value; ?>" size="20" maxlength="32" /></td>                              
        </tr>              
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text">Social Network</b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">
			 <span style="cursor: help; border-bottom: 1px dotted #000;" title=".a-zA-Z0-9 5,50">Facebook</span> 
            <input type="text" name="facebook" value="<?php echo htmlspecialchars($facebook, ENT_QUOTES, "ISO-8859-1"); ?>" size="20" maxlength="50" /> 
            <span style="cursor: help; border-bottom: 1px dotted #000;" title="a-zA-Z0-9_ 1,15">Twitter</span>
            <input type="text" name="twitter" value="<?php echo htmlspecialchars($twitter, ENT_QUOTES, "ISO-8859-1"); ?>" size="20" maxlength="15" />
            <span style="cursor: help; border-bottom: 1px dotted #000;" title="a-zA-Z0-9._ 1,30">Instagram</span>
            <input type="text" name="instagram" value="<?php echo htmlspecialchars($instagram, ENT_QUOTES, "ISO-8859-1"); ?>" size="20" maxlength="30" /> 
            <?php echo $facebook_errato; echo $twitter_errato; echo $instagram_errato; ?></td>                                                               
        </tr>                           
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['data_nascita']; ?></b><br />                                      
            <span class="text2">              
              <?php echo $lang['nascitadescr']; ?>            
            </span></td>                                       
          <td bgcolor="#EEEEEE" align="left">                                      
            <input type="text" name="data_nascita" value="<?php echo htmlspecialchars($data_nascita, ENT_QUOTES, "ISO-8859-1"); ?>" size="11" maxlength="10" />                                        
            <?php echo $data_nascita_errata; ?></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['lavoro']; ?></b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="text" name="occupazione" value="<?php echo $occupazione_value; ?>" size="32" maxlength="50" /></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['citta']; ?></b></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                      
            <input type="text" name="citta" value="<?php echo $citta_value; ?>" size="32" maxlength="50" /></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left" valign="top">                                      
            <b class="text"><?php echo $lang['hobby']; ?></b><br />                                      
            <span class="text2">              
              <?php echo $lang['interessidescr']; ?>            
            </span>                                        
            <a href="javascript:checklength(document.form_profilo);" class="piccolo"><?php echo $lang['conta']; ?></a><br />                                      
            <span class="text2">              
              <?php echo $lang['interessidescr2']; ?>            
            </span></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">
<textarea name="hobby" cols="36" rows="3"><?php echo $hobby_value; ?></textarea></td>                              
        </tr>                                  
        <tr>                                       
          <td bgcolor="#DEE3E7" align="left">                                      
            <b class="text"><?php echo $lang['opzioni']; ?></b><br />                                      
            <span class="text2">              
              <?php echo $lang['opzionidescr']; ?>            
            </span></td>                                       
          <td bgcolor="#EEEEEE" align="left" class="text">                                             
            <input type="radio" id="rb_nome" name="rb" value="nome" <?php echo $link_selected1; ?> /><label for="rb_nome"><?php echo $lang['solonome']; ?></label>                                            
            <input type="radio" id="rb_email" name="rb" value="email" <?php echo $link_selected2; ?> /><label for="rb_email"><?php echo $lang['mostraemail']; ?></label>                                             
            <input type="radio" id="rb_sito" name="rb" value="sito" <?php echo $link_selected3; ?> /><label for="rb_sito"><?php echo $lang['mostrasito']; ?></label>                                             
            <input type="radio" id="rb_profilo" name="rb" value="profilo" <?php echo $link_selected4; ?> /><label for="rb_profilo"><?php echo $lang['mostraprofilo']; ?></label>            
            <input type="radio" id="rb_facebook" name="rb" value="facebook" <?php echo $link_selected5; ?> /><label for="rb_facebook"><?php echo $lang['mostrafb']; ?></label>            
            <input type="radio" id="rb_twitter" name="rb" value="twitter" <?php echo $link_selected6; ?> /><label for="rb_twitter"><?php echo $lang['mostratw']; ?></label>
            <input type="radio" id="rb_instagram" name="rb" value="instagram" <?php echo $link_selected7; ?> /><label for="rb_instagram"><?php echo $lang['mostraig']; ?></label>
            </td>
        </tr>                                 
        <tr>                       
          <td bgcolor="#DEE3E7" align="left">            
            <b class="text"><?php echo $lang['canc_disatt']; ?></b></td>                       
          <td bgcolor="#EEEEEE" align="left" class="text">              
            <?php echo $btn_cancella_news; ?>               
            <input type="checkbox" name="cbdelfile" id="cbdelfile" onclick="if (this.checked) { alert('<?php echo $lang['attenzione_file']; ?>'); }" <?php echo $cbdelfile_dis; ?> /><label for="cbdelfile"><span style="color: rgb(255, 0, 0);"><?php echo $lang['delete'] . ' ' . $files; ?> files </span></label> <?php echo $link_files; ?></td>                 
        </tr>                              
        <tr>                                       
          <td bgcolor="#DEE3E7" align="center" colspan="2">     
			<input type="hidden" name="post_token" value="<?php echo sha1(session_id()); ?>" />                                 
            <input type="submit" name="submit" value="<?php echo $lang['btn_modifica']; ?>" style="font-weight: bold;" /></td>                              
        </tr>                            
      </table>              
    </form><br />              
    <?php require_once ("footer.php"); ?>          
  </body>
</html>
