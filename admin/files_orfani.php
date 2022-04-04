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

if ($_SESSION['livello_id'] != 1) {
    die("No files");
}

check_form();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>              
    <title><?php echo $lang['files_orfani']; ?>
    </title>              
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>         
  </head>        
  <body>              
    <div align="center">                    
      <span class="text"><b><?php echo $lang['files_orfani']; ?></b> (<?php echo $lang['files_orfani_descr']; ?>)       
      </span><br /><br />                    
      <form method="post" action="files_orfani.php" name="admin">                          
        <table border="0" cellpadding="2" cellspacing="1" width="600">                                
          <tr>                                      
            <td class="text" align="center" width="3%">ID <?php echo $lang['elenco_utenti']; ?></td>                                      
            <td class="text" align="center" width="3%">Tot. <?php echo $lang['news']; ?>*</td>                                      
            <td class="text" align="center" width="5%"><?php echo $lang['file']; ?></td>                                      
            <td class="text" align="center" width="2%"><?php echo $lang['dimensione']; ?></td>                                      
            <td class="text" align="center" width="4%"><?php echo $lang['data_file']; ?></td>                                
          </tr>
<?php
$submit_disabled = 'disabled="disabled"';

//seleziono gli user_id degli utenti e li metto in array
$result = mysqli_query($db, "SELECT user_id FROM `$tab_utenti`");

//seleziono il formato della data
$sql_formato_data = mysqli_query($db, "SELECT formato_data FROM `$tab_config`");
$row_formato_data = mysqli_fetch_assoc($sql_formato_data);

$all_userid = array();

while ($row = mysqli_fetch_array($result)) {
    $all_userid[] = $row['user_id'];
}

if ($handle = opendir("../$file_dir")) {
    
    while (false !== ($file = readdir($handle))) {
        
        if ($file != '.' && $file != '..' && !in_array($file, $all_userid)) {

            //ricavo le info sui file
            
			foreach (glob("../$file_dir/$file/" . "*.{apk,ipa,bmp,gif,jpg,jpeg,png,psd,gpx,kml,kmz,gz,m4a,mp3,mp4,rar,zip,torrent,doc,docx,ods,odt,pdf,xml,xls,xlsx}", GLOB_BRACE) as $filename) {
                $nome = basename($filename);
				$dimensione = round(filesize($filename) / 1024, 1) > 1024 ? round(filesize($filename) / 1024 / 1024, 1) . ' MiB' : round(filesize($filename) / 1024, 1) . ' KiB';
                $submit_disabled = (@count($nome) == 0) ? 'disabled="disabled"' : '';

                //seleziono il formato data
                
                switch ($row_formato_data['formato_data']) {
                    case 1:
                        $data = strftime("%a %d %b %Y, %H:%M", filemtime($filename));
                    break;
                    case 2:
                        $data = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", filemtime($filename)));
                    break;
                    case 3:
                        $data = strftime("%d/%m/%Y, %H:%M", filemtime($filename));
                    break;
                    case 4:
                        $data = strftime("%d %b %Y, %H:%M", filemtime($filename));
                    break;
                    case 5:
                        $data = strftime("%d %B %Y, %H:%M", filemtime($filename));
                    break;
                    case 6:
                        $data = strftime("%m/%d/%Y, %I:%M %p", filemtime($filename));
                    break;
                    case 7:
                        $data = strftime("%B %d, %Y %I:%M %p", filemtime($filename));
                    break;
                    case 8:
                        $data = strftime("%I:%M %p %B %d, %Y", filemtime($filename));
                    break;
                }

                //icone estensione file
                $estensione_file = pathinfo($filename);
                
                switch ($estensione_file['extension']) {
					case 'apk':
						$icon_file = '<img src="' . $img_path . '/android.png" alt="apk" />';
						$sql_where = '';
					break;
					case 'ipa':
						$icon_file = '<img src="' . $img_path . '/apple.png" alt="ipa" />';
						$sql_where = '';
					break;						
                    case 'bmp':
						$icon_file = '<img src="' . $img_path . '/icon_bmp.gif" alt="bmp" />';
						$sql_where = "OR nt.immagine LIKE '%$nome'";
					break;
					case 'gif':
						$icon_file = '<img src="' . $img_path . '/icon_gif.gif" alt="gif" />';
						$sql_where = "OR immagine LIKE '%$nome'";
					break;
					case 'jpg':
						$icon_file = '<img src="' . $img_path . '/icon_jpg.png" alt="jpg" />';
						$sql_where = "OR immagine LIKE '%$nome'";
					break;
					case 'jpeg':
						$icon_file = '<img src="' . $img_path . '/icon_jpg.png" alt="jpeg" />';
						$sql_where = "OR immagine LIKE '%$nome'";
					break;
					case 'png':
						$icon_file = '<img src="' . $img_path . '/icon_png.gif" alt="png" />';
						$sql_where = "OR immagine LIKE '%$nome'";
					break;
					case 'psd':
						$icon_file = '<img src="' . $img_path . '/icon_psd.png" alt="psd" />';
						$sql_where = '';
					break;
					case 'gpx':
						$icon_file = '<img src="' . $img_path . '/icon_gpx.gif" alt="gpx" />';
						$sql_where = '';
					break;
					case 'kml':
						$icon_file = '<img src="' . $img_path . '/icon_kml.png" alt="kml" />';
						$sql_where = '';
					break;
					case 'kmz':
						$icon_file = '<img src="' . $img_path . '/icon_kmz.png" alt="kmz" />';
						$sql_where = '';
					break; 
					case 'gz':
						$icon_file = '<img src="' . $img_path . '/icon_gz.gif" alt="gz" />';
						$sql_where = '';
					break;  
					case 'rar':
						$icon_file = '<img src="' . $img_path . '/icon_rar.gif" alt="rar" />';
						$sql_where = '';
					break;
					case 'zip':
						$icon_file = '<img src="' . $img_path . '/icon_zip.gif" alt="zip" />';
						$sql_where = '';
					break;
					case 'torrent':
						$icon_file = '<img src="' . $img_path . '/icon_torrent.png" alt="torrent" />';
						$sql_where = '';
					break;					
					case 'pdf':
						$icon_file = '<img src="' . $img_path . '/pdf.gif" alt="pdf" />';
						$sql_where = '';
					break;
					case 'ods':
						$icon_file = '<img src="' . $img_path . '/icon_ods.png" alt="ods" />';
						$sql_where = '';
					break;
					case 'odt':
						$icon_file = '<img src="' . $img_path . '/icon_odt.png" alt="odt" />';
						$sql_where = '';
					break;
					case 'doc':
						$icon_file = '<img src="' . $img_path . '/icon_doc.gif" alt="doc" />';
						$sql_where = '';
					break;
					case 'docx':
						$icon_file = '<img src="' . $img_path . '/icon_docx.png" alt="docx" />';
						$sql_where = '';
					break;         
					case 'm4a':
						$icon_file = '<img src="' . $img_path . '/icon_m4a.png" alt="m4a" />';
						$sql_where = '';
					break;
					case 'mp3':
						$icon_file = '<img src="' . $img_path . '/icon_mp3.png" alt="mp3" />';
						$sql_where = '';
					break;   
					case 'mp4':
						$icon_file = '<img src="' . $img_path . '/icon_mp4.png" alt="mp4" />';
						$sql_where = '';
					break; 					   
					case 'xls':
						$icon_file = '<img src="' . $img_path . '/icon_xls.gif" alt="xls" />';
						$sql_where = '';
					break;
					case 'xlsx':
						$icon_file = '<img src="' . $img_path . '/icon_xlsx.png" alt="xlsx" />';
						$sql_where = '';
					break;  
					case 'xml':
						$icon_file = '<img src="' . $img_path . '/icon_xml.png" alt="xml" />';
						$sql_where = '';
					break;  
                }

                //vedo in quante news sono presenti i files
                $file_news = mysqli_query($db, "SELECT nt.id, COUNT(nt.id) AS TotNews FROM `$tab_news` nt JOIN `$tab_utenti` nu ON nu.user_id=nt.user_id WHERE nt.testo LIKE '%$nome%' OR nu.user_id=$file $sql_where GROUP BY nt.id");
                $riga = mysqli_fetch_array($file_news);
                $link_search = ($riga['TotNews'] == 0) ? 0 : '<a href="searchadmin.php?chiave=' . $nome . '&amp;rbw=in_news&amp;time=sempre&amp;autore=0&amp;categoria=0&amp;ordine=datadesc&amp;submit=Cerca" target="_blank" title="Search"><b>' . $riga['TotNews'] . '</b></a>';

                //mostro i file
                echo '<tr><td class="text2" align="center" bgcolor="#EEEEEE">' . $file . '</td>';
                echo "\n";
                echo '<td class="text2" align="center" bgcolor="#EEEEEE">' . $link_search . '</td>';
                echo "\n";
                echo '<td class="text2" align="left" bgcolor="#EEEEEE">' . $icon_file . ' <a href="../' . $file_dir . '/' . $file . '/' . $nome . '" target="blank" class="piccolo">' . $nome . '</a></td>';
                echo "\n";
				echo '<td class="text2" align="center" bgcolor="#EEEEEE">' . $dimensione . '</td>';
                echo "\n";
                echo '<td class="text2" align="center" bgcolor="#EEEEEE">' . $data . '</td></tr>';
            }
        }
    }
    closedir($handle);
}
echo '
<tr><td colspan="5" class="text2" align="left">* ' . $lang['files_orfani_descr2'] . ' <br />' . $lang['attenzione_file'] . '<br /></td></tr>
<tr><td colspan="5" class="text2" align="center"><br />
<input type="hidden" name="post_token" value="' . sha1(session_id()) . '" />
<input type="submit" name="submit" value="' . $lang['delete'] . '" onclick="return confirmSubmit()" style="font-weight: bold;" ' . $submit_disabled . '/>
</td></tr></table></form><br />';

if (isset($_POST['submit'])) {

    //seleziono gli user_id degli utenti e li metto in array
    $result = mysqli_query($db, "SELECT user_id FROM `$tab_utenti`");
    $all_userid = array();
    
    while ($row = mysqli_fetch_array($result)) {
        $all_userid[] = $row['user_id'];
    }
    
    if ($handle = opendir("../$file_dir")) {
        
        while (false !== ($oggetto = readdir($handle))) {
            
            if ($oggetto != "." && $oggetto != ".." && $oggetto != "index.html" && !in_array($oggetto, $all_userid)) {

                //cancello i file e le directories
                $files = glob("../$file_dir/$oggetto/*.*");
                
                foreach ($files as $file) {
                    unlink($file);
                }
                rmdir("../$file_dir/$oggetto");
            }
        }
        closedir($handle);
        echo '<div id="success">' . $lang['file_cancellati'] . '</div><br />
		<script language="JavaScript" type="text/javascript">
		<!--
        	function doRedirect() { location.href = "files_orfani.php"; }
        	window.setTimeout("doRedirect()", 1500);
        	//-->
        	</script>';
    }
}
?>              
    </div>        
  </body>
</html>
