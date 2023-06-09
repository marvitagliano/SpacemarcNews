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
 
header('Content-type: text/html; charset=UTF-8');

//includo i file di configurazione
require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

//connessione a MySQL
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

//se non viene inviato l'id dell'autore oppure non è valido

if (!isset($_GET['user_id']) || !preg_match('/^[0-9]{1,5}$/', $_GET['user_id'])) {
    die("No user");
}
else {
    $user_id = intval($_GET['user_id']);
	$result = @mysqli_query($db, "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.mostra_link, nu.email_nascosta, nu.sito, nu.im, nu.im_num, nu.facebook, nu.twitter, nu.instagram, nu.data_nascita, nu.citta, nu.occupazione, nu.hobby, nl.nome_livello, (SELECT COUNT(id) FROM `$tab_news` WHERE user_id=$user_id AND news_approvata = 1 AND data_pubb < " . time() . ") AS TotaleNews, (SELECT ROUND(COUNT(id) / (DATEDIFF(NOW(), MIN(FROM_UNIXTIME(data_pubb)))+1), 1) FROM `$tab_news` WHERE user_id=$user_id AND news_approvata = 1 AND data_pubb < " . time() . ") AS MediaGiornaliera FROM `$tab_utenti` nu JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id WHERE nu.user_id=$user_id LIMIT 1");
    $row = @mysqli_fetch_array($result);
    
    if ($row['mostra_link'] != 'profilo') {
        die("No user");
    }
    $sito = ($row['sito'] == '' || $row['sito'] == 'http://') ? $lang['non_disponibile'] : '<a href="' . $row['sito'] . '" target="blank">' . $row['sito'] . '</a>';
    $im_num = ($row['im'] == '-' || $row['im_num'] == '') ? NULL : $row['im_num'];
    
    switch ($row['im']) {
        case 'icq':
            $im_link = '<img src="' . $img_path . '/icq.png" alt="icq" title="ICQ" /> ' . $im_num;
        break;
        case 'skype':
            $im_link = '<img src="' . $img_path . '/skype.png" alt="skype" title="Skype" /> <a href="skype:' . $im_num . '?chat" title="Skype">' . $im_num . '</a>';
        break;
        case 'whatsapp':
            $im_link = '<img src="' . $img_path . '/whatsapp.png" alt="whatsapp" title="Whatsapp" /> ' . $im_num . '';
        break;
        case 'telegram':
            $im_link = '<img src="' . $img_path . '/telegram.png" alt="telegram" title="Telegram" /> <a href="https://telegram.me/' . $im_num . '" target="_blank" title="Telegram">' . $im_num . '</a>';
        break;
		case 'signal':
            $im_link = '<img src="' . $img_path . '/signal.png" alt="signal" title="Signal" /> ' . $im_num;
        break;     
        case '-':
            $im_link = "N/A";
        break;
    }
    $facebook = ($row['facebook'] == NULL) ? '' : '<img src="' . $img_path . '/facebook.png" alt="Facebook" /> <a href="https://www.facebook.com/' . $row['facebook'] . '" title="Facebook" target="_blank">' . $row['facebook'] . '</a></br>';
    $twitter = ($row['twitter'] == NULL) ? '' : '<img src="' . $img_path . '/twitter.png" alt="Twitter" /> <a href="https://twitter.com/' . $row['twitter'] . '" title="Twitter" target="_blank">' . $row['twitter'] . '</a></br>';
    $instagram = ($row['instagram'] == NULL) ? '' : '<img src="' . $img_path . '/instagram.png" alt="Instagram" /> <a href="https://instagram.com/' . $row['instagram'] . '" title="Instagram" target="_blank">' . $row['instagram'] . '</a>';    
    $data_nascita = ($row['data_nascita'] == NULL) ? $lang['non_disponibile'] : $row['data_nascita'];
    $citta = ($row['citta'] == '') ? $lang['non_disponibile'] : $row['citta'];
    $occupazione = ($row['occupazione'] == '') ? $lang['non_disponibile'] : $row['occupazione'];
    $hobby = ($row['hobby'] == '') ? $lang['non_disponibile'] : $row['hobby'];
    $user_news_perday = ($row['MediaGiornaliera'] == 0) ? '' : ' (' . $row['MediaGiornaliera'] . ' ' . $lang['per_giorno'] . ')';    
    mysqli_close($db);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['autore']; ?>
    </title>         
    <link rel="stylesheet" href="style.css" type="text/css" />     
  </head>     
  <body>         
    <div align="center">             
      <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">                 
        <tr>                     
          <td align="right" class="text" bgcolor="#F1F1F1" width="38%">                         
            <img src="<?php echo $img_path; ?>/profilo.png" alt="" /></td>                     
          <td class="text" bgcolor="#F1F1F1" align="left"><b>                             
              <?php echo $row['nome_cognome']; ?></b></td>                 
        </tr>                 
        <tr>                     
          <td align="right" class="text" bgcolor="#DEE3E7"><b><?php echo $lang['livello']; ?></b></td>                     
          <td class="text" bgcolor="#EEEEEE" align="left">                         
            <?php echo $row['nome_livello']; ?></td>                 
        </tr>                 
        <tr>                        
          <td align="right" class="text" bgcolor="#DEE3E7"><b><?php echo $lang['newsinserite']; ?></b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo $row['TotaleNews']; ?> <?php echo $user_news_perday; ?>                           
            <a href="javascript:opener.location=('archivio.php?autore=<?php echo $row['user_id']; ?>'); self.close();"><?php echo $lang['leggi']; ?></a></td>                 
        </tr>                 
        <tr>                        
          <td align="right" class="text" bgcolor="#DEE3E7"><b><?php echo $lang['mostraemail']; ?></b></td>                     
          <td class="text" bgcolor="#EEEEEE" align="left">    
<?php

if ($row['email_nascosta'] == 1) {
    echo $lang['non_disponibile'];
}
else {
    $chars_email = array(
        "@",
        "."
    );
    $chars_email_replace = array(
        " <b>at</b> ",
        " <b>dot</b> "
    );
    echo str_replace($chars_email, $chars_email_replace, $row['email']);
}
?></td>                 
        </tr>                 
        <tr>                        
          <td align="right" class="text" bgcolor="#DEE3E7"><b><?php echo $lang['mostrasito']; ?></b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo $sito; ?></td>                 
        </tr>                 
        <tr>                        
          <td align="right" valign="top" class="text" bgcolor="#DEE3E7"><b>Instant messaging</b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo $im_link; ?></td>                 
        </tr>          
        <tr>                        
          <td align="right" valign="top" class="text" bgcolor="#DEE3E7"><b>Social Network</b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo $facebook . " " . $twitter . " " . $instagram; ?></td>                 
        </tr>           
        <tr>                     
          <td align="right" class="text" bgcolor="#DEE3E7"><b><?php echo $lang['data_nascita']; ?></b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo $data_nascita; ?></td>                 
        </tr>                 
        <tr>                        
          <td align="right" class="text" bgcolor="#DEE3E7"><b><?php echo $lang['citta']; ?></b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo $citta; ?></td>                 
        </tr>                 
        <tr>                        
          <td align="right" valign="top" class="text" bgcolor="#DEE3E7"><b><?php echo $lang['lavoro']; ?></b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo $occupazione; ?></td>                 
        </tr>                 
        <tr>                     
          <td align="right" class="text" bgcolor="#DEE3E7" valign="top"><b><?php echo $lang['hobby']; ?></b></td>                     
          <td bgcolor="#EEEEEE" class="text" align="left">                         
            <?php echo nl2br($hobby); ?></td>                 
        </tr>             
      </table>         
    </div>     
  </body>
</html>
