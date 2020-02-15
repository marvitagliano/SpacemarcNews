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
check_form();

if ( isset($_GET['id_comm']) && preg_match('/^[0-9]{1,10}$/', $_GET['id_comm']) && isset ($_SESSION['livello_id']) ) {
    $idcomm = intval($_GET['id_comm']);
}
else {
    die("Error");
}

if ( $_SESSION['livello_id'] == 3 ) {
	$where_livello = "AND email_autore = '" . $_SESSION['email'] . "'";
} else {
	$where_livello = '';
}

//estraggo i dati del commento
$view_comm = mysqli_query($db, "SELECT approvato, commento, autore, email_autore, sito_autore, ip_autore FROM `$tab_commenti` WHERE id_comm=$idcomm $where_livello");
$q_riga = mysqli_fetch_assoc($view_comm);

//se l'ID del commento selezionato non esiste in tabella

if (mysqli_num_rows($view_comm) == 0) {
    die("No comment");
}

//vedo se il commento appartiene ad un amministratore/redattore o ad un visitatore
$view_email = mysqli_query($db, "SELECT email FROM `$tab_utenti` WHERE email='" . $q_riga['email_autore'] . "'");
$q_riga_email = mysqli_fetch_assoc($view_email);

if (mysqli_num_rows($view_email) == 0) {
	$readonly = '';
} else {
	$readonly = 'readonly="readonly"';
}
	
	$autore_value = $q_riga['autore'];
	$email_value = $q_riga['email_autore'];
	$sito_value = $q_riga['sito_autore'];
	$commento_value = $q_riga['commento'];


if ( $_SESSION['livello_id'] == 3 ) {
	
	if ($q_riga['approvato'] == 1) {
		
		$approvato_checked = 'disabled="disabled "checked="checked"';
		
	} else {
			
		$approvato_checked = 'disabled="disabled"';
	
	}
	
} else {
	
	$approvato_checked = ($q_riga['approvato'] == 1) ? 'checked="checked"' : NULL;
	
}

$update_msg = NULL;
$campi_vuoti = NULL;
$errore_email = NULL;

if (isset($_GET['modo']) && $_GET['modo'] === 'sa') {
    $action = 'modifica_commento.php?modo=sa&amp;id_comm=' . $idcomm;
    $close_popup = '<br /><a href="javascript:self.close()"; class="text">' . $lang['chiudi'] . '</a></div>';
}
else {
    $action = 'modifica_commento.php?id_comm=' . $idcomm;
    $close_popup = '<br /><a href="javascript:;" onclick="close_and_go();" class="text">' . $lang['chiudi_popup'] . '</a></div>';
}

if (isset($_POST['submit'])) {

    //cancella commento
    
    if (isset($_POST['cbcancella'])) {
        
        if (mysqli_query($db, "DELETE FROM `$tab_commenti` WHERE id_comm=$idcomm $where_livello LIMIT 1")) {
            $update_msg = '<div id="success">' . $lang['modifica_commento_ok'] . '<br />' . $close_popup;
        }
        else {
            $update_msg = '<div id="error">' . $lang['modifica_commento_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
        }
    }
    else {
        $author = htmlspecialchars($_POST['author'], ENT_QUOTES, "ISO-8859-1");
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, "ISO-8859-1");
        $url = htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1");
        $commento = htmlspecialchars($_POST['commento'], ENT_QUOTES, "ISO-8859-1");
        
        $approvato = (isset($_POST['cbapprovato'])) ? 1 : 0;

        if ( $_SESSION['livello_id'] == 3 ) {
			$where_approvato = '';
		} else {
			$where_approvato = ", approvato = $approvato";
		}
		        
        if (trim($author) == '' || trim($commento) == '') {
            $campi_vuoti = '<div id="error">' . $lang['commenti_campi_obb'] . '</div><br />';
        }
        else {
            $campi_vuoti = NULL;
            
            if (!preg_match('/^[.a-z0-9_-]+@[.a-z0-9_-]+\.[a-z]{2,4}$/', $email)) {
                $errore_email = '<div id="error">' . $lang['wrong_email'] . '</div><br />';
            }
            else {
                $errore_email = NULL;
                
                if (trim($url) != '' && !(stristr($url, 'http://'))) {
                    $url = 'http://' . $url;
                }

                $author = mysqli_real_escape_string($db, trim($author));
                $email = mysqli_real_escape_string($db, $email);
                $url = mysqli_real_escape_string($db, trim($url));
                $commento = mysqli_real_escape_string($db, $commento);
                
                if (mysqli_query($db, "UPDATE `$tab_commenti` SET autore='$author', email_autore='$email', sito_autore='$url', commento='$commento' $where_approvato WHERE id_comm=$idcomm $where_livello")) {
                    $update_msg = '<div id="success">' . $lang['modifica_commento_ok'] . ' ' . $close_popup . '<br />';
                }
                else {
                    $update_msg = '<div id="error">' . $lang['modifica_commento_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
                }
            }
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['modifica_commento']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>      
  </head>     
  <body>
    <div align="center">  
<?php
echo $update_msg . $campi_vuoti . $errore_email;
?>    
      <form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" name="input_form">       
        <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">                     
          <tr>                            
            <td align="right" class="text" width="22%" bgcolor="#EEEEEE"><?php echo $lang['autore']; ?></td>                         
            <td align="left" bgcolor="#EEEEEE">
              <input type="text" name="author" value="<?php echo $autore_value; ?>" <?php echo $readonly; ?> size="26" maxlength="40" /> <img src="<?php echo $img_path; ?>/ip.png" alt="IP" title="IP: <?php echo $q_riga['ip_autore']; ?>" /></td>          
          </tr>          
          <tr>                            
            <td align="right" class="text" bgcolor="#EEEEEE">Email</td>                         
            <td align="left" bgcolor="#EEEEEE" class="text2">
              <input type="text" name="email" value="<?php echo $email_value; ?>" <?php echo $readonly; ?> size="30" maxlength="50" /></td>          
          </tr>          
          <tr>                            
            <td align="right" class="text" bgcolor="#EEEEEE"><?php echo $lang['sitoweb_commento']; ?></td>                         
            <td align="left" bgcolor="#EEEEEE">
              <input type="text" name="url" value="<?php echo $sito_value; ?>" <?php echo $readonly; ?> size="30" maxlength="50" /></td>          
          </tr>          
          <tr>                            
            <td align="right" class="text" bgcolor="#EEEEEE"><?php echo $lang['testo']; ?></td>                         
            <td align="left" bgcolor="#EEEEEE">
<textarea name="commento" cols="40" rows="8"><?php echo $commento_value; ?></textarea></td>          
          </tr>         
          <tr>                            
            <td bgcolor="#EEEEEE"></td>                         
            <td align="left" bgcolor="#EEEEEE" class="text">
              <input type="checkbox" id="cbcancella" name="cbcancella" onclick="if (this.checked) { alert('<?php echo $lang['attenzione_commento']; ?>'); }" /><label for="cbcancella"><?php echo $lang['delete']; ?></label> 
              <input type="checkbox" id="cbapprovato" name="cbapprovato" <?php echo $approvato_checked; ?> /><label for="cbapprovato"><?php echo $lang['approva']; ?></label> </td>          
          </tr>					
          <tr>                         
            <td align="center" bgcolor="#EEEEEE" colspan="2">
			  <input type="hidden" name="post_token" value="<?php echo sha1(session_id()); ?>" />                             
              <input type="submit" name="submit" value="<?php echo $lang['modifica']; ?>" style="font-weight: bold;" /></td>                     
          </tr>    
        </table>    
      </form>
    </div>  
  </body>
</html>
