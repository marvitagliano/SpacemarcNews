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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>
    <title>
<?php

//se l'id è valido visualizzo la notizia
$get_id = (isset($_GET['id']) && preg_match('/^[0-9]{1,8}$/', $_GET['id'])) ? intval($_GET['id']) : 0;

//includo il file di configurazione
require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/admin/functions.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

//connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

$query_msg = NULL;
$campi_vuoti = NULL;
$errore_autore = NULL;
$errore_email = NULL;
$errore_captcha = NULL;
$link_inserisci = NULL;
$autore_value = NULL;
$email_value = NULL;
$sito_value = NULL;
$commento_value = NULL;
$redirect_report = NULL;

//seleziono la notizia richiesta dall'utente
$sql_news = @mysqli_query($db, "SELECT nt.id, nt.titolo, nt.testo, nt.id_cat, nt.data_pubb, nt.letture, nt.immagine, nt.nosmile, nt.abilita_commenti, nt.notifica_commenti, nu.user_id, nu.nome_cognome, nu.email, nu.attivo, nu.mostra_link, nu.sito, nu.facebook, nu.twitter, nu.instagram, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(id_comm) FROM `$tab_commenti` WHERE id_news=$get_id AND approvato=1) AS TotaleCommenti, (SELECT email FROM `$tab_utenti` WHERE livello_id=1 LIMIT 1) AS EmailAdmin FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat WHERE nt.id=$get_id AND nt.news_approvata = 1 AND nt.data_pubb < " . time() . "");
$rownews = @mysqli_fetch_array($sql_news);

if (@mysqli_num_rows($sql_news) > 0) {

    //aggiorno il numero di letture
    
    if (!isset($_SESSION['loggato'])) {
        mysqli_query($db, "UPDATE `$tab_news` SET letture=letture+1 WHERE id=$get_id");
    }
    $testo = nl2br($rownews['testo']);
    $img_view = ($rownews['immagine'] != '') ? '<div class="imgap"><img src="' . $rownews['immagine'] . '" border="1" alt="" width="96" height="86" /></div>' : NULL;
    
    switch ($rownews['mostra_link']) {
        case 'nome':
            $profilo_autore = $rownews['nome_cognome'];
        break;
        case 'email':
            $newemail = explode("@", $rownews['email']);
            $profilo_autore = '
				<script language="javascript" type="text/javascript">function nospam(user,domain){ document.location.href = "mailto:" + user + "@" + domain; }</script>
				<a href="javascript:nospam(\'' . $newemail[0] . '\',\'' . $newemail[1] . '\')" class="piccolo">' . $rownews['nome_cognome'] . '</a>';
        break;
        case 'sito':
            $profilo_autore = ($rownews['sito'] == '') ? $rownews['nome_cognome'] : '<a href="' . $rownews['sito'] . '" target="_blank" class="piccolo">' . $rownews['nome_cognome'] . '</a>';
        break;
        case 'profilo':
            $profilo_autore = '<a href="javascript:;" onclick="window.open(\'autore.php?user_id=' . $rownews['user_id'] . '\', \'\', \'width=450, height=330, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]" class="piccolo">' . $rownews['nome_cognome'] . '</a>';
        break;
        case 'facebook':
            $profilo_autore = '<a href="https://www.facebook.com/' . $rownews['facebook'] . '" title="Facebook" target="_blank">' . $rownews['nome_cognome'] . '</a>';
        break;
        case 'twitter':
            $profilo_autore = '<a href="https://twitter.com/' . $rownews['twitter'] . '" title="Twitter" target="_blank">' . $rownews['nome_cognome'] . '</a>';
        break;
        case 'instagram':
            $profilo_autore = '<a href="https://instagram.com/' . $rownews['instagram'] . '" title="Instagram">' . $rownews['nome_cognome'] . '</a>';        
		break;
        default:
            $profilo_autore = $rownews['nome_cognome'];
    }

    //estraggo alcune configurazioni
    $sql_conf = @mysqli_query($db, "SELECT nome_sito, url_sito, commenti_per_page, moderazione_commenti, disattivazione_commenti, formato_data FROM `$tab_config`");
    $rowconf = @mysqli_fetch_array($sql_conf);

    //suddivisione commenti per x pagine
    $rec_page = $rowconf['commenti_per_page'];
    $start = (isset($_GET['start'])) ? abs(floor(intval($_GET['start']))) : 0;
    
    function bbCode($testo) {
        global $lang, $img_path, $rownews;

        //sostituisco i bbcode con i tags HTML e con gli smilies
        
        if ($rownews['nosmile'] == 0) {
            $replace = array(
                '[b]' => '<b>',
                '[/b]' => '</b>',
                '[i]' => '<i>',
                '[/i]' => '</i>',
                '[u]' => '<u>',
                '[/u]' => '</u>',
                '[ul]' => '<ul>',
                '[/ul]' => '</ul>',
                '[li]' => '<li>',
                '[/li]' => '</li>',
                '&amp;' => '&',
                ':cool:' => '<img src="' . $img_path . '/cool.gif" alt="" />',
				':)' => '<img src="' . $img_path . '/smile.gif" alt="" />',
				':lol:' => '<img src="' . $img_path . '/tongue.gif" alt="" />',
				':D' => '<img src="' . $img_path . '/biggrin.gif" alt="" />',
				';)' => '<img src="' . $img_path . '/wink.gif" alt="" />',
				':o' => '<img src="' . $img_path . '/ohh.gif" alt="" />',
				':(' => '<img src="' . $img_path . '/sad.gif" alt="" />',
				':dotto:' => '<img src="' . $img_path . '/dotto.gif" alt="" />',
				':wtf:' => '<img src="' . $img_path . '/parolaccia.gif" alt="" />',
				':ehm:' => '<img src="' . $img_path . '/stordito.gif" alt="" />',
				':info:' => '<img src="' . $img_path . '/info.png" alt="" />',
				':star:' => '<img src="' . $img_path . '/star.png" alt="" />',
				':alert:' => '<img src="' . $img_path . '/alert.png" alt="" />',
				':???:' => '<img src="' . $img_path . '/question.png" alt="" />',
				':check:' => '<img src="' . $img_path . '/check.png" alt="" />',
				':wiki:' => '<img src="' . $img_path . '/wikipedia.png" alt="" />',
				':comm:' => '<img src="' . $img_path . '/comm.png" alt="" />',
				':www:' => '<img src="' . $img_path . '/www.png" alt="" />',
				':fb:' => '<img src="' . $img_path . '/facebook.png" alt="" />',
				':tw:' => '<img src="' . $img_path . '/twitter.png" alt="" />',
				':ta:' => '<img src="' . $img_path . '/ta.png" alt="" />',
				':li:' => '<img src="' . $img_path . '/linkedin.gif" alt="" />',
				':pi:' => '<img src="' . $img_path . '/pinterest.png" alt="" />',
				':ig:' => '<img src="' . $img_path . '/instagram.png" alt="" />',
				':yt:' => '<img src="' . $img_path . '/youtube.png" alt="" />',
				':st:' => '<img src="' . $img_path . '/steam.gif" alt="" />',
				':sp:' => '<img src="' . $img_path . '/spotify.png" alt="" />', 
				':he:' => '<img src="' . $img_path . '/heart.png" alt="" />',				
				':cc:' => '<img src="' . $img_path . '/cc.png" alt="" />',
				':dx:' => '<img src="' . $img_path . '/dx.png" alt="" />',
				':wa:' => '<img src="' . $img_path . '/whatsapp.png" alt="" />',
				':appl:' => '<img src="' . $img_path . '/apple.png" alt="" />',
				':andr:' => '<img src="' . $img_path . '/android.png" alt="" />',
				':lin:' => '<img src="' . $img_path . '/icon_tux.png" alt="" />',
				':win:' => '<img src="' . $img_path . '/icon_win.png" alt="" />',
				':dwnl:' => '<img src="' . $img_path . '/icon_download.png" alt="" />',
				':gpx:' => '<img src="' . $img_path . '/icon_gpx.gif" alt="" />',
				':kml:' => '<img src="' . $img_path . '/icon_kml.png" alt="" />',
				':kmz:' => '<img src="' . $img_path . '/icon_kmz.png" alt="" />',
				':rar:' => '<img src="' . $img_path . '/icon_rar.gif" alt="" />',
				':zip:' => '<img src="' . $img_path . '/icon_zip.gif" alt="" />',
				':trn:' => '<img src="' . $img_path . '/icon_torrent.png" alt="" />',
				':tel:' => '<img src="' . $img_path . '/tel.png" alt="" />',
				':email:' => '<img src="' . $img_path . '/mail.png" alt="" />',				
				':doc:' => '<img src="' . $img_path . '/icon_doc.gif" alt="" />',
				':xls:' => '<img src="' . $img_path . '/icon_xls.gif" alt="" />', 
				':pdf:' => '<img src="' . $img_path . '/pdf.gif" alt="" />',
				':xml:' => '<img src="' . $img_path . '/icon_xml.png" alt="" />',
				':man:' => '<img src="' . $img_path . '/profilo.png" alt="" />', 
				':jpg:' => '<img src="' . $img_path . '/icon_jpg.png" alt="" />',
				':psd:' => '<img src="' . $img_path . '/icon_psd.png" alt="" />',
				':clo:' => '<img src="' . $img_path . '/clock.png" alt="" />',
				':home:' => '<img src="' . $img_path . '/icon_home.png" alt="" />',
				':mk:' => '<img src="' . $img_path . '/marker.png" alt="" />'
            );
            $testo = strtr($testo, $replace);
        }
        else {
            $replace = array(
                '[b]' => '<b>',
                '[/b]' => '</b>',
                '[i]' => '<i>',
                '[/i]' => '</i>',
                '[u]' => '<u>',
                '[/u]' => '</u>',
                '[ul]' => '<ul>',
                '[/ul]' => '</ul>',
                '[li]' => '<li>',
                '[/li]' => '</li>'
            );
            $testo = strtr($testo, $replace);
        }

        //cerco eventuali bbcode...
        $testo_cerca = array(
        	'{\[e\](\r\n|\r|\n)*(.+)\[/e\]}siU',
            '{\[email\](\r\n|\r|\n)*([a-zA-Z0-9\._-]+@(([a-zA-Z0-9_-])+\.)+[a-z]{2,4})\[/email\]}siU',
            '{\[email=(\w[\w\-\.\+]*?@\w[\w\-\.]*?\w\.[a-zA-Z]{2,4})\](.+)?\[\/email\]}siU',
            '{(\[)(url)(])((http|ftp|https)://)([^;<>\*\(\)"\s]*)(\[/url\])}siU',
            '{(\[)(url)(=)([\'"]?)((http|ftp|https)://)([^;<>\*\(\)"\s]*)(\\4])(.*)(\[/url\])}siU',
            '{(\[)(callto)(])((callto):)([^;<>\*\(\)"\s]*)(\[/callto\])}siU',
            '{(\[)(callto)(=)([\'"]?)((callto):)([^;<>\*\(\)"\s]*)(\\4])(.*)(\[/callto\])}siU',
            '{(\[)(size)(=)([\'"]?)([0-9]*)(\\4])(.*)(\[/size\])}siU',
			'{(\[)(color)(=)([\'"]?)([a-z]*)(\\4])(.*)(\[/color\])}siU',            
            '{\[img\](\r\n|\r|\n)*((http|https)://([^;<>\*\(\)\"\s]+)|[a-zA-Z0-9/\\\._\- ]+)\[/img\]}siU',
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

        //...e li sostituisco con gli appositi tags HTML
        $testo_sostituisci = array(
			'<span class="evidenziato">\\2</span>',          
            '<a href="mailto:\\2">\\2</a>',
            '<a href="mailto:\\1">\\2</a>',
            '<a href="\\4\\6" target="_blank">\\4\\6</a>',
            '<a href="\\5\\7" target="_blank">\\9</a>',
            '<a href="\\4\\6" target="_blank">\\4\\6</a>',
            '<a href="\\5\\7" target="_blank">\\9</a>',
            '<span style="font-size: \\5pt;">\\7</span>',
			'<span style="color: \\5;">\\7</span>',                 
            '<img src="\\2" alt="img" title="" />',
            '<div style="background-color:#FFFFFF; margin:0 auto; width:100%;" class="text2"><b>' . $lang['citazione'] . '</b></div><div style="background-color:#F9F9F9; margin:0 auto; width:98%; height: auto; border: 1px solid #DEE3E7; padding: 3px;" class="text2">\\2</div>',
            '<div style="background-color:#FFFFFF; margin:0 auto; width:100%;" class="text2"><b>' . $lang['codice'] . '</b></div><div style="background-color:#F9F9F9; width: 98%; height: auto; padding: 3px; line-height: 7px; border: 1px solid #E1E1E1; white-space: nowrap; overflow: auto;" class="text"><pre>\\2</pre></div>',
            '<object width="320" height="265"><param name="movie" value="http://www.youtube.com/v/\\1&hl=it&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/\\1&hl=it&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="320" height="265"></embed></object>',
            '<iframe src="\\4\\6" width="400" height="300" frameborder="0" style="border:0"></iframe>',
            '<img src="' . $img_path . '/icq.png" alt="" title="ICQ" /> \\1',
            '<img src="' . $img_path . '/skype.png" alt="skype" title="Skype" /> <a href="skype:\\1?chat" title="Skype">\\1</a>',
			'<img src="' . $img_path . '/whatsapp.png" alt="" title="Whatsapp" /> \\1',
			'<img src="' . $img_path . '/telegram.png" alt="" title="Telegram" /> <a href="https://telegram.me/\\1" target="_blank" title="Telegram">\\1</a>',
			'<img src="' . $img_path . '/signal.png" alt="" title="Signal" /> \\1'
        );
        $testo = preg_replace($testo_cerca, $testo_sostituisci, $testo);
        
        return $testo;
    } 

	if ( isset($_POST['rbr']) ) {
		
		if (!empty($_POST['web'])) {
			
			@mysqli_query($db, "INSERT INTO `$tab_ban` (ban_ip, dataora, login_errati) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', " . time() . ", 0)");
			
		} else { 
		
			$rbr_array = array('' . $lang['report_spam'] . '', '' . $lang['report_molestie'] . '', '' . $lang['report_ot'] . '');
			$rbr_value = in_array($_POST['rbr'], $rbr_array) ? $_POST['rbr'] : 'a';
			$id_comment_value = is_numeric($_POST['id_comment']) ? $_POST['id_comment'] : 1;

			setcookie("report_comment_" . $id_comment_value, $rbr_value, time() + 604800, "/" . $news_dir);

			$phpversion = (!@phpversion()) ? "N/A" : phpversion();
			$header = "From: " . $_SERVER['SERVER_ADMIN'] . "\n";
			$header.= "Reply-To: " . $_SERVER['SERVER_ADMIN'] . "\n";
			$header.= "Return-Path: " . $_SERVER['SERVER_ADMIN'] . "\n";
			$header.= "X-Mailer: PHP " . $phpversion . "\n";
			$header.= "MIME-Version: 1.0\n";
			$header.= "Content-type: text/plain; charset=ISO-8859-1\n";
			$header.= "Content-Transfer-encoding: 7bit\n";
			@mail($rownews['EmailAdmin'], "" . $rowconf['nome_sito'] . ": " . $lang['report_email_oggetto'] . " ID " . $id_comment_value . "", "" . $lang['report_email_testo'] . " (" . $rbr_value . "):\n " . $rowconf['url_sito'] . "/" . $news_dir . "/viewcomment.php?id_comm=" . $id_comment_value . "\n Reported by: " . $_SERVER['REMOTE_ADDR'] .  "\n-- \n" . $rowconf['url_sito'] . "", $header);
			
			$redirect_report = '
			<script language="JavaScript" type="text/javascript">
			<!--
			function doRedirect() { location.href = "' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $rownews['id'] . '"; }
			window.setTimeout("doRedirect()", 2000);
			//-->
			</script>';
			}
		}
?>
      <?php echo $rownews['titolo'] . ' - ' . $rowconf['nome_sito']; ?>
    </title>        
    <link rel="stylesheet" href="style.css" type="text/css" />
	<script language="JavaScript" src="javascript.js" type="text/JavaScript"></script>      
    <link rel="alternate" type="application/rss+xml" title="Feed RSS News" href="<?php echo $rowconf['url_sito'] . "/" . $news_dir . "/rss.php"; ?>" />     
  </head>        
  <body>
<?php

	echo $redirect_report;
	
    //seleziono il formato data
    
    switch ($rowconf['formato_data']) {
        case 1:
            $data = strftime("%a %d %b %Y, %H:%M", $rownews['data_pubb']);
        break;
        case 2:
            $data = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $rownews['data_pubb']));
        break;
        case 3:
            $data = strftime("%d/%m/%Y, %H:%M", $rownews['data_pubb']);
        break;
        case 4:
            $data = strftime("%d %b %Y, %H:%M", $rownews['data_pubb']);
        break;
        case 5:
            $data = strftime("%d %B %Y, %H:%M", $rownews['data_pubb']);
        break;
        case 6:
            $data = strftime("%m/%d/%Y, %I:%M %p", $rownews['data_pubb']);
        break;
        case 7:
            $data = strftime("%B %d, %Y %I:%M %p", $rownews['data_pubb']);
        break;
        case 8:
            $data = strftime("%I:%M %p %B %d, %Y", $rownews['data_pubb']);
        break;
    }
    echo '<div id="container" style="width: 605px">';
    echo "\n";
    echo '<div id="titolo_art" class="text" style="background-color: #F6F6F6"><div id="imgcat" style="float: left; margin: 0px 0px 10px 0px; width: 35px;"><img src="' . $rownews['img_categoria'] . '" border="0" alt="" title="" width="30" height="30" /></div><b>' . $rownews['titolo'] . '</b><br /><span class="text2"> ' . $lang['di_news'] . ' ' . $profilo_autore . ' - ' . $data . ' [' . $lang['categoria'] . ': <b><a href="archivio.php?sortby=5&amp;categoria=' . $rownews['id_cat'] . '">' . $rownews['nome_categoria'] . '</a></b>]</span><br /></div>';
    echo "\n";
    echo '<div id="body_art" class="text" style="background-color: #FFFFFF"><br />' . $img_view . bbCode($testo) . '<br /><br /><br /><br /></div>';
    echo "\n";

    //condivisione news e tags
    echo '<br /><div id="divtags" class="text2">';
    $sql_tags = @mysqli_query($db, "SELECT nta.id_tag, nta.tag FROM `$tab_tags` nta JOIN `$tab_tags_rel` ntr ON ntr.id_tag=nta.id_tag WHERE ntr.id_news=$get_id ORDER BY nta.tag ASC");
	
	if (mysqli_num_rows($sql_tags) > 0) {
		echo $lang['tags'] . ': ';
	}
	
    while ($rowtags = mysqli_fetch_array($sql_tags)) {
		
		echo '<a href="tags.php?tag=' . $rowtags['tag'] . '" class="tags">' . $rowtags['tag'] . '</a> '; 
    }
    echo '</div><br />';
    
    echo "\n";
    echo '<div id="tool_art" class="text2" style="background-color: #F6F6F6">
<select name="categoria" class="select_categorie" onchange="window.location=this.options[selectedIndex].value">
<option value="archivio.php">' . $lang['categorie_select'] . '</option>';
    $cat_sel = mysqli_query($db, "SELECT DISTINCT nca.id_cat, nca.nome_categoria FROM `$tab_categorie` nca, `$tab_news` nt WHERE (SELECT COUNT(nt.id_cat) FROM `$tab_news` nt WHERE nt.id_cat=nca.id_cat) > 0 ORDER BY nca.nome_categoria ASC");
    
    while ($row_sel = mysqli_fetch_array($cat_sel)) {
        $categoria_selected = ($row_sel['id_cat'] == $rownews['id_cat']) ? ' selected="selected"' : NULL;
        echo '<option value="archivio.php?sortby=5&amp;categoria=' . $row_sel['id_cat'] . '" ' . $categoria_selected . '>' . $row_sel['nome_categoria'] . '</option>';
        echo "\n";
    }
    echo '</select> <a href="archivio.php" class="piccolo"><img src="' . $img_path . '/folder.png" alt="" />' . $lang['archivio'] . '</a> <a href="search.php" class="piccolo"><img src="' . $img_path . '/search.png" alt="" />' . $lang['cerca'] . '</a>&nbsp;<a href="print.php?id=' . $rownews['id'] . '" target="_blank" class="piccolo"><img src="' . $img_path . '/print.png" alt="" />' . $lang['stampa'] . '</a> <a href="getpdf.php?id=' . $rownews['id'] . '" class="piccolo" target="_blank"><img src="' . $img_path . '/pdf.gif" alt="" />PDF</a> <a href="rss.php" class="piccolo"><img src="' . $img_path . '/rss.gif" alt="" />RSS</a> <a href="tags.php" class="piccolo"><img src="' . $img_path . '/tags.png" alt="" />Tags</a> &nbsp; <a href="https://www.facebook.com/sharer/sharer.php?u=' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $rownews['id'] . '&amp;t=' . $rownews['titolo'] . '" target="_blank"><img src="' . $img_path . '/facebook.png" alt="facebook" title="Facebook" /></a> <a href="https://twitter.com/intent/tweet?source=webclient&amp;text=' . $rownews['titolo'] . ' - ' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $rownews['id'] . '" target="_blank"><img src="' . $img_path . '/twitter.png" alt="twitter" title="Twitter" /></a> <a href="https://reddit.com/submit?url=' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $rownews['id'] . '&amp;title=' . $rownews['titolo'] . '" target="_blank"><img src="' . $img_path . '/reddit.png" alt="reddit" title="Reddit" /></a> <a href="https://web.whatsapp.com/send?text=' . $rownews['titolo'] . ': ' . urlencode($rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=') . $rownews['id'] . '" target="_blank"><img src="' . $img_path . '/whatsapp.png" alt="whatsapp" title="Whatsapp" /></a> <a href="javascript:;" onclick="window.open(\'segnala.php?id=' . $rownews['id'] . '\', \'\', \'width=450, height=280, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]" class="piccolo"><img src="' . $img_path . '/mail.png" alt="" title="Email" /></a> &nbsp; ' . $lang['letture'] . ' ' . number_format($rownews['letture'], 0, '', '.') . ' <!-- (C) Spacemarc News --> <a href="http://www.spacemarc.it" target="_blank" class="piccolo" title="Spacemarc News">&copy;</a></div><br />';
    echo "\n";

    // mostro la prima notizia precedente quella corrente
    $sql_prec = @mysqli_query($db, "SELECT nt.id, nt.titolo, nt.data_pubb FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id AND nt.news_approvata = 1 WHERE nt.data_pubb < " . $rownews['data_pubb'] . " AND nt.data_pubb < " . time() . " ORDER BY nt.data_pubb DESC LIMIT 1");
    $rowprec = @mysqli_fetch_array($sql_prec);
    $idprec = $rowprec['id'];
    $titoloprec = $rowprec['titolo'];
    echo '<div class="pager_art" style="width: 300px">';
    
    if ($idprec >= 1) {
        echo '<br />&#129152; <a href="view.php?id=' . $idprec . '" class="piccolo">' . $titoloprec . '</a></div>';
    }
    else {
        echo '&nbsp;</div>';
    }

    //mostro la prima notizia successiva a quella corrente
    $sql_succ = @mysqli_query($db, "SELECT nt.id, nt.titolo, nt.data_pubb FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id AND nt.news_approvata = 1 WHERE nt.data_pubb > " . $rownews['data_pubb'] . " AND nt.data_pubb < " . time() . " ORDER BY nt.data_pubb ASC LIMIT 1");
    $rowsucc = @mysqli_fetch_array($sql_succ);
    
	if ($rowsucc['data_pubb'] < $rownews['data_pubb']) {
        echo '<div></div><br /><br />';
    }
    else {
        $idsucc = $rowsucc['id'];
        $titolosucc = $rowsucc['titolo'];
        echo '<div class="pager_art" align="right" style="width: 300px"><br /><a href="view.php?id=' . $idsucc . '" class="piccolo">' . $titolosucc . '</a> &#129154;</div><br /><br />';	
	}

    echo '</div>';
   
   //controllo se i commenti sono disattivati globalmente
if ( $rowconf['disattivazione_commenti'] == 0 ) {
	
    echo '<br /><div id="commenti" class="text" style="width: 605px">';

    //controllo se un autore ha effettuato l'accesso con sessione
    
    if (isset($_SESSION['loggato'])) {
        $sql_sessione = @mysqli_fetch_array(mysqli_query($db, "SELECT user_id, nome_cognome, email, sito FROM `$tab_utenti` WHERE user_id=" . $_SESSION['user_id'] . " AND attivo=1 LIMIT 1"));
        
        if ($_SESSION['user_id'] == $sql_sessione['user_id']) {
            $autore_value = $sql_sessione['nome_cognome'];
            $email_value = $sql_sessione['email'];
            $sito_value = $sql_sessione['sito'];
            $readonly = 'readonly="readonly"';
            $logout = ' <a href="admin/logout.php?ref=c">' . $lang['logout'] . '</a>';
        }
        else {
            $autore_value = (isset($_POST['author'])) ? htmlspecialchars($_POST['author'], ENT_QUOTES, "ISO-8859-1") : NULL;
            $email_value = (isset($_POST['email'])) ? htmlspecialchars($_POST['email'], ENT_QUOTES, "ISO-8859-1") : NULL;
            $sito_value = (isset($_POST['url'])) ? htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1") : NULL;
			$readonly = '';	            
            $logout = NULL;
        }
    }

    //controllo se un autore ha effettuato l'accesso con cookie
    elseif (isset($_COOKIE['accesso_news'])) {

        $sql_cookie = @mysqli_fetch_array(mysqli_query($db, "SELECT user_id, nome_cognome, email, livello_id, attivo, sito, token FROM `$tab_utenti` WHERE sha1(token)='" . $_COOKIE['accesso_news'] . "' AND attivo=1 LIMIT 1"));
        
        if (sha1($sql_cookie['token']) == $_COOKIE['accesso_news']) {

            //se c'è solo il cookie avvio anche le altre sessioni
            $_SESSION['loggato'] = "login_ok";
            $_SESSION['user_id'] = $sql_cookie['user_id'];
            $_SESSION['email'] = $sql_cookie['email'];            
            $_SESSION['livello_id'] = $sql_cookie['livello_id'];
            $_SESSION['nome_cognome_sess'] = $sql_cookie['nome_cognome'];
            $autore_value = $sql_cookie['nome_cognome'];
            $email_value = $sql_cookie['email'];
            $sito_value = $sql_cookie['sito'];
            $readonly = 'readonly="readonly"';           
			$logout = ' <a href="admin/logout.php?ref=c">' . $lang['logout'] . '</a>';
        }
        else {
            $autore_value = (isset($_POST['author'])) ? htmlspecialchars($_POST['author'], ENT_QUOTES, "ISO-8859-1") : NULL;
            $email_value = (isset($_POST['email'])) ? htmlspecialchars($_POST['email'], ENT_QUOTES, "ISO-8859-1") : NULL;
            $sito_value = (isset($_POST['url'])) ? htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1") : NULL;
			$readonly = '';	
            $logout = NULL;
        }
    }
    else {
		$autore_value = (isset($_POST['author'])) ? htmlspecialchars($_POST['author'], ENT_QUOTES, "ISO-8859-1") : NULL;
		$email_value = (isset($_POST['email'])) ? htmlspecialchars($_POST['email'], ENT_QUOTES, "ISO-8859-1") : NULL;
		$sito_value = (isset($_POST['url'])) ? htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1") : NULL;
		$readonly = '';		
        $logout = NULL;
    }

	//controllo se l'IP del visitatore è bannato
    $bloccaIP = FALSE;
	$ip = explode(".", $_SERVER['REMOTE_ADDR']);
    $sql_ban_ip = @mysqli_query($db, "SELECT ban_ip FROM `$tab_ban` WHERE ban_ip IS NOT NULL AND ban_ip LIKE '" . $ip[0] . ".%' AND login_errati = 0");
		
		while ($row_ban_ip = mysqli_fetch_array($sql_ban_ip)) {
			
			$ipbannato = explode(".", $row_ban_ip['ban_ip']);
		
				if ( ($ipbannato[1] == $ip[1] || $ipbannato[1] == '*') && ($ipbannato[2] == $ip[2] || $ipbannato[2] == '*') && ($ipbannato[3] == $ip[3] || $ipbannato[3] == '*') ) {
					$bloccaIP = TRUE;
                }
		}
            

    
    if ($rownews['abilita_commenti'] == 1 && $bloccaIP == FALSE) {

        //invio commento
        
        if (isset($_POST['submit'])) {

			$commento_value = (isset($_POST['commento'])) ? htmlspecialchars($_POST['commento'], ENT_QUOTES, "ISO-8859-1") : NULL;
          
            if (trim($_POST['author']) == '' || trim($_POST['commento']) == '') {
                $campi_vuoti = '<div id="error">' . $lang['commenti_campi_obb'] . '</div><br />';
            }
            else {
				
				if (!preg_match('/^[.a-zA-Z0-9\-\s]{1,20}$/', $_POST['author'])) {
                    $errore_autore = '<div id="error">' . $lang['solonome'] . ': . a-z A-Z 0-9 - &lt;space&gt;</div><br />';
                }
                else {
					
					if (!preg_match('/^[.a-z0-9_-]+@[.a-z0-9_-]+\.[a-z]{2,4}$/', $_POST['email'])) {
						$errore_email = '<div id="error">' . $lang['wrong_email'] . '</div><br />';
					}
					else {
					
						if (!empty($_POST['web'])) {
							$errore_captcha = '<div id="error">S P A M &nbsp; D E T E C T E D</div><br />';
							@mysqli_query($db, "INSERT INTO `$tab_ban` (ban_ip, dataora, login_errati) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', " . time() . ", 0)");
						} 
						else {
						
							if ($_POST['spamcode'] != @$_SESSION['antispam']) {
								$errore_captcha = '<div id="error">' . $lang['antispam_error'] . '</div><br />';
							} 
						
							else {
								$author = htmlspecialchars($_POST['author'], ENT_QUOTES, "ISO-8859-1");
								$email = htmlspecialchars($_POST['email'], ENT_QUOTES, "ISO-8859-1");
								
								if ( strpos($_POST['url'],'https://') !== false ) {
									$url = htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1");
								} elseif ( strpos($_POST['url'],'http://') !== false )  {
									$url = htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1");
								} elseif ( trim($_POST['url']) == '' )  {
									$url = htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1");
								} else {
									$url = 'http://' . htmlspecialchars($_POST['url'], ENT_QUOTES, "ISO-8859-1");
								}
								
								$commento = htmlspecialchars($_POST['commento'], ENT_QUOTES, "ISO-8859-1");

								$author = mysqli_real_escape_string($db, trim($author));
								$email = mysqli_real_escape_string($db, $email);
								$url = mysqli_real_escape_string($db, trim($url));
								$commento = mysqli_real_escape_string($db, $commento);

								$approvato = ($rowconf['moderazione_commenti'] == 1) ? 0 : 1;
								$approvato_msg = ($rowconf['moderazione_commenti'] == 1) ? $lang['commento_da_approvare'] : $lang['inserisci_commento_ok'];

								//l'autore loggato commenta un proprio articolo
								
								if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rownews['user_id'] && $rownews['attivo'] == 1) {
									$author = $rownews['nome_cognome'];
									$email = $rownews['email'];
									$url = $rownews['sito'];
								}

								//l'autore loggato commenta l'articolo di un altro autore
								elseif (isset($_SESSION['loggato']) && $_SESSION['user_id'] != $rownews['user_id']) {
									$sql_registrato = mysqli_query($db, "SELECT nome_cognome, email, sito FROM `$tab_utenti` WHERE user_id=" . $_SESSION['user_id'] . " LIMIT 1");
									$riga_registrato = mysqli_fetch_array($sql_registrato);
									$author = $riga_registrato['nome_cognome'];
									$email = $riga_registrato['email'];
									$url = $riga_registrato['sito'];
								}

								//il visitatore inserisce l'email dell'autore dell'articolo
								elseif ((!isset($_SESSION['loggato']) && $_POST['email'] == $rownews['email']) || (isset($_SESSION['loggato']) && $_POST['email'] == $rownews['email'] && $rownews['attivo'] == 0)) {
									$email = 'fake@fake.tld';
								}
								else {
									$email = $_POST['email'];
								}
		
									  if (mysqli_query($db, "INSERT INTO `$tab_commenti` (id_news, approvato, commento, autore, data_comm, email_autore, sito_autore, ip_autore) VALUES ($get_id, $approvato, '$commento', '$author', " . time() . ", '$email', '$url', '" . $_SERVER['REMOTE_ADDR'] . "')")) {

										$query_msg = '<div id="success">' . $approvato_msg . '</div>
													 <script language="JavaScript" type="text/javascript">
														 <!--
														 function doRedirect() { location.href = "' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $rownews['id'] . '"; }
														 window.setTimeout("doRedirect()", 2000);
														 //-->
														 </script>';

										//invio notifica all'amministratore per nuovo commento
										
										if ($rownews['notifica_commenti'] == 1 && (!isset($_SESSION['loggato']) || $_SESSION['livello_id'] > 1)) {
											$phpversion = (!@phpversion()) ? "N/A" : phpversion();
											$header = "From: " . $_SERVER['SERVER_ADMIN'] . "\n";
											$header.= "Reply-To: " . $_SERVER['SERVER_ADMIN'] . "\n";
											$header.= "Return-Path: " . $_SERVER['SERVER_ADMIN'] . "\n";
											$header.= "X-Mailer: PHP " . $phpversion . "\n";
											$header.= "MIME-Version: 1.0\n";
											$header.= "Content-type: text/plain; charset=ISO-8859-1\n";
											$header.= "Content-Transfer-encoding: 7bit\n";
											@mail($rownews['EmailAdmin'], "" . $rowconf['nome_sito'] . ": " . $lang['nuovo_commento_email'] . " ID " . $rownews['id'] . "", "" . $lang['nuovo_commento_email'] . ": " . $rowconf['url_sito'] . "/" . $news_dir . "/view.php?id=" . $rownews['id'] . " \n" . $lang['data'] . ": " . date("j M Y G:i:s") . "\n\n-- \n" . $rowconf['url_sito'] . "", $header);

										}
									}
									else {
										$query_msg = '<div id="error">' . $lang['inserisci_commento_error'] . '</div><br />';
									}
						}
					}
				}
            }
        }
	}
        
        $action = (isset($_GET['start'])) ? 'view.php?id=' . $get_id . '&amp;start=' . $start . '#form_commento' : 'view.php?id=' . $get_id . '#form_commento';
        $form_commenti = '
				<a name="form_commento"></a>
				<form action="' . $action . '" method="post" id="form_comm">
				<fieldset>
				<legend><img src="' . $img_path . '/insert.png" border="0" alt="" /> <b>' . $lang['inserisci_commento'] . '</b></legend><br />	
				<input type="text" name="author" size="30" maxlength="40" value="' . $autore_value . '" ' . $readonly . ' style="background-color: #F6F6F6;" /> ' . $lang['nome_commento'] . ' ' . $logout . '<br /><br />
				<input type="text" name="email" size="30" maxlength="50" value="' . $email_value . '" ' . $readonly . ' style="background-color: #F6F6F6;" /> ' . $lang['email_commento'] . '<br /><br />
				<input type="text" name="url" size="30" maxlength="50" value="' . $sito_value . '" ' . $readonly . ' style="background-color: #F6F6F6;" /> ' . $lang['sitoweb_commento'] . '<br /><br />
        <textarea name="commento" id="commento" cols="66" rows="6" style="background-color: #F6F6F6;">' . $commento_value . '</textarea><br />
        ' . captcha() . ' &nbsp; <input name="spamcode" type="text" size="5" maxlength="6" /> <input type="text" name="web" size="10" value="" class="hp" /> &nbsp; <input type="submit" name="submit" value="' . $lang['btn_insert'] . '" style="font-weight: bold;" /><br />
				</fieldset>
				</form>';
    }
    else {
        $link_inserisci = ' - <span style="color: rgb(255, 0, 0);"><b>' . $lang['commenti_disabilitati'] . '</b></span>';
        $action = NULL;
        $form_commenti = NULL;
    }

    //stampo i commenti
    if ($rowconf['disattivazione_commenti'] == 1) {
		
		echo '<div id="elenco_commenti" style="background-color: #E5E5E5"></div><br /><br />';
	
	} elseif ($rowconf['moderazione_commenti'] == 1) {

		echo '<div id="elenco_commenti" style="background-color: #E5E5E5"> <b>' . $lang['commenti'] . ' (' . $rownews['TotaleCommenti'] . ')</b>' . $link_inserisci . ' <span class="text2">' . (($rowconf['moderazione_commenti'] == 1 && $rownews['abilita_commenti'] == 1) ? $lang['testo_moderazione'] : '') . '</span></div><br /><br />';
    
    } else {
		
		echo '<div id="elenco_commenti" style="background-color: #E5E5E5"> <b>' . $lang['commenti'] . ' (' . $rownews['TotaleCommenti'] . ')</b>' . $link_inserisci . ' <span class="text2">' . (($rowconf['moderazione_commenti'] == 1 && $rownews['abilita_commenti'] == 1) ? $lang['testo_moderazione'] : '') . '</span></div><br /><br />';
		
	}
    
    $comm_in_attesa = (isset($_SESSION['loggato']) && $_SESSION['livello_id'] == 1) ? '0,1' : '1';
    $sql_commenti = mysqli_query($db, "SELECT id_comm, id_news, approvato, commento, autore, data_comm, email_autore, sito_autore FROM `$tab_commenti` WHERE id_news=$get_id AND approvato IN ($comm_in_attesa) ORDER BY id_comm DESC LIMIT $start,$rec_page");
    
    for ($i = (isset($_GET['start']) && $_GET['start'] > 0) ? $_GET['start'] + 1 : 1; $riga_comm = mysqli_fetch_array($sql_commenti); ++$i) {
		
        $colore = ($riga_comm['email_autore'] == $rownews['email']) ? '#EEEEFF' : '#F6F6F6';
        
        $start_commento = (isset($_GET['start'])) ? '&amp;start=' . intval($_GET['start']) . '' : '';
        
        switch ($rowconf['formato_data']) {
            case 1:
                $data_comm = strftime("%a %d %b %Y, %H:%M", $riga_comm['data_comm']);
            break;
            case 2:
                $data_comm = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $riga_comm['data_comm']));
            break;
            case 3:
                $data_comm = strftime("%d/%m/%Y, %H:%M", $riga_comm['data_comm']);
            break;
            case 4:
                $data_comm = strftime("%d %b %Y, %H:%M", $riga_comm['data_comm']);
            break;
            case 5:
                $data_comm = strftime("%d %B %Y, %H:%M", $riga_comm['data_comm']);
            break;
            case 6:
                $data_comm = strftime("%m/%d/%Y, %I:%M %p", $riga_comm['data_comm']);
            break;
            case 7:
                $data_comm = strftime("%B %d, %Y %I:%M %p", $riga_comm['data_comm']);
            break;
            case 8:
                $data_comm = strftime("%I:%M %p %B %d, %Y", $riga_comm['data_comm']);
            break;
        }

        //visualizzo il link per modificare i commenti

        if (isset($_SESSION['loggato'])) {
            
            if ($_SESSION['livello_id'] == 1) {
                
                $comm_approvato = ($riga_comm['approvato'] == 1) ? '&nbsp; &#9989; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['modifica'] . '</a>' : '&nbsp; &#9940; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['da_approvare'] . '</a>';
            
            } elseif ($_SESSION['livello_id'] == 3 && $riga_comm['email_autore'] == $email_value) {

				$comm_approvato = ($riga_comm['approvato'] == 1) ? '&nbsp; &#9989; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['modifica'] . '</a>' : '&nbsp; &#9940; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['da_approvare'] . '</a>';
            }
            
            else {
				
                $comm_approvato = '';
            }

        }
      
        elseif (isset($_COOKIE['accesso_news'])) {
            
			if ($sql_cookie['livello_id'] == 1) {
                
                $comm_approvato = ($riga_comm['approvato'] == 1) ? '&nbsp; &#9989; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['modifica'] . '</a>' : '&nbsp; &#9940; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['da_approvare'] . '</a>';
            
            } elseif ($sql_cookie['livello_id'] == 3 && $riga_comm['email_autore'] == $email_value) {

				$comm_approvato = ($riga_comm['approvato'] == 1) ? '&nbsp; &#9989; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['modifica'] . '</a>' : '&nbsp; &#9940; <a href="javascript:;" onclick="window.open(\'admin/modifica_commento.php?id_comm=' . $riga_comm['id_comm'] . '\', \'\', \'width=470, height=350, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]">' . $lang['da_approvare'] . '</a>';
            }
            
            else {
				
                $comm_approvato = '';
            }
        }

        else {
			
            $comm_approvato = '';
        }

        //sostituisco le parole da bannare
        $sql_ban = mysqli_query($db, "SELECT ban_word FROM `$tab_ban` WHERE ban_word IS NOT NULL");
        
        while ($row_ban = @mysqli_fetch_array($sql_ban)) {
            $riga_comm['autore'] = str_ireplace($row_ban['ban_word'], '***', $riga_comm['autore']);
            $riga_comm['sito_autore'] = str_ireplace($row_ban['ban_word'], '-', $riga_comm['sito_autore']);
            $riga_comm['commento'] = str_ireplace($row_ban['ban_word'], '***', $riga_comm['commento']);
        }
        
        $link_autore = ($riga_comm['sito_autore'] != '') ? '<a href="' . $riga_comm['sito_autore'] . '" target="_blank" rel="nofollow">' . $riga_comm['autore'] . '</a>' : $riga_comm['autore'];
        
        $riga_comm['commento'] = preg_replace('~\[qc](.+?);(\d+)\[/qc]~', '<div style="background-color:#FFFFCC; margin:0 auto; width:96%; height: auto; border: 1px solid #DEE3E7; padding: 3px;" class="text2">' . $lang['risposta_a'] . ' <a href="viewcomment.php?id_comm=$2">$1</a></div>', $riga_comm['commento']);

        echo '<a name="commento-' . $riga_comm['id_comm'] . '"></a><div id="testo_commento_' . $i . '" class="text" align="left" style="background-color: ' . $colore . '; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 3px;">' . preg_replace('/([(http|https|ftp)]+:\/\/[\w-?&:;#!~=\.\/\@]+[\w\/])/i', '<a href="$1" target="_blank" rel="nofollow">$1</a>', nl2br($riga_comm['commento'])) . '<br /><br />
			<hr style="color: #444444; border-style: dashed; border-width: 1px 0px 0px 0px; width: 99%; margin-bottom: 4px;" />
			<span style="font-size: 10px;"> <img src="' . $img_path . '/commpub.png" border="0" alt="" /> ' . $link_autore . ' &nbsp; &#128338; <a href="' . $rowconf['url_sito'] . '/' . $news_dir . '/viewcomment.php?id_comm=' . $riga_comm['id_comm'] . '" title="Nr. ' . $riga_comm['id_comm'] . '">' . $data_comm . '</a>  &nbsp; &#8629; <a href="#form_commento" onclick="InserisciQuote(\'[qc]' . $riga_comm['autore'] . ';' . $riga_comm['id_comm'] . '[/qc]\n' . '\'); return true;">' . $lang['quote'] . '</a> &nbsp; &#9888; <a href="javascript:void(0);" onclick="reportComment(\'report_' . $riga_comm['id_comm'] . '\');">' . $lang['report'] . '</a></span> ' . $comm_approvato . '</div><div id="report_' . $riga_comm['id_comm'] . '" class="report">';

if ( isset ( $_COOKIE['report_comment_' . $riga_comm['id_comm'] . ''] ) || $bloccaIP == TRUE ) {
	
	echo $lang['report_segnalato'] . ' &nbsp; <a href="javascript:void(0);" onclick="reportComment(\'report_' . $riga_comm['id_comm'] . '\');"><img src="' . $img_path . '/chiudi.png" alt="" /></a>';

} else {
	
	$action_r = (isset($_GET['start'])) ? 'view.php?id=' . $get_id . '&amp;start=' . $start : 'view.php?id=' . $get_id;
	
	echo '
	<form action="' . $action_r . '" method="post" id="form_report_' . $riga_comm['id_comm'] . '" name="form_report_' . $riga_comm['id_comm'] . '">
	<input type="hidden" name="id_comment" value="' . intval($riga_comm['id_comm']) . '" />     
	<input type="radio" id="rb_spam_' . $riga_comm['id_comm'] . '" name="rbr" value="' . $lang['report_spam'] . '" /><label for="rb_spam_' . $riga_comm['id_comm'] . '">' . $lang['report_spam'] . '</label> 
	<input type="radio" id="rb_molestie_' . $riga_comm['id_comm'] . '" name="rbr" value="' . $lang['report_molestie'] . '" /><label for="rb_molestie_' . $riga_comm['id_comm'] . '">' . $lang['report_molestie'] . '</label>
	<input type="radio" id="rb_ot_' . $riga_comm['id_comm'] . '" name="rbr" value="' . $lang['report_ot'] . '" checked="checked" /><label for="rb_ot_' . $riga_comm['id_comm'] . '">' . $lang['report_ot'] . '</label> 
	<input type="text" name="web" size="10" value="" class="hp" /> 
	 &nbsp; <a href="#" onclick="document[\'form_report_' . $riga_comm['id_comm'] . '\'].submit()">' . $lang['invia'] .  '</a> &nbsp; &nbsp; <a href="javascript:void(0);" onclick="reportComment(\'report_' . $riga_comm['id_comm'] . '\');"><img src="' . $img_path . '/chiudi.png" alt="" /></a>
	</form>';
	}
echo '</div><br />';
        echo "\n";
    }

    //paginazione
    $num_totale = $rownews['TotaleCommenti'];
    $numero_pagine = @ceil($num_totale / $rec_page);
    $pagina_attuale = @ceil(($start / $rec_page) + 1);
    echo '<div class="text2" align="right"> ' . page_bar("view.php?id=$get_id", $pagina_attuale, $numero_pagine, $rec_page) . '</div><br />';
    echo $query_msg . $campi_vuoti . $errore_autore . $errore_email . $errore_captcha;

    //se i commenti sono abilitati e attivati stampo il form
    echo $form_commenti;
    echo '</div>';
    mysqli_close($db);
	
	} //fine controllo commenti disattivati
}

//se l'id della notizia passato via GET non è valido mostro la pagina col messaggio di errore
else {
?>No news</title>              
    <link rel="stylesheet" href="style.css" type="text/css" />    
    </head>              
    <body>                    
      <div class="text2" style="width: 400px;"><?php echo $lang['notizia_inesistente']; ?><br /><a href="archivio.php" class="piccolo"><?php echo $lang['archivio']; ?></a> - <a href="search.php" class="piccolo"><?php echo $lang['cerca']; ?></a>       
      </div>
<?php
}
?>              
    </body>
</html>
