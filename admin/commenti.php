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

if ($_SESSION['livello_id'] != 1) {
    header('Location: ' . $dir_admin . '/elenco_news.php');
    exit();
}

check_form();

$conf = mysqli_query($db, "SELECT max_gest_comm, formato_data FROM `$tab_config`");
$rowconf = mysqli_fetch_array($conf);

// risultati visualizzati per pagina
$rec_page = $rowconf['max_gest_comm'];
$query_msg = NULL;
$query_msg_ban = NULL;

if (isset($_GET['id_news']) && preg_match('/^[0-9]{1,8}$/', $_GET['id_news'])) {
    $q_id = " WHERE id_news=" . intval($_GET['id_news']) . "";
    $get_id = "&amp;id_news=" . intval($_GET['id_news']) . "";
    $query_count = "SELECT COUNT(id_news) AS NumTotale FROM `$tab_commenti` WHERE id_news=" . intval($_GET['id_news']);
    $action1 = '&id_news=' . intval($_GET['id_news']) . '';
}
else {
    $q_id = NULL;
    $get_id = NULL;
    $query_count = "SELECT COUNT(id_comm) AS NumTotale FROM `$tab_commenti`";
    $action1 = '';
}

//controllo per action del form e refresh dopo la query
if  ( isset($_GET['start']) ) {
    $start = abs(floor(intval($_GET['start'])));
	$action2 = '&start=' . intval($_GET['start']) . '';
    
	} else {
	$start = 0;
	$action2 = '';
}

//vedo se c'è sortby via GET... NO paginazione, solo per costruire i link

if (isset($_GET['sortby'])) {
    $get_sortby = "sortby=" . addslashes($_GET['sortby']);
	$action3 = '&sortby=' . $_GET['sortby'] . '';
        
    switch ($_GET['sortby']) {
        case 'id_comm_asc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY id_comm ASC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . ' </a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'id_comm_desc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY id_comm DESC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_asc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'id_news_asc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY id_news ASC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'id_news_desc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY id_news DESC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_asc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'approvato_asc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY approvato ASC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'approvato_desc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY approvato DESC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_asc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'autore_asc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY autore ASC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'autore_desc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY autore DESC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_asc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a>';
        break;
        case 'data_asc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY data_comm ASC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
        break;
        case 'data_desc':
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY data_comm DESC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_asc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
        break;
        default:
            $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY data_comm ASC LIMIT $start,$rec_page";
            $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
            $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
            $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
            $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="commenti.php?sortby=data_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
    }
}
else {
    $get_sortby = NULL;
	$action3 = '';
    $order_query = "SELECT id_comm, id_news, approvato, LEFT(commento, 68) AS commento, autore, data_comm, email_autore, sito_autore, ip_autore FROM `$tab_commenti` " . $q_id . " ORDER BY data_comm DESC LIMIT $start,$rec_page";
    $link_id_comm = '<a href="commenti.php?sortby=id_comm_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['commenti'] . '</a>';
    $link_id_news = '<a href="commenti.php?sortby=id_news_desc&amp;start=' . $start . '' . $get_id . '">ID ' . $lang['news'] . '</a>';
    $link_approvato = '<a href="commenti.php?sortby=approvato_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['approvato'] . '</a>';
    $link_autore = '<a href="commenti.php?sortby=autore_desc&amp;start=' . $start . '' . $get_id . '">' . $lang['autore'] . '</a>';
    $link_data = '<a href="commenti.php?sortby=data_asc&amp;start=' . $start . '' . $get_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
}

if (isset($_POST['submit_news'])) {
    
    if (isset($_POST['cb_id'])) {
		
        $nid = implode(",", $_POST['cb_id']);

        // cancello i commenti delle news
        
        if ($_POST['submit_sel'] == 'canc_comm') {
            
            if (mysqli_query($db, "DELETE FROM `$tab_commenti` WHERE id_comm IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['canc_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh: 2; url=commenti.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['canc_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        // approvo i commenti selezionati
        
        if ($_POST['submit_sel'] == 'approva_comm') {
            
            if (mysqli_query($db, "UPDATE `$tab_commenti` SET approvato=1 WHERE approvato=0 AND id_comm IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['approva_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh: 2; url=commenti.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['approva_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        // disapprovo i commenti selezionati
        
        if ($_POST['submit_sel'] == 'disapprova_comm') {
            
            if (mysqli_query($db, "UPDATE `$tab_commenti` SET approvato=0 WHERE approvato=1 AND id_comm IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['disapprova_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh: 2; url=commenti.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['disapprova_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }
    }
    else {
        $nid = NULL;
        $query_msg = '<div id="error">' . $lang['selez_comm_error'] . '</div><br />';
    }
}

//inserimento termini ban
elseif (isset($_POST['inserisci_parola'])) {
    
    if (!preg_match('/^([.:\/a-zA-Z0-9- ]{1,255})$/', $_POST['parola'])) {
        $query_msg_ban = '<div id="error">' . $lang['errore_parola_ban'] . '</div><br />';
    }
    else {
        mysqli_query($db, "INSERT INTO `$tab_ban` (ban_word, dataora) VALUES ('" . $_POST['parola'] . "', " . time() . ")");
        $query_msg_ban = '<div id="success">' . $lang['parola_ban_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
        header("Refresh: 2; url=commenti.php");
    }
}

//rimozione termini ban
elseif (isset($_POST['rimuovi_parola'])) {
    
    if (empty($_POST['ban_words'])) {
        $query_msg_ban = '<div id="error">' . $lang['errore_rimozione_parola_ban'] . '</div><br />';
    }
    else {
        $_POST['ban_words'] = implode(',', $_POST['ban_words']);
        mysqli_query($db, "DELETE FROM `$tab_ban` WHERE id_ban IN (" . $_POST['ban_words'] . ")");
        $query_msg_ban = '<div id="success">' . $lang['rimozione_parola_ban_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
		header("Refresh: 2; url=commenti.php");
    }
}

//inserimento IP ban
elseif (isset($_POST['inserisci_ip'])) {
    
    if (!preg_match('/^([.*0-9-]{7,31})$/', $_POST['ip'])) {
        $query_msg_ban = '<div id="error">' . $lang['errore_ip_ban'] . '</div><br />';
    }
    else {

        if (stristr($_POST['ip'], '-')) {

            //preparo il range di IP per la query
            $ip1 = explode('-', $_POST['ip']);
            $ip2 = explode('.', $ip1[0]);
            $ip3 = explode('.', $ip1[1]);
            $ip4 = explode('.', $ip1[0]);
            $ip5 = $ip4[0] . '.' . $ip4[1] . '.' . $ip4[2] . '.';
            
            for ($i = $ip2[3]; $i <= $ip3[3]; ++$i) {
                mysqli_query($db, "INSERT INTO `$tab_ban` (ban_ip, dataora, white_list) VALUES ('" . $ip5 . $i . "', " . time() . ", 0)");
            }
        }
        else {
            mysqli_query($db, "INSERT INTO `$tab_ban` (ban_ip, dataora, white_list) VALUES ('" . $_POST['ip'] . "', " . time() . ", 0)");
        }
        $query_msg_ban = '<div id="success">' . $lang['ip_ban_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
		header("Refresh: 2; url=commenti.php");
    }
}

//rimozione IP ban
elseif (isset($_POST['rimuovi_ip'])) {
    
    if (empty($_POST['ban_ips'])) {
        $query_msg_ban = '<div id="error">' . $lang['errore_rimozione_ip_ban'] . '</div><br />';
    }
    else {
        $_POST['ban_ips'] = implode(',', $_POST['ban_ips']);
        mysqli_query($db, "DELETE FROM `$tab_ban` WHERE id_ban IN (" . $_POST['ban_ips'] . ") AND white_list = 0");
        $query_msg_ban = '<div id="success">' . $lang['rimozione_ip_ban_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
		header("Refresh: 2; url=commenti.php");        
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['commenti']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>      
  </head>     
  <body>
<?php
require_once ("menu.php");
echo $query_msg;
echo '<form name="admin" action="commenti.php?' . $action1 . $action2 . $action3 . '" method="post">
<table width="100%" style="border: 3px solid #DDDDDD;" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF" align="center">
<tr><td width="1%" align="center" bgcolor="#EEEEEE"><img src="' . $img_path . '/comm.png" border="0" alt="" /></td>
<td width="20%" class="text" align="center" bgcolor="#EEEEEE">' . $link_id_comm . '</td>
<td width="4%" class="text" align="center" bgcolor="#EEEEEE">' . $link_id_news . '</td>
<td width="9%" class="text" align="center" bgcolor="#EEEEEE">' . $link_data . '</td>
<td width="10%" class="text" align="center" bgcolor="#EEEEEE">' . $link_autore . '</td>
<td width="4%" class="text" align="center" bgcolor="#EEEEEE">' . $link_approvato . '</td>
<td width="2%" class="text" align="center" bgcolor="#EEEEEE">' . $lang['opzioni'] . '</td>
</tr>';
$q_order = mysqli_query($db, "$order_query");

while ($q_riga = mysqli_fetch_array($q_order)) {

    //seleziono il formato data
    
    switch ($rowconf['formato_data']) {
        case 1:
            $data = strftime("%a %d %b %Y, %H:%M", $q_riga['data_comm']);
        break;
        case 2:
            $data = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $q_riga['data_comm']));
        break;
        case 3:
            $data = strftime("%d/%m/%Y, %H:%M", $q_riga['data_comm']);
        break;
        case 4:
            $data = strftime("%d %b %Y, %H:%M", $q_riga['data_comm']);
        break;
        case 5:
            $data = strftime("%d %B %Y, %H:%M", $q_riga['data_comm']);
        break;
        case 6:
            $data = strftime("%m/%d/%Y, %I:%M %p", $q_riga['data_comm']);
        break;
        case 7:
            $data = strftime("%B %d, %Y %I:%M %p", $q_riga['data_comm']);
        break;
        case 8:
            $data = strftime("%I:%M %p %B %d, %Y", $q_riga['data_comm']);
        break;
    }

    //controllo se c'è la notizia a cui appartiene il commento e se l'autore è autorizzato alla sua pubblicazione
    $sql_news = mysqli_query($db, "SELECT DISTINCT nt.id, nt.titolo, nt.news_approvata FROM `$tab_news` nt JOIN `$tab_commenti` nco ON nco.id_news=nt.id JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id WHERE nt.id=nco.id_news AND nt.id=" . $q_riga['id_news']);
    $riga_news = mysqli_fetch_array($sql_news);
    
    if (mysqli_num_rows($sql_news) == 1 && $riga_news['news_approvata'] == 1) {
        $link_news = '<a href="../view.php?id=' . $q_riga['id_news'] . '" target="_blank" title="' . $riga_news['titolo'] . '">' . $q_riga['id_news'] . '</a>';
    }
    elseif (mysqli_num_rows($sql_news) == 1 && $riga_news['news_approvata'] == 0) {
        $link_news = '<a href="modifica.php?id=' . $q_riga['id_news'] . '" target="_blank" title="' . $riga_news['titolo'] . '">' . $q_riga['id_news'] . '</a>';
    }
    else {
        $link_news = '<span class="help2" style="color: #AAAAAA; cursor: help;" title="' . $lang['news_cancellata'] . '">' . $q_riga['id_news'] . '</span>';
    }
    $sito = ($q_riga['sito_autore'] != '') ? '<img src="' . $img_path . '/www.png" alt="WWW" title="Web: ' . $q_riga['sito_autore'] . '" />' : '';
    $approvato = ($q_riga['approvato'] == 1) ? '&#9989;' : '&#9940;';
    $testo_comm = ($q_riga['approvato'] == 1) ? $q_riga['id_comm'] . ' - ' . $q_riga['commento'] . '...' : '<span style="color: #AAAAAA">' . $q_riga['id_comm'] . ' - ' . $q_riga['commento'] . '...</span>';
    echo '<tr onmouseover="this.bgColor=\'#E6F1FA\'" onmouseout="this.bgColor=\'#FFFFFF\'">
                      <td align="center"><input type="checkbox" name="cb_id[]" value="' . $q_riga['id_comm'] . '" id="id_' . $q_riga['id_comm'] . '" /></td>
                      <td align="left" class="text"><label for="id_' . $q_riga['id_comm'] . '">' . $testo_comm . '</label></td>
                      <td align="center" class="text">' . $link_news . '</td>
                      <td align="left" class="text">' . $data . '</td>
                      <td align="left" class="text">' . $q_riga['autore'] . ' <img src="' . $img_path . '/mail.png" alt="Email" title="Email: ' . $q_riga['email_autore'] . '" /> <img src="' . $img_path . '/ip.png" alt="IP" title="IP: ' . $q_riga['ip_autore'] . '" /> ' . $sito . '</td>
                      <td align="center" class="text">' . $approvato . '</td>
                      <td align="center" class="text"><a href="javascript:;" onclick="window.open(\'modifica_commento.php?id_comm=' . $q_riga['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['modifica'] . '</a></td>
                      </tr>';
}
echo '<tr>
  <td colspan="3" bgcolor="#EEEEEE" class="text2" align="left">
' . $lang['select'] . ' <a href="javascript:onClick=checkTutti()" class="piccolo">' . $lang['select_all'] . '</a>, <a href="javascript:onClick=uncheckTutti()" class="piccolo">' . $lang['select_none'] . '</a>&nbsp;
<select name="submit_sel">
    <option selected="selected">' . $lang['operazioni'] . '</option>
    <option value="approva_comm">' . $lang['approva_commenti'] . '</option>
    <option value="disapprova_comm">' . $lang['disapprova_commenti'] . '</option>
    <option value="canc_comm" style="background:red; color:white;">' . $lang['cancella_commenti'] . ' &#9888; </option>    
</select> <input type="submit" name="submit_news" value="' . $lang['vai'] . '" onclick="return confirmSubmit();" style="font-weight: bold;" /> <input type="hidden" name="post_token" value="' . sha1(session_id()) . '" /> 
</td>
<td colspan="4" bgcolor="#EEEEEE" class="text2" align="right">';

//paginazione
$sql_num_totale = mysqli_query($db, "$query_count");
$num_totale_riga = mysqli_fetch_array($sql_num_totale);
$numero_pagine = ceil($num_totale_riga['NumTotale'] / $rec_page);
$pagina_attuale = ceil(($start / $rec_page) + 1);
echo '<b>(' . $lang['totale'] . ' ' . $num_totale_riga['NumTotale'] . ')</b> ' . page_bar("commenti.php?$get_sortby$get_id", $pagina_attuale, $numero_pagine, $rec_page);
echo '</td></tr></table>';
?>         
    </form><br /><br />
	<?php echo $query_msg_ban; ?>
	<a name="form_ban"></a>
	<form name="ban" action="commenti.php#form_ban" method="post">
    <fieldset>                             
    <legend class="text2"><b><?php echo $lang['parole_ip_descr']; ?></b></legend><br />
	<table width="100%" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF" align="center">
	<tr>
	<td class="text2">
	<input type="text" name="parola" size="25" maxlength="255" /> <input type="submit" name="inserisci_parola" value="<?php echo $lang['btn_insert']; ?>" style="font-weight: bold;" /> (.:/a-zA-Z0-9-&lt;space&gt;) <br />
	<select name="ban_words[]" multiple="multiple" id="ban_words" size="8" style="width: 230px">
	<?php
$sel_ban_words = mysqli_query($db, "SELECT id_ban, ban_word, dataora FROM `$tab_ban` WHERE ban_word IS NOT NULL ORDER BY ban_word ASC");

while ($riga_ban = mysqli_fetch_array($sel_ban_words)) {
    echo '<option value="' . $riga_ban['id_ban'] . '" title="' . date('d/m/Y H:i', $riga_ban['dataora']) . '">' . $riga_ban['ban_word'] . '</option>';
    echo "\n";
}
?>                             
	</select>
	<br /><?php echo $lang['select']; ?>                           
	<a href="javascript:void(0)" onclick="listbox_selectall('ban_words', true)" class="piccolo"><?php echo $lang['select_all']; ?></a>,                               
	<a href="javascript:void(0)" onclick="listbox_selectall('ban_words', false)" class="piccolo"><?php echo $lang['select_none']; ?></a> &nbsp; 
	<input type="submit" name="rimuovi_parola" value="<?php echo $lang['delete']; ?>" onclick="return confirmSubmit();" style="font-weight: bold;" />
	</td>                     
	<td class="text2">
	<input type="text" name="ip" size="25" maxlength="31" /> <input type="submit" name="inserisci_ip" value="<?php echo $lang['btn_insert']; ?>" style="font-weight: bold;" /> (<span style="cursor: help; border-bottom: 1px dotted #000;" title="195.20.205.* 195.20.*.* 195.*.*.*  195.20.205.9-195.20.205.50"><?php echo $lang['ip_range']; ?></span>)<br />
	<select name="ban_ips[]" multiple="multiple" id="ban_ips" size="8" style="width: 230px">
<?php

	if  ( isset($_GET['sortbyip']) && $_GET['sortbyip'] == 'ip' ) {
		$sql_sortbyip = 'ban_ip, dataora ASC';
	} else {
		$sql_sortbyip = 'dataora DESC';
	}

$sel_ban_ip = mysqli_query($db, "SELECT id_ban, ban_ip, dataora, login_errati FROM `$tab_ban` WHERE ban_ip IS NOT NULL AND white_list=0 ORDER BY " . mysqli_real_escape_string($db, $sql_sortbyip));

while ($riga_ban_ip = mysqli_fetch_array($sel_ban_ip)) {
	$option_descr = ($riga_ban_ip['login_errati'] > 0) ? $lang['login_errato'] : $lang['ban_commento'];
	$bg_ban_login = ($riga_ban_ip['login_errati'] > 0) ? 'style="background-color: #FFA500"' : ''; 
    echo '<option value="' . $riga_ban_ip['id_ban'] . '" title="' . date('d/m/Y H:i', $riga_ban_ip['dataora']) . '" ' . $bg_ban_login . '>' . $riga_ban_ip['ban_ip'] . $option_descr . '</option>';
    echo "\n";
}
?> 
</select>
	<br /><?php echo $lang['select']; ?>
	<a href="javascript:void(0)" onclick="listbox_selectall('ban_ips', true)" class="piccolo"><?php echo $lang['select_all']; ?></a>,                               
	<a href="javascript:void(0)" onclick="listbox_selectall('ban_ips', false)" class="piccolo"><?php echo $lang['select_none']; ?></a> &nbsp; 
	<input type="hidden" name="post_token" value="<?php echo sha1(session_id()); ?>" />
	<input type="submit" name="rimuovi_ip" value="<?php echo $lang['delete']; ?>" onclick="return confirmSubmit();" style="font-weight: bold;" />  
	<?php 
	$sql_tot_ban = mysqli_query($db, "SELECT SUM(CASE WHEN ban_ip IS NOT NULL AND login_errati = 0 AND white_list = 0 THEN 1 ELSE 0 END) AS TotBanIp, SUM(CASE WHEN ban_ip IS NOT NULL AND login_errati > 0 AND white_list = 0 THEN 1 ELSE 0 END) AS TotLoginErrati FROM `$tab_ban`");
	$num_totale_ban = mysqli_fetch_array($sql_tot_ban);
	
	echo $lang['tot_ban_commenti'] . ' ' . $num_totale_ban['TotBanIp'] . ' ' . $lang['tot_login_errati'] . ' ' . $num_totale_ban['TotLoginErrati'] . ' (' . $lang['ordina_per'] . ': <a href="commenti.php?sortbyip=ip">IP</a>, <a href="commenti.php">' . $lang['data'] . '</a>)'; 
	?>	
	</td>
	</tr>
	</table>
    </fieldset>
	</form>
	<br />
    <?php require_once ("footer.php"); ?>      
  </body>
</html>
