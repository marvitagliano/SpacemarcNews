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
require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/admin/functions.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

// connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

//se c'è l'id della notizia inviato via GET ed è di tipo numerico ed è presente in tabella vado avanti
$get_id = (isset($_GET['id']) && preg_match('/^[0-9]{1,8}$/', $_GET['id'])) ? intval($_GET['id']) : 0;
$errore = NULL;
$errore_captcha = NULL;
$inviata = NULL;
$mittente_value = NULL;
$mail_mittente_value = NULL;
$mail_amico_value = NULL;

$result = @mysqli_query($db, "SELECT nt.id, nt.titolo, nc.nome_sito, nc.url_sito FROM `$tab_config` nc, `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id = nt.user_id WHERE nt.id=$get_id AND nt.news_approvata = 1 AND nt.data_pubb < " . time() . "");
$row = @mysqli_fetch_array($result);

if (@mysqli_num_rows($result) == 0) {
    die("No news");
}

if (isset($_POST['submit'])) {
	
	$mittente_value = (isset($_POST['mittente'])) ? htmlspecialchars($_POST['mittente'], ENT_QUOTES, "ISO-8859-1") : NULL;
	$mail_mittente_value = (isset($_POST['mail_mittente'])) ? htmlspecialchars($_POST['mail_mittente'], ENT_QUOTES, "ISO-8859-1") : NULL;
	$mail_amico_value = (isset($_POST['mail_amico'])) ? htmlspecialchars($_POST['mail_amico'], ENT_QUOTES, "ISO-8859-1") : NULL;

    if (!preg_match('/^[.a-zA-Z0-9\-\'\s]{1,20}$/', $_POST['mittente']) || !preg_match('/^[.a-z0-9_-]+@[.a-z0-9_-]+\.[a-z]{2,4}$/', $_POST['mail_mittente']) || !preg_match('/^[.a-z0-9_-]+@[.a-z0-9_-]+\.[a-z]{2,4}$/', $_POST['mail_amico'])) {
        $errore = '<div id="error">' . $lang['compila_correttamente'] . '</div>';
    }
    else {
        
        if ($_POST['spamcode'] != @$_SESSION['antispam'] || !empty($_POST['web'])) {
            $errore_captcha = '<div id="error">' . $lang['antispam_error'] . '</div>';
        }
        else {
            $titolo2 = html_entity_decode($row['titolo'], ENT_QUOTES, 'ISO-8859-1');
            $titoloChars = array(
                '&rsquo;' => '\'',
                '&euro;' => 'E'
            );
            $titolo3 = strtr($titolo2, $titoloChars);
            $errore = NULL;
            $messaggio = "\n-- \n" . $lang['leggi_news_disclaimer'] . " " . $row['nome_sito'] . ". " . $lang['leggi_news_disclaimer2'];
            $phpversion = (!@phpversion()) ? "N/A" : phpversion();
            $header = "From: " . $_POST['mittente'] . " <" . $_POST['mail_mittente'] . ">\n";
            $header.= "Reply-To: " . $_POST['mail_mittente'] . "\n";
            $header.= "Return-Path: " . $_POST['mail_mittente'] . "\n";
            $header.= "X-Mailer: PHP " . $phpversion . "\n";
            $header.= "MIME-Version: 1.0\n";
            $header.= "Content-type: text/plain; charset=ISO-8859-1\n";
            $header.= "Content-Transfer-encoding: 7bit\n";
            
            if (@mail($_POST['mail_amico'], $lang['leggi_news1'] . " " . $row['nome_sito'] . "", $lang['ciao'] . ",\n" . $_POST['mittente'] . " " . $lang['leggi_news2'] . ": $titolo2 \n\n" . $lang['leggi_news3'] . ": " . $row['url_sito'] . "/$news_dir/view.php?id=" . $row['id'] . "\n\n$messaggio", $header)) {
                $inviata = '<div id="success">' . $lang['email_utenti_ok'] . ' <script language="JavaScript" type="text/JavaScript">setTimeout(\'window.close()\', 2000)</script></div>';
            }
            else {
                $inviata = '<div id="error">' . $lang['email_utenti_error'] . '</div>';
            }
        }
    }
}
mysqli_close($db);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>              
    <title>Email
    </title>              
    <link rel="stylesheet" href="style.css" type="text/css" />        
  </head>        
  <body>              
    <div align="center">
<?php echo $errore;
echo $errore_captcha;
echo $inviata; ?>                    
      <span class="text2">                          
        <img src="<?php echo $img_path; ?>/mail.png" alt="" /> <b>                                
          <?php echo $row['titolo']; ?></b>                    
      </span><br /><br />                    
      <form method="post" action="segnala.php?id=<?php echo $row['id']; ?>" id="form_send">                          
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="text2">                                
          <tr>                                      
            <td align="right" width="33%"><?php echo $lang['segnala_nome']; ?> *</td>                                      
            <td align="left">                                               
              <input type="text" name="mittente" size="20" maxlength="20" value="<?php echo $mittente_value; ?>" /></td>                                
          </tr>                                
          <tr>                                      
            <td align="right"><?php echo $lang['segnala_email']; ?> *</td>                                      
            <td align="left">                                               
              <input type="text" name="mail_mittente" size="20" maxlength="50" value="<?php echo $mail_mittente_value; ?>" /></td>                                
          </tr>                                
          <tr>                                         
            <td align="right"><?php echo $lang['segnala_email_dest']; ?> *</td>                                      
            <td align="left">                                               
              <input type="text" name="mail_amico" size="20" maxlength="50" value="<?php echo $mail_amico_value; ?>" /></td>                                
          </tr>
          <tr>                                      
            <td align="right">&nbsp;</td>                                      
            <td align="left"><br /><?php echo captcha(); ?> <input name="spamcode" type="text" size="5" maxlength="6" /> <input type="text" name="web" size="10" value="" class="hp" /><input type="submit" name="submit" value="<?php echo $lang['invia_email']; ?>" style="font-weight: bold;" /></td>                                
          </tr>                          
        </table>                    
      </form>              
    </div>        
  </body>
</html>
