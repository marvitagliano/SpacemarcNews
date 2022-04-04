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

if ($_SESSION['livello_id'] != 1) {
    header('Location: ' . $dir_admin . '/elenco_news.php');
    exit();
}

check_form();

//estraggo alcune impostazioni
$conf = mysqli_query($db, "SELECT nome_sito, max_utenti, url_sito, formato_data FROM `$tab_config`");
$rowconf = mysqli_fetch_array($conf);
$utente_msg = NULL;
$query_utente_msg = NULL;
$utente_presente = NULL;
$email_errata = NULL;
$popup_email = NULL;

// risultati visualizzati per pagina
$rec_page = $rowconf['max_utenti'];
$query_msg = NULL;
$del_ok = NULL;

//controllo per action del form e refresh dopo la query
if  ( isset($_GET['start']) ) {
    $start = abs(floor(intval($_GET['start'])));
	$action1 = '&start=' . intval($_GET['start']) . '';
    
	} else {
	$start = 0;
	$action1 = '';
}

if (isset($_GET['sortby'])) {
    $get_sortby = "sortby=" . addslashes($_GET['sortby']);
    $action2 = '&sortby=' . $_GET['sortby'] . '';
    
    switch ($_GET['sortby']) {
        case 'nome_asc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.nome_cognome ASC LIMIT $start,$rec_page";
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_email = '<a href="utenti.php?sortby=email_desc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_desc&amp;start=' . $start . '">Status</a>';
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'nome_desc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.nome_cognome DESC LIMIT $start,$rec_page";
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_asc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_email = '<a href="utenti.php?sortby=email_desc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_desc&amp;start=' . $start . '">Status</a>';
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'email_asc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.email ASC LIMIT $start,$rec_page";
            $link_email = '<a href="utenti.php?sortby=email_desc&amp;start=' . $start . '">Email</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_status = '<a href="utenti.php?sortby=status_desc&amp;start=' . $start . '">Status</a>';
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'email_desc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.email DESC LIMIT $start,$rec_page";
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_status = '<a href="utenti.php?sortby=status_desc&amp;start=' . $start . '">Status</a>';
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'status_asc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.attivo ASC LIMIT $start,$rec_page";
            $link_status = '<a href="utenti.php?sortby=status_desc&amp;start=' . $start . '">Status</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'status_desc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.attivo DESC LIMIT $start,$rec_page";
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'news_asc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY TotaleNews ASC LIMIT $start,$rec_page";
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'news_desc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY TotaleNews DESC LIMIT $start,$rec_page";
            $link_news = '<a href="utenti.php?sortby=news_asc&amp;start=' . $start . '">' . $lang['news'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'permessi_asc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.permessi ASC LIMIT $start,$rec_page";
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'permessi_desc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.permessi DESC LIMIT $start,$rec_page";
            $link_news = '<a href="utenti.php?sortby=news_asc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_asc&amp;start=' . $start . '">' . $lang['permessi'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'autorizza_asc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.autorizza_news DESC LIMIT $start,$rec_page";
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'autorizza_desc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.autorizza_news ASC LIMIT $start,$rec_page";
            $link_news = '<a href="utenti.php?sortby=news_asc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_asc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_asc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a>';
        break;
        case 'data_asc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.data_registrazione ASC LIMIT $start,$rec_page";
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=mail_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_news = '<a href="utenti.php?sortby=news_asc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
        break;
        case 'data_desc':
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.data_registrazione DESC LIMIT $start,$rec_page";
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_asc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_asc&amp;start=' . $start . '">Status</a>';
            $link_news = '<a href="utenti.php?sortby=news_asc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_asc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
        break;
        default:
            $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.data_registrazione DESC LIMIT $start,$rec_page";
            $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
            $link_email = '<a href="utenti.php?sortby=email_desc&amp;start=' . $start . '">Email</a>';
            $link_status = '<a href="utenti.php?sortby=status_desc&amp;start=' . $start . '">Status</a>';
            $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
            $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
            $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
            $link_data_registrazione = '<a href="utenti.php?sortby=data_desc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
    }
}
else {
    $get_sortby = NULL;
	$action2 = '';
    $order_query = "SELECT nu.user_id, nu.nome_cognome, nu.email, nu.livello_id, nu.attivo, nu.permessi, nu.autorizza_news, nu.data_registrazione, nl.nome_livello, COUNT(nt.user_id) AS TotaleNews FROM `$tab_utenti` nu LEFT JOIN `$tab_news` nt ON nt.user_id = nu.user_id JOIN `$tab_livelli` nl ON nl.livello_id=nu.livello_id GROUP BY nu.user_id ORDER BY nu.data_registrazione DESC LIMIT $start,$rec_page";
    $link_nome_cognome = '<a href="utenti.php?sortby=nome_desc&amp;start=' . $start . '">' . $lang['nome_utente'] . '</a>';
    $link_email = '<a href="utenti.php?sortby=email_desc&amp;start=' . $start . '">Email</a>';
    $link_status = '<a href="utenti.php?sortby=status_desc&amp;start=' . $start . '">Status</a>';
    $link_news = '<a href="utenti.php?sortby=news_desc&amp;start=' . $start . '">' . $lang['news'] . '</a>';
    $link_permessi = '<a href="utenti.php?sortby=permessi_desc&amp;start=' . $start . '">' . $lang['permessi'] . '</a>';
    $link_autorizza_news = '<a href="utenti.php?sortby=autorizza_desc&amp;start=' . $start . '">' . $lang['aut_news'] . '</a>';
    $link_data_registrazione = '<a href="utenti.php?sortby=data_asc&amp;start=' . $start . '">' . $lang['data_reg'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
}

if (isset($_POST['submit_news'])) {
    
    if (isset($_POST['cb_id'])) {
		
        $uid = implode(",", $_POST['cb_id']);

        //cancello le news
        
        if ($_POST['submit_sel'] == 'elimina_news') {
            				
			// rimuovo la relazione tags-news
			mysqli_query($db, "DELETE FROM `$tab_tags_rel` WHERE id_news IN (SELECT id FROM `$tab_news` WHERE user_id IN ($uid)");
				
            if (mysqli_query($db, "DELETE FROM `$tab_news` WHERE user_id IN ($uid) AND user_id <>" . $_SESSION['user_id'])) {

                $query_msg = '<div id="success">' . $lang['canc_news_user_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['canc_news_user_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //attivo gli utenti
        elseif ($_POST['submit_sel'] == 'attiva_utenti') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET attivo=1 WHERE user_id IN ($uid) AND attivo=0 AND user_id <>" . $_SESSION['user_id'])) {
                $query_msg = '<div id="success">' . $lang['utenti_attivati_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['utenti_attivati_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //disattivo gli utenti
        elseif ($_POST['submit_sel'] == 'disattiva_utenti') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET attivo=0, mostra_link='nome', token=NULL, cookie=0, new_pwd=NULL, key_pwd=NULL WHERE user_id IN ($uid) AND attivo=1 AND user_id <>" . $_SESSION['user_id'] . " AND livello_id>1")) {
                $query_msg = '<div id="success">' . $lang['utenti_disattivati_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['utenti_disattivati_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //permessi per Upload
        elseif ($_POST['submit_sel'] == 'upload') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET permessi='upload' WHERE user_id IN ($uid) AND user_id <>" . $_SESSION['user_id'] . " AND livello_id>1")) {
                $query_msg = '<div id="success">' . $lang['edit_permessi_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['edit_permessi_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //permessi per letture
        elseif ($_POST['submit_sel'] == 'letture') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET permessi='letture' WHERE user_id IN ($uid) AND user_id <>" . $_SESSION['user_id'] . " AND livello_id>1")) {
                $query_msg = '<div id="success">' . $lang['edit_permessi_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['edit_permessi_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //tutti i permessi
        elseif ($_POST['submit_sel'] == 'tutto') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET permessi='tutto' WHERE user_id IN ($uid) AND user_id <>" . $_SESSION['user_id'] . " AND livello_id>1")) {
                $query_msg = '<div id="success">' . $lang['edit_permessi_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['edit_permessi_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //nessun permesso
        elseif ($_POST['submit_sel'] == 'nessuno') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET permessi='nessuno' WHERE user_id IN ($uid) AND user_id <>" . $_SESSION['user_id'] . " AND livello_id>1")) {
                $query_msg = '<div id="success">' . $lang['edit_permessi_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['edit_permessi_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //autorizza news
        elseif ($_POST['submit_sel'] == 'autorizza_news') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET autorizza_news=1 WHERE user_id IN ($uid) AND user_id <>" . $_SESSION['user_id'] . " AND autorizza_news=0 AND livello_id>1")) {
                $query_msg = '<div id="success">' . $lang['edit_permessi_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['edit_permessi_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //blocco news
        elseif ($_POST['submit_sel'] == 'blocca_news') {
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET autorizza_news=0 WHERE user_id IN ($uid) AND user_id <>" . $_SESSION['user_id'] . " AND autorizza_news=1 AND livello_id>1")) {
                $query_msg = '<div id="success">' . $lang['edit_permessi_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=utenti.php?' . $action1 . $action2 . '');
            }
            else {
                $query_msg = '<div id="error">' . $lang['edit_permessi_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //cancello i file degli utenti
        elseif ($_POST['submit_sel'] == 'rimuovi_file') {
            $dirs = explode(",", $uid);
            
            foreach ($dirs as $del_dirs => $val) {
                
                if (!preg_match('/^[0-9]{1,5}$/', $val)) {
                    unset($dirs[$del_dirs]);
                }
                else {
                    full_rmdir('../' . $file_dir . '/' . $val);
                }
            }
        }

        //invio email agli utenti
        elseif ($_POST['submit_sel'] == 'invia_email') {
            $popup_email = '<script language="javascript" type="text/JavaScript">
				<!--
				var test = window.open(\'invia_email_utenti.php?user_id=' . $uid . '\', \'popup\',\'width=420px, height=435px, resizable=1, scrollbars=1, location=1, status=1\');
				if (!test) {
				alert(\'' . $lang['nopopup'] . '\');
				} 
				//-->
				</script>';
        }
    }
    else {
        $uid = NULL;
        $query_msg = '<div id="error">' . $lang['selez_utente'] . '</div><br />';
    }
}

//creazione nuovo utente

if (isset($_POST['submit'])) {
    
    if (trim($_POST['nome_cognome']) == '' || trim($_POST['email']) == '') {
        $utente_msg = '<br /><div id="error2">' . $lang['campi_obbligatori'] . '</div>';
    }
    else {
        $utente_msg = NULL;
        
        if (!preg_match('/^[.a-z0-9_-]+@[.a-z0-9_-]+\.[a-z]{2,4}$/', $_POST['email'])) {
            $email_errata = '<br /><div id="error2">' . $lang['wrong_email'] . '</div>';
        }
        else {
            $email_errata = NULL;
            $pwd_random = NewPassword();
            $password_utente = password_hash($pwd_random, PASSWORD_DEFAULT);
            
            $nome_cognome_utente = mysqli_real_escape_string($db, trim($_POST['nome_cognome']));
            $email_utente = $_POST['email'];

            //vedo se l'email del nuovo utente esiste
            $check_utente = mysqli_query($db, "SELECT email FROM `$tab_utenti` WHERE email='$email_utente' LIMIT 1");
            
            if (mysqli_num_rows($check_utente) > 0) {
                $utente_presente = '<br /><div id="error2">' . $lang['user_email_exists'] . '</div>';
            }
            else {
                
                if (mysqli_query($db, "INSERT INTO `$tab_utenti` (nome_cognome, email, livello_id, attivo, user_password, permessi, autorizza_news, mostra_link, email_nascosta, ultimo_accesso, data_registrazione) VALUES ('" . htmlspecialchars($nome_cognome_utente, ENT_QUOTES, "ISO-8859-1") . "', '$email_utente', 3, 1, '$password_utente', 'tutto', 1, 'nome', 1, 0, " . time() . " )")) {
                    
                    if (isset($_POST['invia_dati'])) {
						$phpversion = (!@phpversion()) ? "N/A" : phpversion();
						$header = "From: " . $_SERVER['SERVER_ADMIN'] . "\n";
						$header .= "Reply-To: " . $_SERVER['SERVER_ADMIN'] . "\n";
						$header .= "Return-Path: " . $_SERVER['SERVER_ADMIN'] . "\n";
						$header .= "X-Mailer: PHP " . $phpversion . "\n";
						$header .= "MIME-Version: 1.0\n";
						$header .= "Content-type: text/plain; charset=ISO-8859-1\n";
						$header .= "Content-Transfer-encoding: 7bit\n";
						mail($email_utente, $rowconf['nome_sito'] . ' - ' . $lang['account_attivato'], "" . $lang['ciao'] . " " . stripslashes($nome_cognome_utente) . ",\n" . $lang['corpo'] . "\n$dir_admin/login.php\n\nEmail: $email_utente\n" . $lang['password'] . ": $pwd_random\n\n-- \n" . $rowconf['url_sito'] . "", $header);
						$query_utente_msg = '<br /><span class="text2"><b>' . $lang['utente_ok'] . '</b></span>';
						
					} else {
						
						$query_utente_msg = '<br /><span class="text2"><b>' . $lang['utente_ok_stampa'] . ' Email: ' . $email_utente . ' ' . $lang['password'] . ': ' . $pwd_random . '</b></span>';
					}
                }
                else {
                    $query_utente_msg = '<br /><div id="error">' . $lang['utente_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
                }
                $utente_presente = NULL;
            }
            $utente_msg = NULL;
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['elenco_utenti']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>      
  </head>     
  <body>
<?php
require_once ("menu.php");
echo $popup_email;
echo $query_msg;
echo $del_ok;
echo '<form name="admin" action="utenti.php?' . $action1 . $action2 . '" method="post">
<table width="100%" style="border: 3px solid #DDDDDD;" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF" align="center">
<tr><td align="center" bgcolor="#EEEEEE"><img src="' . $img_path . '/utenti.png" border="0" alt="" /></td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $link_nome_cognome . '</td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $link_email . '</td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $link_status . '</td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $link_news . '</td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $link_permessi . '</td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $link_autorizza_news . '</td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $lang['file'] . '</td>
<td class="text" align="center" bgcolor="#EEEEEE">' . $link_data_registrazione . '</td>
</tr>';
$q_utenti = mysqli_query($db, "$order_query");

while ($q_riga = mysqli_fetch_array($q_utenti)) {

    //seleziono il formato data
    
    switch ($rowconf['formato_data']) {
        case 1:
            $data = strftime("%a %d %b %Y, %H:%M", $q_riga['data_registrazione']);
        break;
        case 2:
            $data = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $q_riga['data_registrazione']));
        break;
        case 3:
            $data = strftime("%d/%m/%Y, %H:%M", $q_riga['data_registrazione']);
        break;
        case 4:
            $data = strftime("%d %b %Y, %H:%M", $q_riga['data_registrazione']);
        break;
        case 5:
            $data = strftime("%d %B %Y, %H:%M", $q_riga['data_registrazione']);
        break;
        case 6:
            $data = strftime("%m/%d/%Y, %I:%M %p", $q_riga['data_registrazione']);
        break;
        case 7:
            $data = strftime("%B %d, %Y %I:%M %p", $q_riga['data_registrazione']);
        break;
        case 8:
            $data = strftime("%I:%M %p %B %d, %Y", $q_riga['data_registrazione']);
        break;
    }
    $nome_livello = ($q_riga['livello_id'] == 1) ? '<br />(' . $q_riga['nome_livello'] . ')' : NULL;
    $TotaleNews = ($q_riga['TotaleNews'] > 0) ? '<a href="gestione_news.php?user_id=' . $q_riga['user_id'] . '"><b>' . $q_riga['TotaleNews'] . '</b></a>' : $q_riga['TotaleNews'];
    $attivo = ($q_riga['attivo'] == 1) ? '' . $lang['attivo'] . '' : '<span style="color: #FF0000">' . $lang['disattivo'] . '</span>';

    if ($q_riga['permessi'] == 'nessuno') {
        $permessi = '<span style="color: #FF0000">' . $lang['nessun_permesso'] . '</span>';
    }
    else 
    if ($q_riga['permessi'] == 'tutto') {
        $permessi = $lang['tutti_permessi'];
    }
    else 
    if ($q_riga['permessi'] == 'letture') {
        $permessi = $lang['modifica_letture'];
    }
    else 
    if ($q_riga['permessi'] == 'upload') {
        $permessi = $lang['permessi_upload'];
    }
    $autorizza_news = ($q_riga['autorizza_news'] == 1) ? '' . $lang['autorizzato'] . '' : '<span style="color: #FF0000; cursor: help;" class="help2" title="' . $lang['user_autorizza_news'] . '">' . $lang['non_autorizzato'] . '</span>';
    $checkbox = ($q_riga['user_id'] == 1) ? NULL : '<input type="checkbox" name="cb_id[]" value="' . $q_riga['user_id'] . '" id="id_' . $q_riga['user_id'] . '" />';

    //conto quanti file ha ogni utente
    $files = 0;
    
    if ($aprodir = @opendir('../' . $file_dir . '/' . $q_riga['user_id'])) {
        
        while (false !== ($ifile = readdir($aprodir))) {
            
            if ($ifile != '.' && $ifile != '..' && $ifile != 'index.html') {
                ++$files;
            }
        }
        closedir($aprodir);
    }
    $files = ($files == 0) ? 0 : '<a href="javascript:;" onclick="window.open(\'files.php?user_id=' . $q_riga['user_id'] . '\', \'\', \'width=650, height=450, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]"><b>' . $files . '</b></a>';
    echo '<tr onmouseover="this.bgColor=\'#E6F1FA\'" onmouseout="this.bgColor=\'#FFFFFF\'">
                      <td width="2%" align="center">' . $checkbox . '</td>
                      <td width="18%" align="left" class="text"><a href="profilo_admin.php?user_id=' . $q_riga['user_id'] . '">' . $q_riga['nome_cognome'] . '</a>' . $nome_livello . '</td>
                      <td width="11%" align="left" class="text">' . $q_riga['email'] . '</td>
                      <td width="8%" align="center" class="text">' . $attivo . '</td>
                      <td width="7%" align="center" class="text">' . $TotaleNews . '</td>
                      <td width="10%" align="center" class="text">' . $permessi . '</td>
                      <td width="10%" align="center" class="text">' . $autorizza_news . '</td>
                      <td width="6%" align="center" class="text">' . $files . '</td>
                      <td width="17%" align="left" class="text">' . $data . '</td>
                      </tr>';
}
echo '<tr>
  	<td colspan="5" bgcolor="#EEEEEE" class="text2" align="left">' . $lang['select'] . '
		<a href="javascript:onClick=checkTutti()" class="piccolo">' . $lang['select_all'] . '</a>, <a href="javascript:onClick=uncheckTutti()" class="piccolo">' . $lang['select_none'] . '</a>&nbsp;
		<select name="submit_sel">
    <option selected="selected">' . $lang['operazioni'] . '</option>
    <option value="attiva_utenti">' . $lang['attiva_utenti'] . '</option>
    <option value="disattiva_utenti">' . $lang['disattiva_utenti'] . '</option>
    <option value="elimina_news" style="background:red; color:white;">' . $lang['cancella_news'] . ' &#9888; </option>
    <option value="upload">' . $lang['permessi_upload'] . '</option>
    <option value="letture">' . $lang['permessi_letture'] . '</option>
    <option value="tutto">' . $lang['tutti_permessi'] . '</option>
    <option value="nessuno">' . $lang['nessun_permesso'] . '</option>
    <option value="autorizza_news">' . $lang['autorizza_news'] . '</option>
    <option value="blocca_news">' . $lang['blocca_news'] . '</option>
    <option value="rimuovi_file" style="background:red; color:white;">' . $lang['cancella_file'] . ' &#9888; </option>    
    <option value="invia_email">' . $lang['invia_email'] . ' [Popup]</option>
</select> <input type="submit" name="submit_news" value="' . $lang['vai'] . '" onclick="return confirmSubmit();" style="font-weight: bold;" /> <input type="hidden" name="post_token" value="' . sha1(session_id()) . '" /></td>';
echo '<td colspan="4" bgcolor="#EEEEEE" class="text2" align="right">';

//paginazione
$sql_num_totale = mysqli_query($db, "SELECT COUNT(user_id) AS NumTotale FROM `$tab_utenti`");
$num_totale_riga = mysqli_fetch_array($sql_num_totale);
$numero_pagine = ceil($num_totale_riga['NumTotale'] / $rec_page);
$pagina_attuale = ceil(($start / $rec_page) + 1);
echo '<b>(' . $lang['totale'] . ' ' . $num_totale_riga['NumTotale'] . ')</b> ' . page_bar("utenti.php?$get_sortby", $pagina_attuale, $numero_pagine, $rec_page);
echo '</td></tr></table>';
?>         
    </form><br />
          <form name="crea_utenti" action="utenti.php" method="post">                         
            <fieldset>                             
              <legend class="text2"><b><?php echo $lang['nuovo_utente']; ?></b></legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">             
                  <tr>                 
                    <td class="text2" align="center">                     
              <?php echo $lang['nome_utente']; ?>
              <input type="text" name="nome_cognome" size="25" maxlength="40" /> &nbsp; Email
              <input type="text" name="email" size="25" maxlength="50" /> &nbsp; <input type="checkbox" name="invia_dati" id="invia_dati" checked="checked" /><label for="invia_dati"><?php echo $lang['invia_accesso']; ?></label> &nbsp; 
              <input type="hidden" name="post_token" value="<?php echo sha1(session_id()); ?>" />		                              
              <input type="submit" name="submit" value="<?php echo $lang['btn_insert']; ?>" style="font-weight: bold;" /><br />				                              
<?php echo $utente_msg; echo $query_utente_msg; echo $utente_presente; echo $email_errata; ?>                           
     </td>             
      </tr>         
    </table>
      </fieldset>                              
          </form>
          <br />         
    <?php require_once ("footer.php"); ?>      
  </body>
</html>
