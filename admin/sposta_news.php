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
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');

$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);
check_login();

// se NON sono un amministratore e voglio visualizzare questa pagina, stampo l'errore

if ($_SESSION['livello_id'] != 1) {
    die("Error");
}

check_form();

$associa_ok = NULL;

//stampo il nome dell'utente da cui spostare le news

if (isset($_GET['user_id']) && preg_match('/^[0-9]{1,5}$/', $_GET['user_id'])) {
    $riga_autore = mysqli_fetch_array(mysqli_query($db, "SELECT nome_cognome, email FROM `$tab_utenti` WHERE user_id=" . intval($_GET['user_id']) . " ORDER BY nome_cognome ASC"));
    $autore = $riga_autore['nome_cognome'] . ' (' . $riga_autore['email'] . ')';
}
else {
    $autore = NULL;
}

//stampo tutti gli utenti
$result = mysqli_query($db, "SELECT user_id, nome_cognome, email FROM `$tab_utenti` ORDER BY nome_cognome ASC");

//associazione news

if (isset($_POST['submit'])) {
      
    if (isset($_GET['user_id']) && preg_match('/^[0-9]{1,5}$/', $_GET['user_id'])) {
        $q_autore = "WHERE user_id = " . intval($_GET['user_id']);
        $q_ids = '';
    }
    else {
        $q_autore = '';

        //estraggo gli id delle news inviati via GET
        $ids = explode(",", $_GET['id']);
        
        foreach ($ids as $k => $val) {
            
            if (!preg_match('/^[0-9]{1,8}$/', $val)) {
                unset($ids[$k]);
            }
        }
        $ids2 = implode(",", $ids);
        $q_ids = "WHERE id IN($ids2)";
    }
    
    if ($_POST['autore'] == 'scegli') {
        $associa_ok = '<div id="error2">' . $lang['associazione_errore'] . '</div>';
    }
    else {
        
        if (mysqli_query($db, "UPDATE `$tab_news` SET user_id=" . intval($_POST['autore']) . " $q_ids $q_autore")) {
            $associa_ok = '<div id="success">' . $lang['associazione_ok'] . '<br /><a href="javascript:;" onclick="close_and_go();">' . $lang['chiudi_popup'] . '</a></div>';
        }
        else {
            $associa_ok = '<div id="error2">' . $lang['associazione_errore'] . '</div><br />' . mysqli_error($db);
        }
    }
}
mysqli_close($db);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['pagina_sposta']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />      
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>      
  </head>     
  <body>         
    <div align="center">   
<?php

if (isset($_GET['user_id']) && preg_match('/^[0-9]{1,5}$/', $_GET['user_id'])) {
    $action = 'sposta_news.php?user_id=' . $_GET['user_id'] . '';
}
else {
    $action = 'sposta_news.php?id=' . @htmlspecialchars($_GET['id'], ENT_QUOTES, "ISO-8859-1") . '';
}
?> 
      <form name="associa" id="associa" action="<?php echo $action; ?>" method="post">     
        <table width="350" cellpadding="2" cellspacing="2" border="0" class="text">
          <tr>
            <td bgcolor="#EEEEEE">ID <?php echo $lang['news']; ?></td>
            <td bgcolor="#EEEEEE"><?php echo $lang['elenco_utenti']; ?></td>
          </tr>
          <tr>
            <td valign="top" align="left">
<?php
echo $autore;

// vado a capo ogni 5 ID visualizzati
$idnews = @explode(",", $_GET['id']);
$i = 1;
$j = 1;

foreach ($idnews as $value => $v) {
    
    if (!preg_match('/^[0-9]{1,8}$/', $v)) {
        unset($idnews[$value]);
        
        break;
    }
    
    if ($i == 5) {
        echo $v . "<br />";
        $i = 1;
        ++$j;
    }
    else {
        (count($idnews) != $j) ? $v.= "," : $v;
        echo $v;
        ++$i;
        ++$j;
    }
}
?></td>
            <td valign="top">		
              <select name="autore">    
                <option value="scegli" selected="selected"><?php echo $lang['scegli']; ?>
                </option>                 
<?php

while ($row_sel = mysqli_fetch_array($result)) {
    $select_disabled = (isset($_GET['user_id']) && $_GET['user_id'] == $row_sel['user_id']) ? 'disabled="disabled"' : '';
    echo '<option value="' . $row_sel['user_id'] . '" title="' . $row_sel['email'] . '" ' . $select_disabled . '>' . $row_sel['nome_cognome'] . '</option>';
    echo "\n";
}
?> 	
              </select></td>
          </tr>
          <tr>
            <td colspan="2" align="center" class="text2"><br /><br />
              <input type="hidden" name="post_token" value="<?php echo sha1(session_id()); ?>" />
              <input type="submit" name="submit" value="<?php echo $lang['vai']; ?>" style="font-weight: bold;" onclick="return confirmSubmit();" /><br /> <br />
              <?php echo $associa_ok; ?></td>
          </tr>
        </table>
      </form>    
    </div>     
  </body>
</html>
