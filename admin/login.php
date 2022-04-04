<?php

/*****************************************************************
 *  Spacemarc News
 *  Author and copyright (C): Marcello Vitagliano
 *  License: GNU General Public License
 *
 *  This program is free software: you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation, either version 3
 *  of the License, or (at your option) any later version.
 *****************************************************************/
 
session_start();
header('Content-type: text/html; charset=ISO-8859-1');

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');

//connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

$cookie = (isset($_COOKIE['accesso_news'])) ? $_COOKIE['accesso_news'] : NULL;

if (isset($_SESSION['loggato'])) {
    header('Location: ' . $dir_admin . '/inserisci.php');
    exit();
}

if ($cookie) {
    
    $result = @mysqli_query($db, "SELECT token FROM `$tab_utenti` WHERE sha1(token)='$cookie' LIMIT 1");
    $riga = @mysqli_fetch_assoc($result);
    
    if (sha1($riga['token']) !== $cookie) {
        header('Location: ' . $dir_admin . '/logout.php');
        exit();
    } else {
		header('Location: ' . $dir_admin . '/inserisci.php');
		exit();
	}
}

// controllo se l'IP è bannato per troppi tentativi di login
$sql_ip = @mysqli_query($db, "SELECT login_errati, dataora FROM `$tab_ban` WHERE ban_ip ='" . $_SERVER['REMOTE_ADDR'] . "' AND login_errati >= 5 AND white_list = 0");
$riga_ip = @mysqli_fetch_assoc($sql_ip);

if ( time() - 300 <= $riga_ip['dataora'] ) { //300 secondi = 5 minuti
	die ( $lang['ip_bloccato'] );
} else {
	@mysqli_query($db, "DELETE FROM `$tab_ban` WHERE ban_ip = '" . $_SERVER['REMOTE_ADDR'] . "' AND login_errati >= 5 AND white_list = 0 LIMIT 1");
}
	
$errore_login = NULL;
$abilitato_no = NULL;
$ultimo_tentativo = NULL;

if ( isset($_POST['submit']) ) {

	$email = @mysqli_real_escape_string($db, trim($_POST['email']));
	$pass = @mysqli_real_escape_string($db, $_POST['user_password']);
	
    $result = @mysqli_query($db, "SELECT user_id, nome_cognome, email, livello_id, user_password, attivo FROM `$tab_utenti` WHERE email='$email' ORDER BY user_id ASC LIMIT 1") or die ("Error " . @mysqli_error($db));

    // estraggo le righe che mi interessano
    $riga = @mysqli_fetch_assoc($result);

    //se la query restituisce un risultato e l'utente è abilitato e la password corrisponde 
    if ( @mysqli_num_rows($result) == 1 && $riga['attivo'] == 1 && password_verify($pass, $riga['user_password']) ) {

        //registro le sessioni
        $_SESSION['loggato'] = "login_ok";
        $_SESSION['user_id'] = $riga['user_id'];
        $_SESSION['email'] = $riga['email'];        
        $_SESSION['livello_id'] = $riga['livello_id'];
        $_SESSION['nome_cognome_sess'] = $riga['nome_cognome'];
        $_SESSION['ultimo_accesso'] = time();

        // invio un cookie per accedere automaticamente al sistema
        
        if (isset($_POST['accesso_news'])) {
            $expire = 2592000;
            $random = mt_rand(0, 40);
            $token = sha1($random . time());
            setcookie("accesso_news", sha1($token) , time() + $expire, "/" . $news_dir);
            @mysqli_query($db, "UPDATE `$tab_utenti` SET token='$token', cookie=1, ultimo_accesso=" . time() . " WHERE user_id=" . intval($_SESSION['user_id']));

        }
        else {
            @mysqli_query($db, "UPDATE `$tab_utenti` SET ultimo_accesso=" . time() . " WHERE user_id=" . intval($_SESSION['user_id']));
        }
        
		@mysqli_query($db, "DELETE FROM `$tab_ban` WHERE ban_ip = '" . $_SERVER['REMOTE_ADDR'] . "' AND login_errati > 0 AND white_list = 0 LIMIT 1");
        header('Location: ' . $dir_admin . '/inserisci.php');
        exit();
    } 
    
    // se la query restituisce una riga e l'utente non è abilitato
    elseif (mysqli_num_rows($result) == 1 && $riga['attivo'] == 0) {
        $errore_login = NULL;
        $abilitato_no = '<div id="error">' . $lang['user_non_aut'] . '</div>';
    }
    
    // se la query restituisce zero righe o se la password inserita è errata
    elseif ( mysqli_num_rows($result) == 0 || !password_verify($pass, $riga['user_password']) ) {

        $abilitato_no = NULL;
        $errore_login = '<div id="error">' . $lang['invalid_user_pass'] . '</div>';
        
        sleep(2);
        
        // blocco IP in caso di login falliti
        $sql_banip = @mysqli_query($db, "SELECT id_ban FROM `$tab_ban` WHERE ban_ip ='" . $_SERVER['REMOTE_ADDR'] . "'");
        $row_banip = @mysqli_fetch_array($result);
        $righe = @mysqli_num_rows($sql_banip);

		// se l'IP non è presente in tabella lo inserisco
		if ( mysqli_num_rows($sql_banip) == 0 ) {

			$abilitato_no = NULL;
			$errore_login = '<div id="error">' . $lang['invalid_user_pass'] . '</div>';
			
			mysqli_query($db, "INSERT INTO `$tab_ban` (ban_ip, dataora, login_errati, white_list) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', " . time() . ", 1, 0)");
		
		// se l'IP è presente in tabella ma non è in white list incremento il numero di tentativi errati
		} elseif ( mysqli_num_rows($sql_banip) > 0 && $row_banip['white_list'] == 0) {

			 mysqli_query($db, "UPDATE `$tab_ban` SET dataora = " . time() . ", login_errati=login_errati+1 WHERE ban_ip = '" . $_SERVER['REMOTE_ADDR'] . "' AND login_errati >= 0 AND white_list = 0 LIMIT 1");
			
			$sql_login = mysqli_query($db, "SELECT login_errati FROM `$tab_ban` WHERE ban_ip = '" . $_SERVER['REMOTE_ADDR'] . "' AND login_errati > 0 AND white_list = 0");
			$row_login = mysqli_fetch_assoc($sql_login);  
			  
			 //se i login errati sono 4 mostro l'avviso di ultimo tentativo
			if ( $row_login['login_errati'] == 4 ) {
				$ultimo_tentativo = '<div id="error">' . $lang['ultimo_tentativo'] . '</div>';
			}
		
		// se l'IP è presente in tabella ed in white list mostro solo il messaggio di accesso non valido
		} elseif ( mysqli_num_rows($sql_banip) > 0 && $row_banip['white_list'] == 1) {

			$abilitato_no = NULL;
			$errore_login = '<div id="error">' . $lang['invalid_user_pass'] . '</div>';
			
			}
		}
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['news'] . ' - ' . $lang['signin']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" media="screen" />    		
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>  
  </head>     
  <body>        
<br /><br />	     
    <form action="login.php" method="post" id="form_login" name="form_login">		       
      <div id="login"><br />       
        <b class="text">Email</b><br />
        <input type="text" name="email" maxlength="50" size="24" id="email" /><br /><br />
        <b class="text"><?php echo $lang['password']; ?></b><br />
        <input type="password" name="user_password" maxlength="30" size="24" id="user_password" value="" onkeypress="capsLock(event)" />
        <br /><input type="checkbox" onchange="document.getElementById('user_password').type = this.checked ? 'text' : 'password'" id="showpwd" /><label for="showpwd" class="text2"><?php echo $lang['showpwd']; ?></label><br />
        <span id="spanCaps0" style="visibility:hidden;" class="text2"><b><?php echo $lang['capslock']; ?></b><br /></span> 
        <br />
        <input type="submit" name="submit" value=" &nbsp;<?php echo $lang['signin']; ?> &nbsp; " id="submit" style="font-weight: bold;" /><br /><br /><input type="checkbox" name="accesso_news" id="accesso_news" /><label for="accesso_news" class="help" title="<?php echo $lang['ricorda_title']; ?>"><?php echo $lang['ricorda']; ?></label> &nbsp; <a href="javascript:;" onclick="window.open('sendpwd.php', '', 'width=330, height=200, resizable=1, scrollbars=0, location=1, status=1');" class="piccolo" title="[Popup]">          
          <?php echo $lang['pwdsend']; ?></a><br /><br />		       
      </div>	     
    </form>
<script language="JavaScript" type="text/javascript"> document.form_login.email.focus(); </script><br />
    <?php echo $errore_login; echo $abilitato_no; echo '<br />' . $ultimo_tentativo; ?>  
<!-- (C) Spacemarc News -->
  </body>
</html>
