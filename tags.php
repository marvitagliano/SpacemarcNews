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

//connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

//estraggo alcune impostazioni
$conf = @mysqli_query($db, "SELECT nome_sito, url_sito, formato_data, tags_per_page FROM `$tab_config`");
$rowconf = @mysqli_fetch_array($conf);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">           
  <head>                   
    <title>                           
      <?php echo $rowconf['nome_sito'] . ' - ' . $lang['tags']; ?>      
    </title>                   
    <link rel="stylesheet" href="style.css" type="text/css" />                   
    <link rel="alternate" type="application/rss+xml" title="Feed RSS News" href="<?php echo $rowconf['url_sito'] . '/' . $news_dir . '/rss.php'; ?>" />           
  </head>           
  <body>
<div id="container" style="width: 605px">
	<div id="titolo_art" class="text" style="background-color: #F6F6F6"><b><?php echo $lang['tags']; ?></b></div><br />
		<div id="tagcloud">
<?php

//stampo l'elenco delle news che contengono il tag scelto
if ( isset($_GET['tag']) && preg_match('/^([.a-zA-Z0-9-_ ]{2,20})$/', $_GET['tag']) ) {
	
	$paginazione = TRUE;
	$get_tag = htmlspecialchars($_GET['tag'], ENT_QUOTES, "UTF-8");
	$tag_pager = '&amp;tag=' . htmlspecialchars($_GET['tag'], ENT_QUOTES, "UTF-8");
	
	$rec_page = $rowconf['tags_per_page'];
	$start = (isset($_GET['start'])) ? abs(floor(intval($_GET['start']))) : 0;
	$sql_news = @mysqli_query($db, "SELECT nt.id, nt.titolo, nt.data_pubb FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id JOIN `$tab_tags_rel` ntr ON ntr.id_news = nt.id JOIN `$tab_tags` nta ON nta.id_tag = ntr.id_tag WHERE nt.news_approvata = 1 AND ntr.visibile = 1 AND nt.data_pubb < " . time() . " AND ntr.data_pubb_tag < " . time() . " AND nta.tag ='" . mysqli_real_escape_string($db, $get_tag) . "' ORDER BY nt.data_pubb DESC LIMIT $start,$rec_page");

	if (mysqli_num_rows($sql_news) > 0) {
		
		echo $lang['posts_tags'] . '<b>' . $get_tag . '</b>';
		echo '<ul>';
		
		while ($row = @mysqli_fetch_array($sql_news)) {
			
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

			echo '<li><a href="view.php?id=' . $row['id'] . '">' . $row['titolo'] . '</a> (' . $data . ')</li>';
			echo "\n";	
		}
		echo '</ul>';
		
	} else {
		
		echo $lang['no_tag'] . '<br /><br /><br />';
		
	}

	//stampo l'elenco di tutti i tags
	} else {

		$paginazione = FALSE;
		$sql_tag = mysqli_query($db, "SELECT COUNT(ntr.id_tag) AS TagCount, nta.id_tag, nta.tag FROM `$tab_tags` nta JOIN `$tab_tags_rel` ntr ON ntr.id_tag = nta.id_tag JOIN `$tab_news` nt ON nt.id = ntr.id_news WHERE nt.news_approvata=1 AND nt.data_pubb < " . time() . "  AND ntr.visibile = 1 AND ntr.data_pubb_tag < " . time() . " GROUP BY ntr.id_tag ORDER BY nta.tag ASC");

		$terms = array();
		$maximum = 0; 

		while ($row_tag = mysqli_fetch_array($sql_tag)) {

			if ($row_tag['TagCount'] > $maximum) {
				$maximum = $row_tag['TagCount'];
			}
		 
			$terms[] = array('term' => $row_tag['tag'], 'counter' => $row_tag['TagCount']);
		}
		
		ksort($terms);

		foreach ($terms as $term) {

			$percent = floor( ($term['counter'] / $maximum) * 100 );
 
			// in base alla percentuale associo una classe css al tag
			if ($percent < 20) { 
				$class = 'smallest'; 
			} elseif ($percent >= 20 and $percent < 40) {
				$class = 'small'; 
			} elseif ($percent >= 40 and $percent < 60) {
				$class = 'medium';
			} elseif ($percent >= 60 and $percent < 80) {
				$class = 'large';
			} else {
				$class = 'largest';
			}
		echo '<a href="tags.php?tag=' . rawurlencode($term['term']) . '" class="' . $class . '">' . $term['term'] . '</a>';
		echo "\n";
		} 
	}
?>
		</div>
    <br />
    <div id="tool_art" class="text2" style="background-color: #F6F6F6">    
      <a href="search.php" class="piccolo">
        <img src="<?php echo $img_path; ?>/search.png" alt="" /><?php echo $lang['cerca']; ?></a>  
        <a href="archivio.php" class="piccolo">                                
          <img src="<?php echo $img_path; ?>/folder.png" alt="" /><?php echo $lang['archivio']; ?></a> 
           <a href="tags.php" class="piccolo">
			<img src="<?php echo $img_path; ?>/tags.png" alt="" /><?php echo $lang['tags']; ?></a><br />  
              <?php 
              if ($paginazione == TRUE) {
				echo '<div id="paginazione" class="text2">';
				$sql_num_totale = @mysqli_query($db, "SELECT COUNT(nt.id) AS NumTotale FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id JOIN `$tab_tags_rel` ntr ON ntr.id_news = nt.id JOIN `$tab_tags` nta ON nta.id_tag = ntr.id_tag WHERE nt.news_approvata = 1 AND ntr.visibile = 1 AND (nt.data_pubb < " . time() . " OR ntr.data_pubb_tag < " . time() . ") AND nta.tag ='" . mysqli_real_escape_string($db, $_GET['tag']) . "'");
				$num_totale_riga = @mysqli_fetch_array($sql_num_totale);
				$numero_pagine = @ceil($num_totale_riga['NumTotale'] / $rec_page);
				$pagina_attuale = @ceil(($start / $rec_page) + 1);
				echo '<b>(' . $lang['totale'] . ' ' .  $num_totale_riga['NumTotale'] . ')</b> ' . page_bar("tags.php?$tag_pager", $pagina_attuale, $numero_pagine, $rec_page);
				echo '</div>';
				}
				?>
			</div>                   
		</div>           
  </body>
</html>
