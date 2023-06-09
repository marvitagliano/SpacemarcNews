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
header('Content-type: text/html; charset=UTF-8');

//calcolo il tempo di generazione della pagina (1a parte)
$mtime1 = explode(" ", microtime());
$starttime = $mtime1[1] + $mtime1[0];

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');

$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);
check_login();

//se non sono un amministratore e voglio visualizzare questa pagina, redirigo all'elenco news personale

if ($_SESSION['livello_id'] != 1) {
    header('Location: ' . $dir_admin . '/elenco_news.php');
    exit();
}

check_form();
 
$conf = mysqli_query($db, "SELECT max_gest_news, formato_data FROM `$tab_config`");
$rowconf = mysqli_fetch_array($conf);
$popup_autore = NULL;

// risultati visualizzati per pagina (serve per la paginazione)
$rec_page = $rowconf['max_gest_news'];
$start = ( isset($_GET['start']) ) ? abs(floor(intval($_GET['start']))) : 0;
$query_msg = NULL;

//controllo user_id per fare le query
if (isset($_GET['user_id']) && preg_match('/^[0-9]{1,5}$/', $_GET['user_id'])) {
    $q_user_id = " WHERE nu.user_id=" . intval($_GET['user_id']) . "";
    $get_user_id = "&amp;user_id=" . intval($_GET['user_id']) . "";
    $query_count = "SELECT COUNT(id) AS NumTotale FROM `$tab_news` WHERE user_id=" . intval($_GET['user_id']);
    $action1 = '&user_id=' . intval($_GET['user_id']) . '';
}
else {
    $q_user_id = NULL;
    $get_user_id = NULL;
    $query_count = "SELECT COUNT(id) AS NumTotale FROM `$tab_news`";
    $action1 = '';
}

//controllo per action del form e refresh dopo la query
if  ( isset($_GET['start']) ) {
    
	$action2 = '&start=' . intval($_GET['start']) . '';
    
	} else {

	$action2 = '';
}

//controllo sortby via GET per costruire i link
if (isset($_GET['sortby'])) {
    $get_sortby = "sortby=" . addslashes($_GET['sortby']);
    $action3 = '&sortby=' . $_GET['sortby'] . '';
    
    switch ($_GET['sortby']) {
        case 'titolo_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id=nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . " ORDER BY nt.titolo ASC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';
        break;
        case 'titolo_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.titolo DESC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
			$link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';
        break;
        case 'autore_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nu.nome_cognome ASC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>  <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'autore_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nu.nome_cognome DESC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>  <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'data_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.data_pubb ASC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'data_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';
        break;
        case 'letture_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.letture ASC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'letture_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.letture DESC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'comm_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY TotaleCommenti ASC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'comm_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY TotaleCommenti DESC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'tags_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY TotaleTags ASC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
            $link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'tags_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY TotaleTags DESC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;        
        case 'cat_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nca.nome_categoria ASC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>  <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'cat_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nca.nome_categoria DESC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
        break;
        case 'approvata_asc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.news_approvata ASC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';            
        break;
        case 'approvata_desc':
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.news_approvata DESC, nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a>';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';            
        break;
        default:
			$action3 = '';
            $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.data_pubb DESC LIMIT $start,$rec_page";
            $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
            $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
            $link_data = '<a href="gestione_news.php?sortby=data_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
            $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
			$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';            
            $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
            $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
    }
}
else {
    $get_sortby = NULL;
	$action3 = '';
    $order_query = "SELECT nt.id, LEFT(nt.titolo, 100) AS titolo, nt.data_pubb, nt.letture, nt.abilita_commenti, nt.notifica_commenti, nt.news_approvata, nu.user_id, nu.nome_cognome, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id) AS TotaleCommenti, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(ntr.id_tag) FROM `$tab_tags_rel` ntr WHERE ntr.id_news=nt.id) AS TotaleTags FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nt.user_id = nu.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat " . $q_user_id . "  ORDER BY nt.data_pubb DESC LIMIT $start,$rec_page";
    $link_titolo = '<a href="gestione_news.php?sortby=titolo_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['titolo'] . '</a>';
    $link_autore = '<a href="gestione_news.php?sortby=autore_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['autore'] . '</a>';
    $link_data = '<a href="gestione_news.php?sortby=data_asc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['data'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
    $link_letture = '<a href="gestione_news.php?sortby=letture_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['letture'] . '</a>';
    $link_commenti = '<a href="gestione_news.php?sortby=comm_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['commenti'] . '</a>';
	$link_tags = '<a href="gestione_news.php?sortby=tags_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['tags'] . '</a>';    
    $link_categorie = '<a href="gestione_news.php?sortby=cat_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['categoria'] . '</a>';
    $link_approvata = '<a href="gestione_news.php?sortby=approvata_desc&amp;start=' . $start . '' . $get_user_id . '">' . $lang['news_approvata'] . '</a>';            
}

if (isset($_POST['submit_news']) && !isset($_POST['submit_cat'])) {
	
    if (isset($_POST['cb_id'])) {
		
        $nid = implode(",", $_POST['cb_id']);

        //cancello le news
        
        if ($_POST['submit_sel'] == 'cancella_news') {
            
			if (mysqli_query($db, "DELETE FROM `$tab_news` WHERE id IN ($nid)")) {
	
				// rimuovo la relazione tags-news
				mysqli_query($db, "DELETE FROM `$tab_tags_rel` WHERE id_news IN ($nid)");

                $query_msg = '<div id="success">' . $lang['canc_news_user_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['canc_news_user_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        //azzero letture
        elseif ($_POST['submit_sel'] == 'azzera_ls') {
            
            if (mysqli_query($db, "UPDATE `$tab_news` SET letture=0 WHERE id IN ($nid) AND letture>0")) {
                $query_msg = '<div id="success">' . $lang['azzera_ls_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['azzera_ls_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        // cambio autore delle news
        elseif ($_POST['submit_sel'] == 'sposta_news') {
            $popup_autore = '<script language="javascript" type="text/JavaScript">
				<!--
				var test = window.open(\'sposta_news.php?id=' . $nid . '\', \'popup\',\'width=410px, height=220px, resizable=1, scrollbars=1, location=1, status=1\');
				if (!test) {
				alert(\'' . $lang['nopopup'] . '\');
				} 
				//-->
				</script>';
        }

        // cancello i commenti delle news
        
        if ($_POST['submit_sel'] == 'canc_comm') {
            
            if (mysqli_query($db, "DELETE FROM `$tab_commenti` WHERE id_news IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['canc_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['canc_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        // abilito i commenti per le news
        
        if ($_POST['submit_sel'] == 'abilita_comm') {
            
            if (mysqli_query($db, "UPDATE `$tab_news` SET abilita_commenti=1 WHERE abilita_commenti=0 AND id IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['abilita_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['abilita_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        // disabilito i commenti per le news
        
        if ($_POST['submit_sel'] == 'disabilita_comm') {
            
            if (mysqli_query($db, "UPDATE `$tab_news` SET abilita_commenti=0 WHERE abilita_commenti=1 AND id IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['disabilita_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['disabilita_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        // abilito notifica email nuovi commenti
        
        if ($_POST['submit_sel'] == 'notifica_comm') {
            
            if (mysqli_query($db, "UPDATE `$tab_news` SET notifica_commenti=1 WHERE notifica_commenti=0 AND id IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['notifica_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['notifica_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }

        // disabilito notifica email nuovi commenti
        
        if ($_POST['submit_sel'] == 'disab_notifica_comm') {
            
            if (mysqli_query($db, "UPDATE `$tab_news` SET notifica_commenti=0 WHERE notifica_commenti=1 AND id IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['disab_notifica_commenti_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
				header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['disab_notifica_commenti_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }
        
        // approvazione news
        
        if ($_POST['submit_sel'] == 'approva_news') {
			
			// estraggo i tags associati alla notizia selezionata
			$sql_recount = mysqli_query($db, "SELECT id_tag FROM `$tab_tags` WHERE id_tag IN (SELECT id_tag FROM `$tab_tags_rel` WHERE id_news IN ($nid))");
			$alltags = array();

			while ($row_recount = mysqli_fetch_array($sql_recount)) {
				$alltags[] =  $row_recount['id_tag'];
			}
            
            if (mysqli_query($db, "UPDATE `$tab_news` SET news_approvata=1 WHERE news_approvata=0 AND id IN ($nid)")) {
				
				foreach ($alltags as $onetag) {

					mysqli_query($db, "UPDATE `$tab_tags_rel` SET visibile=1 WHERE id_tag = " . $onetag . " AND id_news IN ($nid) AND visibile=0");
				}
			
                $query_msg = '<div id="success">' . $lang['news_approvata_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['news_approvata_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }
        
        // disapprovazione news
        
        if ($_POST['submit_sel'] == 'disapprova_news') {

			// estraggo i tags associati alla notizia selezionata
			$sql_recount = mysqli_query($db, "SELECT id_tag FROM `$tab_tags` WHERE id_tag IN (SELECT id_tag FROM `$tab_tags_rel` WHERE id_news IN ($nid))");
			$alltags = array();

			while ($row_recount = mysqli_fetch_array($sql_recount)) {
				$alltags[] =  $row_recount['id_tag'];
			}
			            
            if (mysqli_query($db, "UPDATE `$tab_news` SET news_approvata=0 WHERE news_approvata=1 AND id IN ($nid)")) {
				
				foreach ($alltags as $onetag) {
												
					mysqli_query($db, "UPDATE `$tab_tags_rel` SET visibile=0 WHERE id_tag = " . $onetag . " AND id_news IN ($nid) AND visibile=1");
				}

                $query_msg = '<div id="success">' . $lang['news_disapprovata_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['news_disapprovata_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }
        
        // cancello i tags
        
		if ($_POST['submit_sel'] == 'rimuovi_tags') {
			
			// rimuovo la relazione tags-news
            if (mysqli_query($db, "DELETE FROM `$tab_tags_rel` WHERE id_news IN ($nid)")) {

	            $query_msg = '<div id="success">' . $lang['canc_tags_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['canc_tags_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }
        
    }
    else {
        $nid = NULL;
        $query_msg = '<div id="error">' . $lang['selez_news_error'] . '</div><br />';
    }
}

//cambio categoria
elseif (isset($_POST['submit_cat'])) {

    if (isset($_POST['cb_id'])) {
        $nid = implode(",", $_POST['cb_id']);
        
        if ($_POST['categoria'] == 'scegli') {
            $query_msg = '<div id="error">' . $lang['news_nuova_categoria_errore'] . '</div><br />';
        }
        else {
            
            if (mysqli_query($db, "UPDATE `$tab_news` SET id_cat=" . intval($_POST['categoria']) . " WHERE id IN ($nid)")) {
                $query_msg = '<div id="success">' . $lang['news_nuova_categoria_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header('Refresh:2; url=gestione_news.php?' . $action1 . $action2 . $action3 .'');
            }
            else {
                $query_msg = '<div id="error">' . $lang['news_nuova_categoria_errore'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
        }
    }
    else {
        $nid = NULL;
        $query_msg = '<div id="error">' . $lang['selez_news_error'] . '</div><br />';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['gestione_news']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>      
  </head>     
  <body>
<?php
require_once ("menu.php");
echo $query_msg;
echo $popup_autore;
echo '<form name="admin" action="gestione_news.php?' . $action1 . $action2 . $action3 . '" method="post">
<table width="100%" style="border: 3px solid #DDDDDD;" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF" align="center">
<tr><td width="1%" align="center" bgcolor="#EEEEEE"><img src="' . $img_path . '/news.png" border="0" alt="" /></td>
<td width="18%" class="text" align="center" bgcolor="#EEEEEE">' . $link_titolo . '</td>
<td width="10%" class="text" align="center" bgcolor="#EEEEEE">' . $link_autore . '</td>
<td width="12%" class="text" align="center" bgcolor="#EEEEEE">' . $link_data . '</td>
<td width="12%" class="text" align="center" bgcolor="#EEEEEE">' . $link_letture . ' ' . $link_commenti . ' ' . $link_tags . '</td>
<td width="6%" class="text" align="center" bgcolor="#EEEEEE">' . $link_categorie . '</td>
<td width="6%" class="text" align="center" bgcolor="#EEEEEE">' . $link_approvata . '</td>
<td width="4%" class="text" align="center" bgcolor="#EEEEEE">' . $lang['opzioni'] . '</td>
</tr>';
$q_order = mysqli_query($db, "$order_query");

while ($q_riga = mysqli_fetch_array($q_order)) {

    //seleziono il formato data
    
    switch ($rowconf['formato_data']) {
        case 1:
        	$data = date("D j F Y, H:i", $q_riga['data_pubb']);
        break;
        case 2:
			$data = date("l j F Y, H:i", $q_riga['data_pubb']);
        break;
        case 3:
             $data = date("d/m/Y, H:i", $q_riga['data_pubb']);
        break;
        case 4:
            $data = date("d M Y, H:i", $q_riga['data_pubb']);
        break;
        case 5:
            $data = date("d F Y, H:i", $q_riga['data_pubb']);
        break;
        case 6:
            $data = date("m/d/Y, H:i", $q_riga['data_pubb']);
        break;
        case 7:
             $data = date("F d, Y H:i", $q_riga['data_pubb']);
        break;
        case 8:
            $data = date("H:i F d, Y", $q_riga['data_pubb']);
        break;
    }
    
		//estraggo i tags presenti per ogni notizia
		$sql_elenco_tags = mysqli_query($db, "SELECT ntg.tag FROM `$tab_tags` ntg JOIN `$tab_tags_rel` ntr ON ntr.id_tag = ntg.id_tag WHERE ntr.id_news = " . $q_riga['id'] . " ORDER BY ntg.tag ASC");
		
		$elenco_tags = array();
		
		while ($sql_elenco_tags_riga = mysqli_fetch_array($sql_elenco_tags)) {
			$elenco_tags[] = $sql_elenco_tags_riga['tag'];
		}
		$tags_associati = ($q_riga['TotaleTags'] > 0) ? '<span class="help2" style="border-bottom: 1px dotted #000;" title="' . implode(", ", $elenco_tags) . '">' . $q_riga['TotaleTags'] . '</span>' : $q_riga['TotaleTags'];
		$autore = ($q_riga['user_id'] == NULL) ? $q_riga['nome_cognome'] : '<a href="profilo_admin.php?user_id=' . $q_riga['user_id'] . '">' . $q_riga['nome_cognome'] . '</a>';
		$comm_abilitati = ($q_riga['abilita_commenti'] == 1) ? '' : ' <img src="' . $img_path . '/no_comm.png" title="' . $lang['comm_disab_icon'] . '" alt="" />';
		$ico_notifica_commenti = ($q_riga['notifica_commenti'] == 0) ? ' <img src="' . $img_path . '/no_mail.png" title="' . $lang['notifica_disab_icon'] . '" alt="" />' : '';
		$TotaleCommenti = ($q_riga['TotaleCommenti'] > 0) ? '<a href="commenti.php?id_news=' . $q_riga['id'] . '"><b>' . $q_riga['TotaleCommenti'] . '</b></a>' : $q_riga['TotaleCommenti'];
		$link_leggi = ($q_riga['data_pubb'] < time() && $q_riga['news_approvata'] == 1) ? '<a href="../view.php?id=' . $q_riga['id'] . '" target="_blank">' . $lang['leggi'] . '</a>' : '<span style="color: #AAAAAA">' . $lang['leggi'] . '</span>';
		$titolo_draft = ($q_riga['data_pubb'] < time() && $q_riga['news_approvata'] == 1) ? $q_riga['titolo'] : '<span style="color: #AAAAAA">' . $q_riga['titolo'] . '</span>';
		$icona_data_futura = ($q_riga['data_pubb'] > time()) ? '<img src="' . $img_path . '/clock.png" alt="data" />' : '';    
		$news_approvata = ($q_riga['news_approvata'] == 1) ? '&#9989;' : '&#9940;';
		echo '<tr onmouseover="this.bgColor=\'#E6F1FA\'" onmouseout="this.bgColor=\'#FFFFFF\'">
						  <td align="center"><input type="checkbox" name="cb_id[]" value="' . $q_riga['id'] . '" id="news_' . $q_riga['id'] . '" /></td>
						  <td align="left" class="text"><label for="news_' . $q_riga['id'] . '">' . $titolo_draft . ' ' . $comm_abilitati . ' ' . $ico_notifica_commenti . '</label></td>
						  <td align="left" class="text">' . $autore . '</td>
						  <td align="left" class="text">' . $icona_data_futura . ' ' . $data . '</td>
						  <td align="center" class="text">' . number_format($q_riga['letture'], 0, '', '.') . ' &nbsp; - &nbsp; ' . $TotaleCommenti . ' &nbsp; - &nbsp; ' . $tags_associati . '</td>
						  <td align="left" class="text"><img src="' . $q_riga['img_categoria'] . '" width="16" height="16" alt="" /> ' . $q_riga['nome_categoria'] . ' </td>
						  <td align="center" class="text">' . $news_approvata . ' </td>
						  <td align="center" class="text"><a href="modifica.php?id=' . $q_riga['id'] . '">' . $lang['modifica'] . '</a>&bull;' . $link_leggi . '</td>
						  </tr>';

}
echo '<tr>
  <td colspan="5" bgcolor="#EEEEEE" class="text2" align="left">
' . $lang['select'] . ' <a href="javascript:onClick=checkTutti()" class="piccolo">' . $lang['select_all'] . '</a>, <a href="javascript:onClick=uncheckTutti()" class="piccolo">' . $lang['select_none'] . '</a>&nbsp;
<select name="submit_sel">
    <option selected="selected">' . $lang['operazioni'] . '</option>
    <option value="azzera_ls" style="background:red; color:white;">' . $lang['azzera_letture'] . ' &#9888; </option>
    <option value="abilita_comm">' . $lang['commenti_on'] . '</option>
    <option value="disabilita_comm">' . $lang['commenti_off'] . '</option>
    <option value="canc_comm" style="background:red; color:white;">' . $lang['cancella_commenti'] . ' &#9888; </option>    
    <option value="notifica_comm">' . $lang['notifica_commenti_on'] . '</option>
    <option value="disab_notifica_comm">' . $lang['notifica_commenti_off'] . '</option>
    <option value="cancella_news" style="background:red; color:white;">' . $lang['cancella_news'] . ' &#9888; </option>    
    <option value="approva_news">' . $lang['approva_news'] . '</option>
    <option value="disapprova_news">' . $lang['disapprova_news'] . '</option>
    <option value="rimuovi_tags" style="background:red; color:white;">' . $lang['rimuovi_tag'] . ' &#9888; </option>
	<option value="sposta_news">' . $lang['cambia_autore'] . ' [Popup]</option>
</select> <input type="submit" name="submit_news" value="' . $lang['vai'] . '" onclick="return confirmSubmit();" style="font-weight: bold;" /> 

&nbsp; &nbsp; ' . $lang['sposta_news'] . '
<select name="categoria">
<option value="scegli" selected="selected">' . $lang['scegli'] . '</option>';
$cat_sel = mysqli_query($db, "SELECT id_cat, nome_categoria FROM `$tab_categorie` ORDER BY nome_categoria ASC");

while ($row_sel = mysqli_fetch_array($cat_sel)) {
    echo '<option value="' . $row_sel['id_cat'] . '">' . $row_sel['nome_categoria'] . '</option>';
    echo "\n";
}
echo '</select> <input type="hidden" name="post_token" value="' . sha1(session_id()) . '" /> <input type="submit" name="submit_cat" value="' . $lang['vai'] . '" onclick="return confirmSubmit();" style="font-weight: bold;" />
</td>
<td colspan="3" bgcolor="#EEEEEE" class="text2" align="right">';

//paginazione
$sql_num_totale = mysqli_query($db, "$query_count");
$num_totale_riga = mysqli_fetch_array($sql_num_totale);
$numero_pagine = ceil($num_totale_riga['NumTotale'] / $rec_page);
$pagina_attuale = ceil(($start / $rec_page) + 1);
echo '<b>(' . $lang['totale'] . ' ' . $num_totale_riga['NumTotale'] . ')</b> ' . page_bar("gestione_news.php?$get_sortby$get_user_id", $pagina_attuale, $numero_pagine, $rec_page);
echo '</td></tr></table></form><br />';        
require_once ("footer.php"); 
?>
</body>
</html>
