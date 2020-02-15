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

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');

$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);
check_login();

// se NON sono un amministratore e voglio visualizzare questa pagina, redirigo all'elenco news personale

if ($_SESSION['livello_id'] != 1) {
    header('Location: ' . $dir_admin . '/elenco_news.php');
    exit();
}

check_form();

$err_email = NULL;
$mail_msg = NULL;
$get_user_id = (isset($_GET['user_id'])) ? $_GET['user_id'] : NULL;
$oggetto_value = NULL;
$messaggio_value = NULL;

if (isset($_POST['submit'])) {
	
	$oggetto_value = (isset($_POST['oggetto'])) ? htmlspecialchars($_POST['oggetto'], ENT_QUOTES, "ISO-8859-1") : NULL;
	$messaggio_value = (isset($_POST['messaggio'])) ? htmlspecialchars($_POST['messaggio'], ENT_QUOTES, "ISO-8859-1") : NULL;

    //controllo campi
    
    if (!preg_match('/^[.a-zA-Z0-9!?:,+\'\[\]\-\(\)\s]{1,50}$/', stripslashes(trim($_POST['oggetto']))) || trim($_POST['messaggio']) == '' || empty($_POST['email'])) {
        $err_email = '<div id="error">' . $lang['email_utenti_campi'] . '</div>';
    }
    else {

        //controllo se ho scelto di ricevere una copia dell'email
        
        if (isset($_POST['cb_admin'])) {
            $result = mysqli_query($db, "SELECT email FROM `$tab_utenti` WHERE user_id=" . intval($_SESSION['user_id']) . " LIMIT 1");
            $row = mysqli_fetch_array($result);
            $mail_admin = "," . $row['email'];
        }
        else {
            $mail_admin = NULL;
        }
        $phpversion = (!@phpversion()) ? 'N/A' : phpversion();
        $to = $mail_admin;
        $oggetto = stripslashes($_POST['oggetto']);
        $messaggio = htmlentities($_POST['messaggio'], ENT_QUOTES);
        $messaggio .= "\n\n--\n" . $lang['firma_email'] . " - http://" . $_SERVER['HTTP_HOST'];
        $to_bbc = implode(",", $_POST['email']);
        $header = "From: $mail_admin\n";
        $header.= "Bcc: " . $to_bbc . $mail_admin . "\n";
        $header.= "Reply-To: $mail_admin\n";
        $header.= "Return-Path: $mail_admin\n";
        $header.= "X-Mailer: PHP " . $phpversion . "\n";
        $header.= "MIME-Version: 1.0\n";
        $header.= "Content-type: text/plain; charset=ISO-8859-1\n";
        $header.= "Content-Transfer-encoding: 7bit\n";
        
        if (mail($to, $oggetto, $messaggio, $header)) {
            $mail_msg = '<div id="success">' . $lang['email_utenti_ok'] . '</div> <script language="JavaScript" type="text/JavaScript">setTimeout(\'window.close()\', 2500)</script>';
        }
        else {
            $mail_msg = '<div id="error">' . $lang['email_utenti_error'] . '</div>';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['invia_email']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>      
  </head>     
  <body>         
    <div align="center">
<?php echo $err_email;
echo $mail_msg; ?><br />             
      <span class="text"><b>          
          <?php echo $lang['email_utenti_descr']; ?></b>      
      </span><br />             
      <form method="post" name="sendmail" action="invia_email_utenti.php?user_id=<?php echo htmlspecialchars($get_user_id, ENT_QUOTES, "ISO-8859-1"); ?>">                 
        <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">                     
          <tr>                            
            <td align="right" class="text" width="23%" bgcolor="#EEEEEE"><?php echo $lang['oggetto']; ?></td>                         
            <td align="left" bgcolor="#EEEEEE">                             
              <input type="text" name="oggetto" size="30" maxlength="50" value="<?php echo $oggetto_value; ?>" /></td>                     
          </tr>                     
          <tr>                            
            <td align="right" class="text" valign="top" bgcolor="#EEEEEE"><?php echo $lang['destinatari']; ?></td>                         
            <td align="left" bgcolor="#EEEEEE" class="text2">                             
              <select name="email[]" multiple="multiple" id="email" size="6" style="width: 210px">
<?php

//estraggo le email degli utenti selezionati via GET
$ids = explode(",", $get_user_id);

foreach ($ids as $k => $v) {
    
    if (!preg_match('/^[0-9]{1,5}$/', $v)) {
        unset($ids[$k]);
    }
}
$ids2 = implode(",", $ids);
$sel_utenti = mysqli_query($db, "SELECT nome_cognome, email FROM `$tab_utenti` WHERE user_id IN ($ids2) ORDER BY email ASC");

while ($riga = mysqli_fetch_array($sel_utenti)) {
    echo '<option value="' . $riga['email'] . '" selected="selected" title="' . $riga['nome_cognome'] . '">' . $riga['email'] . '</option>';
    echo "\n";
}
?>                             
              </select><br /><?php echo $lang['select']; ?>
              <a href="javascript:void(0)" onclick="listbox_selectall('email', true)" class="piccolo"><?php echo $lang['select_all']; ?></a>,                               
              <a href="javascript:void(0)" onclick="listbox_selectall('email', false)" class="piccolo"><?php echo $lang['select_none']; ?></a><br /><br /></td>                     
          </tr>                     
          <tr>                            
            <td align="right" class="text" valign="top" bgcolor="#EEEEEE"><?php echo $lang['messaggio']; ?></td>                         
            <td align="left" bgcolor="#EEEEEE">
<textarea name="messaggio" cols="31" rows="7"><?php echo $messaggio_value; ?></textarea></td>                     
          </tr>                     
          <tr>                            
            <td align="right" bgcolor="#EEEEEE"></td>                         
            <td align="left" bgcolor="#EEEEEE" class="text2">                             
              <input type="checkbox" name="cb_admin" id="cb_admin" checked="checked" /><label for="cb_admin"><?php echo $lang['email_utenti_descr2']; ?></label><br /><br /></td>                     
          </tr>                     
          <tr>                         
            <td align="center" bgcolor="#EEEEEE" colspan="2">    
			   <input type="hidden" name="post_token" value="<?php echo sha1(session_id()); ?>" />                         
              <input type="submit" name="submit" value="<?php echo $lang['invia_email']; ?>" style="font-weight: bold;" /></td>                     
          </tr>                 
        </table>             
      </form>         
    </div>     
  </body>
</html>
