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
require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/admin/functions.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

//connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

//se c'è l'id della notizia inviato via GET ed è di tipo numerico ed è presente in tabella visualizzo la notizia
$get_id = (isset($_GET['id']) && preg_match('/^[0-9]{1,8}$/', $_GET['id'])) ? intval($_GET['id']) : 0;

//estraggo la notizia selezionata via GET
$sql_news = @mysqli_query($db, "SELECT nt.id, nt.titolo, nt.testo, nt.data_pubb, nt.immagine, nt.nosmile, nu.nome_cognome, nca.nome_categoria FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id JOIN `$tab_categorie` nca ON nca.id_cat=nt.id_cat WHERE nt.id=$get_id AND nt.news_approvata = 1 AND nt.data_pubb < " . time() . "");
$rownews = @mysqli_fetch_array($sql_news);

if (@mysqli_num_rows($sql_news) > 0) {
    $testo = nl2br($rownews['testo']);
    $img_view = ($rownews['immagine'] != '') ? '<div class="imgap"><img src="' . $rownews['immagine'] . '" border="1" alt="" width="96" height="86" /></div>' : NULL;

    //estraggo alcune impostazioni
    $sql_conf = @mysqli_query($db, "SELECT nome_sito, url_sito, formato_data FROM `$tab_config`");
    $rowconf = @mysqli_fetch_array($sql_conf);
    
    function bbCode($testo) {
        global $lang, $img_path, $rownews;

        //sostituisco i bbcode con i tags HTML e con gli smlies
        
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
                '[/li]' => '</li>',
                '&amp;' => '&'
            );
            $testo = strtr($testo, $replace);
        }
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
            '\\2',
            '\\2 [\\1]',
            '\\4\\6',
            '\\9',
            '\\4\\6',
            '\\9 [\\5\\7]',
            '<span style="font-size: \\5pt;">\\7</span>',
			'<span style="color: \\5;">\\7</span>',                
            '<img src="\\2" alt="img" title="" />',
            '<div style="margin:0 auto; width: 99%; font-size: 11px;"><b>' . $lang['citazione'] . '</b></div><div style="margin:0 auto; width:98%; height: auto; border: 1px solid #DEE3E7; padding: 3px;"><i>\\2</i></div>',
            '<div style="margin:0 auto; width: 100%; font-size: 11px;"><b>' . $lang['codice'] . '</b></div><div style="width: 740px; height: auto; padding: 0px; line-height: 6px; font-size: 11px; border: 1px solid #E1E1E1; white-space: nowrap; overflow: auto;"><pre>\\2</pre></div>',
            '[Video: youtube.com/watch?v=\\1]',
            '<iframe src="\\4\\6" width="400" height="300" frameborder="0" style="border:0"></iframe>',
            '<img src="' . $img_path . '/icq.png" alt="" title="ICQ" /> \\1',
            '<img src="' . $img_path . '/skype.png" alt="" title="Skype" /> \\1',
            '<img src="' . $img_path . '/whatsapp.png" alt="" title="Whatsapp" /> \\1',
			'<img src="' . $img_path . '/telegram.png" alt="" title="Telegram" /> \\1',
            '<img src="' . $img_path . '/signal.png" alt="" title="Signal" /> \\1'
        );
        $testo = preg_replace($testo_cerca, $testo_sostituisci, $testo);
        
        return $testo;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>              
    <title>                    
      <?php echo $rownews['titolo'] . ' - ' . $lang['pagina_stampa']; ?>
    </title>              
    <link rel="stylesheet" href="print.css" type="text/css" />              
    <link rel="alternate" type="application/rss+xml" title="Feed RSS News" href="<?php echo $rowconf['url_sito'] . '/' . $news_dir . '/rss.php'; ?>" />
<script language="JavaScript" src="javascript.js" type="text/JavaScript"></script>        
  </head>        
  <body>              
    <div id="container_print">                       
      <div id="logo_print">                               
        <a href="/" title="Home Page">                                
          <img src="/imgs/logo.gif" alt="LOGO" border="0" /></a>                       
      </div>                       
      <div id="tools_print">	                           
        <span id="nascondi"><a href="javascript:;" onclick="img_hide();"><?php echo $lang['senza_immagini']; ?></a></span> 
        <span id="mostra" style="display: none;"><a href="javascript:;" onclick="img_show();"><?php echo $lang['con_immagini']; ?></a></span> - 
        <a href="javascript:;" onclick="self.print();"><?php echo $lang['stampa']; ?></a>                    
      </div><br /><br /><br /><br /><br />                       
      <div id="news_print">
<?php

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
    echo '
<b>' . $lang['titolo'] . '</b>: ' . $rownews['titolo'] . ' <b>' . $lang['autore'] . '</b>: ' . $rownews['nome_cognome'] . '<br />
<b>' . $lang['data'] . '</b>: ' . $data . ' <b>' . $lang['categoria'] . '</b>: ' . $rownews['nome_categoria'] . ' <b>URL</b>: <a href="' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $rownews['id'] . '">' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $rownews['id'] . '</a><br /><br /><br />' . $img_view . bbCode($testo) . ' ';
?>                          
        <p>&nbsp;                           
        </p><br /><br />                          
        <div id="note_print">Copyright &copy;            
          <?php echo $rowconf['nome_sito']; ?> - <?php echo $lang['copyright']; ?>
        </div>                    
      </div>              
    </div>        
  </body>
</html>
<?php
    mysqli_close($db);
}
else {
    echo "No news";
}
?>
