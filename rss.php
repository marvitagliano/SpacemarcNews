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
 
header('Content-type: text/xml; charset=ISO-8859-1');

//includo i file di configurazione
require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/admin/functions.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

//apro la connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

//estraggo alcune impostazioni
$conf = @mysqli_query($db, "SELECT nome_sito, url_sito FROM `$tab_config`");
$rowconf = @mysqli_fetch_array($conf);
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
echo "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
echo "<channel>\n";
echo "<atom:link href=\"" . $rowconf['url_sito'] . "/$news_dir/rss.php\" rel=\"self\" type=\"application/rss+xml\" />\n";
echo "<title>" . $rowconf['nome_sito'] . " - Feed RSS</title>\n";
echo "<link>" . $rowconf['url_sito'] . "</link>\n";
echo "<description>" . $lang['news'] . "</description>\n";
echo "<copyright>Copyright " . $rowconf['nome_sito'] . "</copyright>\n";
echo "<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n";
echo "<language>IT-it</language>\n\n";
$query = @mysqli_query($db, "SELECT nt.id, nt.titolo, nt.testo, nt.data_pubb, nu.nome_cognome FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id WHERE nt.news_approvata = 1 AND nt.data_pubb < " . time() . " ORDER BY nt.data_pubb DESC LIMIT 0,10");

while ($row = @mysqli_fetch_array($query)) {
    $titolo = html_entity_decode($row['titolo'], ENT_QUOTES, "ISO-8859-1");
    $autore = html_entity_decode($row['nome_cognome'], ENT_QUOTES, "ISO-8859-1");
    $testo = nl2br($row['testo']);
    $format_text = array(
		'{\[e\](\r\n|\r|\n)*(.+)\[/e\]}siU',   
        '{\[email\](\r\n|\r|\n)*([a-zA-Z0-9\._-]+@(([a-zA-Z0-9_-])+\.)+[a-z]{2,4})\[/email\]}siU',
        '{\[email=(\w[\w\-\.\+]*?@\w[\w\-\.]*?\w\.[a-zA-Z]{2,4})\](.+)?\[\/email\]}siU',
        '{(\[)(url)(])((http|ftp|https)://)([^;<>\*\(\)\"\s]*)(\[/url\])}siU',
        '{(\[)(url)(=)([\'\"]?)((http|ftp|https)://)([^;<>\*\(\)\"\s]*)(\\4])(.*)(\[/url\])}siU',
        '{(\[)(callto)(])((callto):)([^;<>\*\(\)\"\s]*)(\[/callto\])}siU',
        '{(\[)(callto)(=)([\'\"]?)((callto):)([^;<>\*\(\)\"\s]*)(\\4])(.*)(\[/callto\])}siU',
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
    $replace_text = array(
		'<span class="evidenziato">\\2</span>',       
        '<a href="mailto:\\2">\\2</a>',
        '<a href="mailto:\\1">\\2</a>',
        '<a href="\\4\\6" target="_blank">\\4\\6</a>',
        '<a href="\\5\\7" target="_blank">\\9</a>',
        '<a href="\\4\\6" target="_blank">\\4\\6</a>',
        '<a href="\\5\\7" target="_blank">\\9</a>',
		'\\7',
		'\\7', 
        '<img src="\\2" alt="img" title="" />',
        '[' . $lang['citazione'] . ']<i>\\2</i>[/' . $lang['citazione'] . ']',
        '[' . $lang['codice'] . ']<code>\\2</code>[/' . $lang['codice'] . ']',
        '<a href="http://www.youtube.com/watch?v=\\1" target="_blank">Video Youtube</a>',
        'Google Map: \\4\\6',
        '<img src="' . $img_path . '/icq.png" alt="" title="ICQ" /> \\1',
        '<img src="' . $img_path . '/skype.png" alt="" title="Skype" /> <a href="skype:\\1?chat" title="Skype">\\1</a>',
		'<img src="' . $img_path . '/whatsapp.png" alt="" title="Whatsapp" /> \\1',
		'<img src="' . $img_path . '/telegram.png" alt="" title="Telegram" /> <a href="https://telegram.me/\\1" target="_blank" title="Telegram">\\1</a>',
		'<img src="' . $img_path . '/signal.png" alt="" title="Signal" /> \\1'
    );
    $testo = preg_replace($format_text, $replace_text, $testo);
    $replace_list = array(
        '[ul]' => '<ul>',
        '[/ul]' => '</ul>',
        '[li]' => '<li>',
        '[/li]' => '</li>',
        '[b]' => '<b>',
        '[/b]' => '</b>',
        '[i]' => '<i>',
        '[/i]' => '</i>',
        '[u]' => '<u>',
        '[/u]' => '</u>',
        ':cool:' => '',
		':)' => '',
		':lol:' => '',
		':D' => '',
		';)' => '',
		':o' => '',
		':(' => '',
		':dotto:' => '',
		':wtf:' => '',
		':ehm:' => '',
		':info:' => '',
		':star:' => '',
		':alert:' => '',
		':???:' => '',
		':check:' => '',
		':wiki:' => '',
		':comm:' => '',
		':www:' => '',
		':fb:' =>  '',
		':tw:' => '',
		':ta:' => '',
		':li:' => '',
		':pi:' => '',
		':ig:' => '',
		':yt:' => '',
		':st:' => '',
		':sp:' => '', 
		':he:' => '',
		':cc:' => '',
		':dx:' => '',
		':wa:' => '',
		':appl:' => '',
		':andr:' => '',
		':lin:' => '',
		':win:' => '',
		':dwnl:' => '',
		':gpx:' => '',
		':kml:' => '',
		':kmz:' => '',
		':rar:' => '',
		':zip:' => '',
		':trn:' => '',
		':tel:' => '',
		':email:' => '',		
		':doc:' => '',
		':xls:' => '',
		':pdf:' => '',
		':xml:' => '',
		':man:' => '',
		':jpg:' => '',
		':psd:' => '',
		':clo:' => '',
		':home:' => '',
		':mk:' => ''
    );
    $testo = strtr($testo, $replace_list);
    echo "<item>\n";
    echo "<title><![CDATA[$titolo]]></title>\n";
    echo "<dc:creator><![CDATA[$autore]]></dc:creator>\n";
    echo "<category>News</category>\n";
    echo "<pubDate>" . date("r", $row['data_pubb']) . "</pubDate>\n";
    echo "<guid>" . $rowconf['url_sito'] . "/$news_dir/view.php?id=" . $row['id'] . "</guid>\n";
    echo "<description><![CDATA[$testo]]></description>\n";
    echo "</item>\n\n";
}
echo "</channel>\n";
echo "</rss>";
?>
