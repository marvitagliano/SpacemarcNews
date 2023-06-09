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
require_once (dirname(__FILE__) . '/admin/functions.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

//estraggo alcune impostazioni
$conf = @mysqli_query($db, "SELECT nome_sito, url_sito, max_ricerche, formato_data FROM `$tab_config`");
$rowconf = @mysqli_fetch_array($conf);
$error = NULL;
$num_totale = NULL;
$rec_page = $rowconf['max_ricerche'];
$settimana = time() - 60 * 60 * 24 * 7;
$mese = time() - 60 * 60 * 24 * 31;
$anno = time() - 60 * 60 * 24 * 365;
$start = (isset($_GET['start'])) ? abs(floor(intval($_GET['start']))) : 0;

if (isset($_GET['chiave'])) {
    $chiave = @mysqli_real_escape_string($db, $_GET['chiave']);
    
    if (trim($chiave) == '' || strlen(trim($chiave)) < 4) {
        $error = '<br /><div id="error2">' . $lang['max_min_chars'] . '</div>';
        $doquery = NULL;
    }
    else {
        $error = NULL;
        $doquery = 1;
    }
}
else {
    $chiave = NULL;
    $doquery = NULL;
}

if (isset($_GET['time'])) {
    $time = $_GET['time'];
    
    switch ($time) {
        case 'sett':
            $q_time = "nt.data_pubb >= $settimana";
            $q_field = NULL;
        break;
        case 'mese':
            $q_time = "nt.data_pubb >= $mese";
            $q_field = NULL;
        break;
        case 'anno':
            $q_time = "nt.data_pubb >= $anno";
            $q_field = NULL;
        break;
        case 'sempre':
            $q_time = "nt.data_pubb > 1";
            $q_field = NULL;
        break;
        default:
            $q_time = "nt.data_pubb >= $mese";
            $q_field = NULL;
    }
}
else {
    $q_time = "nt.data_pubb >= $mese";
    $q_field = ", nt.letture";
    $time = "mese";
}

if (isset($_GET['ordine'])) {
    $ordine = $_GET['ordine'];
    
    switch ($ordine) {
        case 'titoli':
            $q_ordine = "nt.titolo ASC";
            $q_field = NULL;
        break;
        case 'datadesc':
            $q_ordine = "nt.data_pubb DESC";
            $q_field = NULL;
        break;
        case 'piulette':
            $q_ordine = "nt.letture DESC";
            $q_field = ", letture";
        break;
        case 'pertinenza':
            $q_ordine = "Pertinenza DESC";
            $q_field = NULL;
        break;
        case 'categoria':
            $q_ordine = "nca.nome_categoria ASC";
            $q_field = NULL;
        break;
        default:
            $q_ordine = "nt.data_pubb DESC";
            $q_field = ", letture";
    }
}
else {
    $q_ordine = "nt.data_pubb DESC";
    $q_field = NULL;
    $ordine = "datadesc";
}

if (isset($_GET['autore'])) {
    $get_autore = intval($_GET['autore']);
    
    switch ($get_autore) {
        case 0:
            $q_autore = "nu.user_id > 0";
        break;
        default:
            $q_autore = "nu.user_id=$get_autore";
    }
}
else {
    $q_autore = "nu.user_id >0";
    $get_autore = "0";
}

if (isset($_GET['categoria'])) {
    $get_categoria = intval($_GET['categoria']);
    
    switch ($get_categoria) {
        case 0:
            $q_categoria = "nt.id_cat > 0";
        break;
        default:
            $q_categoria = "nt.id_cat=$get_categoria";
    }
}
else {
    $q_categoria = "nt.id_cat > 0";
    $get_categoria = "0";
}

    $val_chiave = (isset($_GET['chiave'])) ? htmlspecialchars($_GET['chiave'], ENT_QUOTES, "UTF-8") : NULL;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>              
    <title>                    
      <?php echo $rowconf['nome_sito'] . ' - ' . $lang['cerca_titolo']; ?>    
    </title>              
    <link rel="stylesheet" href="style.css" type="text/css" />              
    <link rel="alternate" type="application/rss+xml" title="Feed RSS News" href="<?php echo $rowconf['url_sito'] . '/' . $news_dir . '/rss.php'; ?>" />         
<script language="JavaScript" src="javascript.js" type="text/JavaScript"></script>         
  </head>        
  <body>              
    <div id="container" style="width: 605px">                    
      <div id="titolo_art" class="text" style="background-color: #F6F6F6"><b><?php echo $lang['cerca_titolo']; ?></b>                    
      </div>                    
      <div id="body_art" class="text" style="background-color: #FFFFFF">                          
        <form name="cerca" id="cerca" method="get" action="search.php"><br /><?php echo $lang['cerca']; ?>
          <input type="text" size="26" name="chiave" maxlength="50" class="searchbox" value="<?php echo $val_chiave; ?>" /> 
          <select name="time">                                      
            <option value="sett"<?php echo (isset($_GET['time']) && $_GET['time'] == "sett") ? ' selected="selected"' : NULL; ?>>                                      
            <?php echo $lang['settimana']; ?>                                        
            </option>                                      
            <option value="mese"<?php echo (isset($_GET['time']) && $_GET['time'] == "mese") ? ' selected="selected"' : NULL; ?>>                                      
            <?php echo $lang['mese']; ?>                                        
            </option>                                      
            <option value="anno"<?php echo (isset($_GET['time']) && $_GET['time'] == "anno") ? ' selected="selected"' : NULL; ?>>                                      
            <?php echo $lang['anno']; ?>                                        
            </option>                                      
            <option value="sempre"<?php echo (isset($_GET['time']) && $_GET['time'] == "sempre") ? ' selected="selected"' : NULL; ?>>                                      
            <?php echo $lang['sempre']; ?>                                        
            </option>                                
          </select> <br /><br /><?php echo $lang['scritte_da']; ?>
          <select name="autore">                                      
            <option value="0"<?php echo (isset($_GET['autore']) && $_GET['autore'] == "0") ? ' selected="selected"' : NULL; ?>><?php echo $lang['da_tutti']; ?>                                       
            </option>
<?php
$res_sel = @mysqli_query($db, "SELECT nu.user_id, nu.nome_cognome FROM `$tab_utenti` nu JOIN `$tab_news` nt ON nt.user_id=nu.user_id WHERE nt.news_approvata = 1 GROUP BY nu.user_id HAVING COUNT(nt.user_id) > 0 ORDER BY nu.nome_cognome ASC");

if (@mysqli_num_rows($res_sel) != 0) {
    
    while ($row_sel = @mysqli_fetch_array($res_sel)) {
        echo '<option value="' . $row_sel['user_id'] . '" ' . (isset($_GET['autore']) && $_GET['autore'] == $row_sel['user_id'] ? ' selected="selected"' : NULL) . '>' . $row_sel['nome_cognome'] . '</option>';
        echo "\n";
    }
}
?>                      
          </select> in
          <select name="categoria">            
            <option value="0"><?php echo $lang['da_tutti']; ?>
            </option>
<?php
$cat_sel = @mysqli_query($db, "SELECT DISTINCT nca.id_cat, nca.nome_categoria FROM `$tab_categorie` nca, `$tab_news` nt WHERE (SELECT COUNT(nt.id_cat) FROM `$tab_news` nt WHERE nt.id_cat=nca.id_cat) > 0 ORDER BY nca.nome_categoria ASC");

while ($row_sel = @mysqli_fetch_array($cat_sel)) {
    echo '<option value="' . $row_sel['id_cat'] . '" ' . (isset($_GET['categoria']) && $_GET['categoria'] == $row_sel['id_cat'] ? ' selected="selected"' : NULL) . '>' . $row_sel['nome_categoria'] . '</option>';
    echo "\n";
}
?>           
          </select> <?php echo $lang['ordina_per']; ?>                
          <select name="ordine">                                      
            <option value="pertinenza"<?php echo (isset($_GET['ordine']) && $_GET['ordine'] == 'pertinenza' ? ' selected="selected"' : NULL); ?>>                         
            <?php echo $lang['pertinenza']; ?>             
            </option>                         
            <option value="datadesc"<?php echo (isset($_GET['ordine']) && $_GET['ordine'] == 'datadesc' ? ' selected="selected"' : NULL); ?>>                                      
            <?php echo $lang['piu_recenti']; ?>                                        
            </option>                                      
            <option value="piulette"<?php echo (isset($_GET['ordine']) && $_GET['ordine'] == 'piulette' ? ' selected="selected"' : NULL); ?>>                                      
            <?php echo $lang['piu_lette']; ?>                                        
            </option>                                      
            <option value="titoli"<?php echo (isset($_GET['ordine']) && $_GET['ordine'] == 'titoli' ? ' selected="selected"' : NULL); ?>>                                      
            <?php echo $lang['titoli_az']; ?>                                        
            </option>              
            <option value="categoria"<?php echo (isset($_GET['ordine']) && $_GET['ordine'] == 'categoria' ? ' selected="selected"' : NULL); ?>>						             
            <?php echo $lang['categoria_az']; ?>             
            </option>          
          </select>&nbsp;            
          <input type="submit" name="submit" value="<?php echo $lang['cerca']; ?>" style="font-weight: bold;" /><br />                          
        </form>
<?php

if ($doquery == 1) {
	
   $result = mysqli_query($db, "SELECT nt.id, nt.titolo, nu.user_id, nca.nome_categoria, nu.nome_cognome, nt.data_pubb$q_field, MATCH(titolo, testo) AGAINST ('$chiave*' IN BOOLEAN MODE) AS Pertinenza 
	FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id AND nt.news_approvata = 1 JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat WHERE MATCH (titolo, testo) AGAINST ('$chiave*' IN BOOLEAN MODE)
	AND $q_time AND $q_autore AND $q_categoria AND nt.data_pubb < " . time() . " ORDER BY $q_ordine LIMIT $start, $rec_page");
    
    $sql_num_totale = mysqli_query($db, "SELECT COUNT(nt.id) AS NumTotale FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id AND nt.news_approvata = 1 AND nt.data_pubb < " . time() . " WHERE MATCH (titolo, testo) AGAINST ('$chiave*' IN BOOLEAN MODE) AND $q_time AND $q_autore AND $q_categoria");
   
	$num_totale_riga = mysqli_fetch_array($sql_num_totale);
	$num_totale = $num_totale_riga['NumTotale'];

    //se la ricerca non produce risultati stampo l'avviso
    $trovata = ($num_totale == 0) ? '<br /><div id="error2">' . $lang['no_results'] . ' <br /><a href="https://www.google.com/search?q=' . $val_chiave . '&amp;sitesearch=' . $rowconf['url_sito'] . '/' . $news_dir . '" title="Google" class="piccolo">' . $lang['no_results_google'] . '</a></div>' : '<br /><span class="text"><b>' . $num_totale . '</b> ' . $lang['risultati'] . ' <b>' . stripslashes(htmlspecialchars($chiave, ENT_QUOTES, "UTF-8")) . '</b></span><br />';
    echo $trovata;
    
    while ($row = mysqli_fetch_array($result)) {

        //seleziono il formato data
        
        switch ($rowconf['formato_data']) {
			case 1:
				$data = date("D j F Y, H:i", $row['data_pubb']);
			break;
			case 2:
				$data = date("l j F Y, H:i", $row['data_pubb']);
			break;
			case 3:
				$data = date("d/m/Y, H:i", $row['data_pubb']);
			break;
			case 4:
				$data = date("d M Y, H:i", $row['data_pubb']);
			break;
			case 5:
				$data = date("d F Y, H:i", $row['data_pubb']);
			break;
			case 6:
				$data = date("m/d/Y, H:i", $row['data_pubb']);
			break;
			case 7:
				$data = date("F d, Y H:i", $row['data_pubb']);
			break;
			case 8:
				$data = date("H:i F d, Y", $row['data_pubb']);
			break;
        }

        //stampo i risultati della ricerca
        $row['titolo'] = str_ireplace($chiave, "<b>" . $chiave . "</b>", $row['titolo']);
        $row['letture'] = (isset($row['letture'])) ? number_format($row['letture'], 0, '', '.') . ' ' . $lang['letture'] . ' - ' : NULL;
        echo '<img src="' . $img_path . '/news.png" alt="" /> ' . $row['letture'] . ' <a href="view.php?id=' . $row['id'] . '">' . $row['titolo'] . '</a> (' . $data . ' - ' . $row['nome_cognome'] . ') [' . $lang['pertinenza'] . ': ' . $row['Pertinenza'] . ' - ' . $lang['categoria'] . ': ' . $row['nome_categoria'] . ']<br />';
    }
}
echo $error;
?><br />                    
      </div>                    
      <div id="tool_art" style="background-color: #F6F6F6">                          
        <a href="archivio.php" class="piccolo">                                
          <img src="<?php echo $img_path; ?>/folder.png" alt="" /><?php echo $lang['archivio']; ?></a> 
           <a href="tags.php" class="piccolo">
			<img src="<?php echo $img_path; ?>/tags.png" alt="" /><?php echo $lang['tags']; ?></a>                   
      </div>
<?php

if ($num_totale > $rec_page) {
    echo '<div id="paginazione" class="text2">';

    //paginazione
    $numero_pagine = ceil($num_totale / $rec_page);
    $pagina_attuale = ceil(($start / $rec_page) + 1);
    echo page_bar("search.php?chiave=" . stripslashes(htmlspecialchars($chiave, ENT_QUOTES, "UTF-8")) . "&amp;categoria=$get_categoria&amp;time=$time&amp;ordine=$ordine&amp;autore=$get_autore", $pagina_attuale, $numero_pagine, $rec_page);
    echo '</div>';
}
?>              
    </div>        
  </body>
</html>
