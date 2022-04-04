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

header('Content-type: text/html; charset=ISO-8859-1');

//includo il file di configurazione
require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/admin/functions.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

//connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

//estraggo alcune impostazioni
$conf = @mysqli_query($db, "SELECT nome_sito, url_sito, max_archivio, max_archivio_parole, nuova_news_day, formato_data FROM `$tab_config`");
$rowconf = @mysqli_fetch_array($conf);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">           
  <head>                   
    <title>                           
      <?php echo $rowconf['nome_sito'] . ' - ' . $lang['pagina_archivio']; ?>      
    </title>                   
    <link rel="stylesheet" href="style.css" type="text/css" />                   
    <link rel="alternate" type="application/rss+xml" title="Feed RSS News" href="<?php echo $rowconf['url_sito'] . '/' . $news_dir . '/rss.php'; ?>" />           
  </head>           
  <body>
<?php

//suddivisione news per pagine
$rec_page = $rowconf['max_archivio'];
$start = (isset($_GET['start'])) ? abs(floor(intval($_GET['start']))) : 0;

if (isset($_GET['autore']) && preg_match('/^[0-9]{1,5}$/', $_GET['autore'])) {
    $autore = abs(floor(intval($_GET['autore'])));
    $get_autore = "&amp;autore=" . abs(floor(intval($_GET['autore'])));
    $q_autore = "WHERE nu.user_id=$autore";
}
else {
    $autore = NULL;
    $get_autore = NULL;
    $q_autore = NULL;
}

if (isset($_GET['categoria']) && preg_match('/^[0-9]{1,3}$/', $_GET['categoria'])) {
    $categoria = abs(floor(intval($_GET['categoria'])));
    $get_categoria = "&amp;categoria=" . abs(floor(intval($_GET['categoria'])));
    $q_categoria = "WHERE nt.id_cat=$categoria";
}
else {
    $categoria = NULL;
    $get_categoria = NULL;
    $q_categoria = NULL;
}

//ordinamento news

if (isset($_GET['sortby']) && preg_match('/^[1-6]{1}$/', $_GET['sortby'])) {
    $sortby = abs(floor(intval($_GET['sortby'])));
    $get_sortby = "&amp;sortby=$sortby";
    
    switch ($sortby) {
        case 1:
            $q_sortby = 'ORDER BY nt.letture DESC, nt.data_pubb DESC';
            $view_letture = $lang['arch_letture'] . ' <span style="font-weight:bold;">';
            $view_letture2 = '</span>';
            $view_commenti = $lang['arch_commenti'];
            $view_commenti2 = '';
        break;
        case 3:
            $q_sortby = 'ORDER BY nt.data_pubb DESC';
            $view_letture = $lang['arch_letture'];
            $view_letture2 = '';
            $view_commenti = $lang['arch_commenti'];
            $view_commenti2 = '';
        break;
        case 4:
            $q_sortby = 'ORDER BY nu.nome_cognome ASC, nt.data_pubb DESC';
            $view_letture = $lang['arch_letture'];
            $view_letture2 = '';
            $view_commenti = $lang['arch_commenti'];
            $view_commenti2 = '';
        break;
        case 5:
            $q_sortby = 'ORDER BY nca.nome_categoria ASC, nt.data_pubb DESC';
            $view_letture = $lang['arch_letture'];
            $view_letture2 = '';
            $view_commenti = $lang['arch_commenti'];
            $view_commenti2 = '';
        break;
        case 6:
            $q_sortby = 'ORDER BY TotaleCommenti DESC, nt.data_pubb DESC';
            $view_letture = $lang['arch_letture'];
            $view_letture2 = '';
            $view_commenti = $lang['arch_commenti'] . ' <span style="font-weight:bold;">';
            $view_commenti2 = '</span>';
        break;
        default:
            $q_sortby = 'ORDER BY nt.data_pubb DESC, nt.data_pubb DESC';
            $view_letture = $lang['arch_letture'];
            $view_letture2 = '';
            $view_commenti = $lang['arch_commenti'];
            $view_commenti2 = '';
    }
}
else {
    $sortby = NULL;
    $q_sortby = "ORDER BY nt.data_pubb DESC";
    $get_sortby = NULL;
    $view_letture = $lang['arch_letture'];
    $view_letture2 = '';
    $view_commenti = $lang['arch_commenti'];
    $view_commenti2 = '';
}
echo '<div id="container" style="width: 605px">';
echo '<div id="titolo_art" class="text" style="background-color: #F6F6F6"><b>' . $lang['archivio'] . '</b> [<a href="archivio.php?sortby=1' . $get_autore . $get_categoria . '">' . $lang['piu_lette'] . '</a>, <a href="archivio.php?sortby=6' . $get_autore . $get_categoria . '">' . $lang['piu_commentate'] . '</a>, <a href="archivio.php?sortby=3' . $get_autore . $get_categoria . '">' . $lang['piu_recenti'] . '</a>, <a href="archivio.php?sortby=4' . $get_autore . $get_categoria . '">' . $lang['autore_az'] . '</a>, <a href="archivio.php?sortby=5' . $get_autore . $get_categoria . '">' . $lang['categoria_az'] . '</a>, <a href="archivio.php">' . $lang['tutte'] . '</a>]</div><br />';

if ($rowconf['max_archivio_parole'] == 0) {
    $result = @mysqli_query($db, "SELECT nt.id, nt.titolo, nt.data_pubb, nt.letture, nu.nome_cognome, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id AND nco.approvato=1) AS TotaleCommenti FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat $q_autore $q_categoria AND nt.news_approvata = 1 AND nt.data_pubb < " . time() . " $q_sortby LIMIT $start,$rec_page");
}
else {
    $result = @mysqli_query($db, "SELECT nt.id, nt.titolo, LEFT(nt.testo, " . $rowconf['max_archivio_parole'] . ") AS testo, nt.data_pubb, nt.letture, nu.nome_cognome, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(nco.id_news) FROM `$tab_commenti` nco WHERE nco.id_news=nt.id AND nco.approvato=1) AS TotaleCommenti FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat $q_autore $q_categoria AND nt.news_approvata = 1 AND nt.data_pubb < " . time() . " $q_sortby LIMIT $start,$rec_page");

    //sostituisco i codici di formattazione
    $testo_cerca = array(
        '{\[ul]}',
        '{\[li]}',
        '{\[/li]}',
        '{\[/ul]}',
        '{&amp;}',
        '{\[b]}',
        '{\[i]}',
        '{\[u]}',
        '{\[/b]}',
        '{\[/i]}',
        '{\[/u]}',
        '{\[e]}',
        '{\[/e]}',
        '{\[img\](\r\n|\r|\n)*((http|https)://([^;<>\*\(\)\"\s]+)|[a-zA-Z0-9/\\\._\- ]+)\[/img\]}siU',
        '{\[email\](\r\n|\r|\n)*([a-zA-Z0-9\._-]+@(([a-zA-Z0-9_-])+\.)+[a-z]{2,4})\[/email\]}siU',
        '{\[email=(\w[\w\-\.\+]*?@\w[\w\-\.]*?\w\.[a-zA-Z]{2,4})\](.+)?\[\/email\]}siU',
        '{(\[)(url)(])((http|ftp|https)://)([^;<>\*\(\)"\s]*)(\[/url\])}siU',
        '{(\[)(url)(=)([\'"]?)((http|ftp|https)://)([^;<>\*\(\)"\s]*)(\\4])(.*)(\[/url\])}siU',
        '{(\[)(callto)(])((callto):)([^;<>\*\(\)"\s]*)(\[/callto\])}siU',
        '{(\[)(callto)(=)([\'"]?)((callto):)([^;<>\*\(\)"\s]*)(\\4])(.*)(\[/callto\])}siU',
        '{(\[)(size)(=)([\'"]?)([0-9]*)(\\4])(.*)(\[/size\])}siU',
		'{(\[)(color)(=)([\'"]?)([a-z]*)(\\4])(.*)(\[/color\])}siU',
        '{\[quote\](\r\n|\r|\n)*(.+)\[/quote\]}siU',
        '{\[code\](\r\n|\r|\n)*(.+)\[/code\]}siU',
        '{\[yt\]([0-9a-zA-Z-_]{11})\[/yt]}siU',
        '{(\[)(gmap)(])((http|https)://)([^;<>\*\(\)"\s]*)(\[/gmap\])}siU',        
        '{\[icq\]([0-9]{5,10})\[/icq\]}siU',
        '{\[sky\]([.0-9a-zA-Z-_]{6,32})\[/sky]}siU',
        '{\[wa\]([0-9]{9,15})\[/wa\]}siU',
        '{\[tg\]([0-9a-zA-Z_]{5,30})\[/tg]}siU',
        '{\[si\]([0-9]{9,15})\[/si\]}siU'
    );
    $testo_sostituisci = array(
        '',
        '',
        '',
        '',
        '&',
        '',
        '',
        '',
        '',
        '',
        '',
        '',        
        '',
        '',
        '\\2',
        '\\1',
        '\\4\\6',
        '\\9',
        '\\4\\6',
        '\\5\\7',
        '\\7',
        '\\7',
        '\\2',
        '\\2',
        '[Video: youtube.com/watch?v=\\1]',
		'\\4\\6',
        '\\1',
        '\\1',
        '\\1',
        '\\1',
        '\\1'
    );
}
echo '<div id="archivio" style="font-size: 13px; line-height: 16px; background-color: #FFFFFF">';

//stampo l'elenco delle news

while ($row = @mysqli_fetch_array($result)) {

    //seleziono il formato data
    
    switch ($rowconf['formato_data']) {
        case 1:
            $data = strftime("%a %d %b %Y, %H:%M", $row['data_pubb']);
        break;
        case 2:
            $data = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $row['data_pubb']));
        break;
        case 3:
            $data = strftime("%d/%m/%Y, %H:%M", $row['data_pubb']);
        break;
        case 4:
            $data = strftime("%d %b %Y, %H:%M", $row['data_pubb']);
        break;
        case 5:
            $data = strftime("%d %B %Y, %H:%M", $row['data_pubb']);
        break;
        case 6:
            $data = strftime("%m/%d/%Y, %I:%M %p", $row['data_pubb']);
        break;
        case 7:
            $data = strftime("%B %d, %Y %I:%M %p", $row['data_pubb']);
        break;
        case 8:
            $data = strftime("%I:%M %p %B %d, %Y", $row['data_pubb']);
        break;
    }
    $img_new = ((time() - $row['data_pubb']) <= 60 * 60 * 24 * $rowconf['nuova_news_day']) ? '<img src="' . $img_path . '/new.gif" alt="New" />' : '';
    $view_nome = ($sortby == 4 || isset($autore)) ? '<span style="font-weight:bold;">' . $row['nome_cognome'] . '</span>, ' : '';
    $view_categoria = ($sortby == 5 || isset($categoria)) ? ', <span style="font-weight:bold;">' . $row['nome_categoria'] . '</span> ' : '';
    
    if ($rowconf['max_archivio_parole'] > 0) {
        echo '<div id="imgcat_' . $row['id'] . '" style="min-height: 50px; height:auto !important; height: 50px;"><div id="news_' . $row['id'] . '" style="float: left; margin: 0px 0px 20px 0px; width: 35px;"><img src="' . $row['img_categoria'] . '" width="30" height="30" alt="" border="0" /></div> ' . $img_new . ' <a href="view.php?id=' . $row['id'] . '">' . $row['titolo'] . '</a> ' . $data . ' - ' . $view_nome . ' ' . $view_letture . ' ' . number_format($row['letture'], 0, '', '.') . $view_letture2 . ', ' . $view_commenti . ' ' . $row['TotaleCommenti'] . $view_commenti2 . $view_categoria . '<br /><span class="text2">' . preg_replace($testo_cerca, $testo_sostituisci, $row['testo']) . '...</span></div>';
        echo "\n";
    }
    else {
        echo '<div id="imgcat_' . $row['id'] . '" style="min-height: 50px; height:auto !important; height: 50px;"><div id="news_' . $row['id'] . '" style="float: left; margin: 0px 0px 20px 0px; width: 35px;"><img src="' . $row['img_categoria'] . '" width="30" height="30" alt="" border="0" /></div> ' . $img_new . ' <a href="view.php?id=' . $row['id'] . '">' . $row['titolo'] . '</a> ' . $data . ' - ' . $view_nome . ' ' . $view_letture . ' ' . number_format($row['letture'], 0, '', '.') . $view_letture2 . ', ' . $view_commenti . ' ' . $row['TotaleCommenti'] . $view_commenti2 . $view_categoria . '</div>';
        echo "\n";
    }
}
?>                   
    </div><br />                   
    <div id="tool_art" class="text2" style="background-color: #F6F6F6">    
      <a href="search.php" class="piccolo">
        <img src="<?php echo $img_path; ?>/search.png" border="0" alt="" /><?php echo $lang['cerca']; ?></a>  
         <a href="tags.php" class="piccolo">
		  <img src="<?php echo $img_path; ?>/tags.png" border="0" alt="" /><?php echo $lang['tags']; ?></a>        
    </div>                   
    <div id="paginazione" class="text2">
<?php

//paginazione
$sql_num_totale = @mysqli_query($db, "SELECT COUNT(nt.id) AS NumTotale FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id $q_autore $q_categoria AND nt.news_approvata = 1 AND nt.data_pubb < " . time() . "");
$num_totale_riga = @mysqli_fetch_array($sql_num_totale);
$numero_pagine = @ceil($num_totale_riga['NumTotale'] / $rec_page);
$pagina_attuale = @ceil(($start / $rec_page) + 1);
echo '<b>(' . $lang['totale'] . ' ' .  $num_totale_riga['NumTotale'] . ')</b> ' . page_bar("archivio.php?$get_autore$get_categoria$get_sortby", $pagina_attuale, $numero_pagine, $rec_page);
?>                   
    </div>                   
    </div>           
  </body>
</html>
