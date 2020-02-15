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

if (isset($_SESSION['loggato'])) {
    die("Error");
}

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['password']; ?>          
    </title>    
    <link rel="stylesheet" href="../style.css" type="text/css" />     
  </head>     
  <body>         
    <div align="center">
<?php
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);
$errore_captcha = NULL;

if (isset($_POST['submit'])) {
    
    if (!preg_match('/^[.a-z0-9_-]+@[.a-z0-9_-]+\.[a-z]{2,4}$/', $_POST['email'])) {
        echo '<div id="error">' . $lang['wrong_email'] . '</div>';
    }
    else {
        
        if ($_POST['spamcode'] != @$_SESSION['antispam'] || !empty($_POST['web'])) {
            $errore_captcha = '<div id="error">' . $lang['antispam_error'] . '</div>';
        }
        else {

            //estraggo i record dell'utente
            $result = @mysqli_query($db, "SELECT nu.nome_cognome, nu.email, nc.url_sito FROM `$tab_utenti` nu, `$tab_config` nc WHERE nu.email='" . $_POST['email'] . "' AND nu.attivo=1 LIMIT 1");
            $riga = @mysqli_fetch_assoc($result);
            
            if ($riga['email'] == $_POST['email']) {
                
                $phpversion = (!@phpversion()) ? "N/A" : phpversion();
                $pwd_random = NewPassword();
				$password_utente = password_hash($pwd_random, PASSWORD_DEFAULT);
                $key_pwd = substr(sha1(uniqid(microtime())) , 0, 10);
                
                mysqli_query($db, "UPDATE `$tab_utenti` SET new_pwd='$password_utente', key_pwd='$key_pwd' WHERE email='" . $riga['email'] . "' LIMIT 1");
                
                $header = "From: " . $_SERVER['SERVER_ADMIN'] . "\n";
                $header.= "Reply-To: " . $_SERVER['SERVER_ADMIN'] . "\n";
                $header.= "Return-Path: " . $_SERVER['SERVER_ADMIN'] . "\n";
                $header.= "X-Mailer: PHP " . $phpversion . "\n";
                $header.= "MIME-Version: 1.0\n";
                $header.= "Content-type: text/plain; charset=ISO-8859-1\n";
                $header.= "Content-Transfer-encoding: 7bit\n";
                @mail($riga['email'], $lang['send_pwd_subject'], $lang['send_pwd_body1'] . html_entity_decode($riga['nome_cognome'], ENT_QUOTES, "ISO-8859-1") . " (IP " . $_SERVER['REMOTE_ADDR'] . ")\n\n" . $lang['send_pwd_body2'] . " $pwd_random\n\n" . $lang['send_pwd_body3'] . " $dir_admin/sendpwd.php?key=$key_pwd\n\n" . $lang['send_pwd_body4'] . "\n\n-- \n" . $riga['url_sito'] . "", $header);
                
                echo '<div id="success">' . $lang['send_pwd_ok'] . '</div><script language="JavaScript" type="text/JavaScript">setTimeout(\'window.close()\', 4500)</script>';
            }
            else {
				sleep(2);
                echo '<div id="error">' . $lang['send_pwd_error'] . '</div>';
            }
        }
    }
}
elseif (isset($_GET['key']) && preg_match('/^([a-z0-9]{10})$/', $_GET['key'])) {
    $result_pwd = @mysqli_query($db, "SELECT user_id, attivo, new_pwd, key_pwd FROM `$tab_utenti` WHERE key_pwd='" . $_GET['key'] . "' AND attivo=1 LIMIT 1");
    $rigap = @mysqli_fetch_array($result_pwd);
    
    if ($rigap['key_pwd'] == $_GET['key']) {
        @mysqli_query($db, "UPDATE `$tab_utenti` SET user_password='" . $rigap['new_pwd'] . "', new_pwd=NULL, key_pwd=NULL WHERE user_id=" . $rigap['user_id'] . " LIMIT 1");
        @mysqli_query($db, "DELETE FROM `$tab_ban` WHERE ban_ip = '" . $_SERVER['REMOTE_ADDR'] . "' AND login_errati >= 5 LIMIT 1");
        echo '<br /><span class="text">' . $lang['new_pwd_active'] . ' <a href="login.php">' . $lang['signin'] . '</a></span></div></body></html>';
        exit();
    }
    else {
        echo $lang['invalid_key'] . '</div></body></html>';
        exit();
    }
}
?>             
      <span class="text"><b>          
          <?php echo $errore_captcha; ?></b>             
      </span><br />       
      <form method="post" name="sendpwd" action="sendpwd.php">                 
        <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" class="text2">                     
          <tr>                         
            <td align="right" class="text" valign="top">Email</td>
			<td align="left"><input type="text" name="email" size="20" maxlength="50" /><br /><br /><?php echo captcha(); ?> <input name="spamcode" type="text" size="3" maxlength="6" /> <input type="text" name="web" size="10" value="" class="hp" /><input type="submit" name="submit" value="<?php echo $lang['vai']; ?>" style="font-weight: bold;" /></td>
          </tr>                 
        </table>             
      </form>         
    </div>     
  </body>
</html>
