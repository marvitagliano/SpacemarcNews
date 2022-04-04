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

if (isset($_GET['user_id']) && preg_match('/^[0-9]{1,5}$/', $_GET['user_id'])) {
    $get_dir = intval($_GET['user_id']);
    $checkid = mysqli_query($db, "SELECT nu.user_id, nu.nome_cognome, nc.formato_data FROM `$tab_utenti` nu, `$tab_config` nc WHERE user_id=$get_dir LIMIT 1");
    $rigaid = mysqli_fetch_assoc($checkid);
    
    if (mysqli_num_rows($checkid) == 0) {
        die("No files");
    }
    
    if ($_SESSION['livello_id'] != 1 && $get_dir != $_SESSION['user_id'] && $rigaid['user_id'] != $_SESSION['user_id']) {
        die("No files");
    }
    
check_form();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['file']; ?>     
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>      
  </head>     
  <body>         
    <div align="center">

<?php
    $dir = "../$file_dir/$get_dir";
    $submit_disabled = 'disabled="disabled"';
    $file_array = array();
    $id_file = 0;
    
    if ($handle = @opendir($dir)) {
        
        while (false !== ($file = readdir($handle))) {
            
            if ($file != "." && $file != ".." && $file != 'index.html') {
                $file_array[] = array(
                    $file,
                    round(filesize($dir . '/' . $file) / 1024, 1) ,
                    filemtime($dir . '/' . $file)
                );
            }
        }
        closedir($handle);
    }

    //se richiamo la pagina dal form di inserimento news mostro anche il link per inserire i file
    
    if (isset($_GET['modo']) && $_GET['modo'] === 'news') {
        $file_url = "http://" . $_SERVER['HTTP_HOST'] . "/" . $upload_path . "/" . $get_dir;
        $get_modo = 'modo=news&';
    }
    else {
        $file_url = '';
        $get_modo = '';
    }
    
    if (isset($_GET['sortby']) && $_GET['sortby'] != '') {
        
        switch ($_GET['sortby']) {
            case 'file_asc':
                $fa_type = 0;
                $fa_order = SORT_ASC;
                $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_desc">' . $lang['file'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
                $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_asc">' . $lang['dimensione'] . '</a>';
                $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_asc">' . $lang['data_file'] . '</a>';
            break;
            case 'file_desc':
                $fa_type = 0;
                $fa_order = SORT_DESC;
                $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_asc">' . $lang['file'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
                $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_asc">' . $lang['dimensione'] . '</a>';
                $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_asc">' . $lang['data_file'] . '</a>';
            break;
            case 'size_asc':
                $fa_type = 1;
                $fa_order = SORT_ASC;
                $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_asc">' . $lang['file'] . '</a>';
                $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_desc">' . $lang['dimensione'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
                $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_asc">' . $lang['data_file'] . '</a>';
            break;
            case 'size_desc':
                $fa_type = 1;
                $fa_order = SORT_DESC;
                $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_asc">' . $lang['file'] . '</a>';
                $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_asc">' . $lang['dimensione'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
                $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_asc">' . $lang['data_file'] . '</a>';
            break;
            case 'data_asc':
                $fa_type = 2;
                $fa_order = SORT_ASC;
                $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_asc">' . $lang['file'] . '</a>';
                $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_asc">' . $lang['dimensione'] . '</a>';
                $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_desc">' . $lang['data_file'] . '</a> <img src="' . $img_path . '/asc.gif" alt="ASC" title="ASC" />';
            break;
            case 'data_desc':
                $fa_type = 2;
                $fa_order = SORT_DESC;
                $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_asc">' . $lang['file'] . '</a>';
                $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_asc">' . $lang['dimensione'] . '</a>';
                $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_asc">' . $lang['data_file'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            break;
            default:
                $fa_type = 2;
                $fa_order = SORT_DESC;
                $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_asc">' . $lang['file'] . '</a>';
                $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_asc">' . $lang['dimensione'] . '</a>';
                $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_asc">' . $lang['data_file'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
            break;
        }
    }
    else {
        $fa_type = 2;
        $fa_order = SORT_DESC;
        $link_file = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=file_asc">' . $lang['file'] . '</a>';
        $link_size = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=size_asc">' . $lang['dimensione'] . '</a>';
        $link_data = '<a href="files.php?' . $get_modo . 'user_id=' . $get_dir . '&amp;sortby=data_asc">' . $lang['data_file'] . '</a> <img src="' . $img_path . '/desc.gif" alt="DESC" title="DESC" />';
    }
    
    foreach ($file_array as $fa) {
        $tos[] = $fa[$fa_type]; // sort by 0=name 1=size 2=date

        
    }
    @array_multisort($tos, $fa_order, $file_array);
    echo '<span class="text"><b>' . $lang['file_inviati'] . ' ' . $rigaid['nome_cognome'] . '</b></span><br /><br />';
    echo '<form method="post" action="files.php?' . $get_modo . 'user_id=' . $get_dir . '" name="admin">';
    echo '<table border="0" cellpadding="2" cellspacing="1" width="600">';
    echo '<tr><td>&nbsp;</td>';
    echo '<td class="text" align="center">Tot. ' . $lang['news'] . '*</td>';
    echo '<td class="text" align="center">' . $link_file . '</td>';
    echo '<td class="text" align="center">' . $link_size . '</td>';
    echo '<td class="text" align="center">' . $link_data . '</td></tr>';
    $submit_disabled = (@count($fa[0]) == 0) ? 'disabled="disabled"' : '';
    
    foreach ($file_array as $fa) {
        ++$id_file;

        //seleziono il formato data
        
        switch ($rigaid['formato_data']) {
            case 1:
                $data = strftime("%a %d %b %Y, %H:%M", $fa[2]);
            break;
            case 2:
                $data = str_replace("ì", "&igrave;", strftime("%A %d %B %Y, %H:%M", $fa[2]));
            break;
            case 3:
                $data = strftime("%d/%m/%Y, %H:%M", $fa[2]);
            break;
            case 4:
                $data = strftime("%d %b %Y, %H:%M", $fa[2]);
            break;
            case 5:
                $data = strftime("%d %B %Y, %H:%M", $fa[2]);
            break;
            case 6:
                $data = strftime("%m/%d/%Y, %I:%M %p", $fa[2]);
            break;
            case 7:
                $data = strftime("%B %d, %Y %I:%M %p", $fa[2]);
            break;
            case 8:
                $data = strftime("%I:%M %p %B %d, %Y", $fa[2]);
            break;
        }

        //icone estensione file
        $estensione_file = pathinfo($fa[0]);
        
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
                $sql_where = "OR immagine LIKE '%$fa[0]'";
            break;
            case 'gif':
                $icon_file = '<img src="' . $img_path . '/icon_gif.gif" alt="gif" />';
                $sql_where = "OR immagine LIKE '%$fa[0]'";
            break;
            case 'jpg':
                $icon_file = '<img src="' . $img_path . '/icon_jpg.png" alt="jpg" />';
                $sql_where = "OR immagine LIKE '%$fa[0]'";
            break;
            case 'jpeg':
                $icon_file = '<img src="' . $img_path . '/icon_jpg.png" alt="jpeg" />';
                $sql_where = "OR immagine LIKE '%$fa[0]'";
            break;
            case 'png':
                $icon_file = '<img src="' . $img_path . '/icon_png.gif" alt="png" />';
                $sql_where = "OR immagine LIKE '%$fa[0]'";
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
        $file_news = mysqli_query($db, "SELECT COUNT(id) AS TotNews FROM `$tab_news` WHERE testo LIKE '%$fa[0]%' $sql_where");
        $riga = mysqli_fetch_array($file_news);
        $link_search = ($riga['TotNews'] == 0) ? 0 : '<a href="searchadmin.php?chiave=' . $fa[0] . '&amp;rbw=in_news&amp;time=sempre&amp;autore=' . $get_dir . '&amp;categoria=0&amp;ordine=datadesc&amp;submit=Cerca" target="_blank" title="' . $lang['cerca'] . '"><b>' . $riga['TotNews'] . '</b></a>';
        echo '<tr><td align="center" bgcolor="#EEEEEE"><input type="checkbox" name="cb_id[]" value="' . $fa[0] . '" id="f_' . $id_file . '" /></td>';
        echo "\n";
        echo '<td align="center" bgcolor="#EEEEEE" class="text2">' . $link_search . '</td>';
        echo "\n";
        
        if (isset($_GET['modo']) && $_GET['modo'] === 'news') {
            
            if ($estensione_file['extension'] == 'gif' || $estensione_file['extension'] == 'jpg' || $estensione_file['extension'] == 'jpeg' || $estensione_file['extension'] == 'png') {

				if ( substr($fa[0], -7, 3) == '_th' || substr($fa[0], -8, 3) == '_th' ) {
						$img_big = str_replace('_th', '', $file_url . '/' . $fa[0]);
						$link_inserisci = '[<a href="javascript:;" onclick="addFile(\'[url=' . $img_big . '][img]' . $file_url . '/' . $fa[0] . '[/img][/url]\'); return(false);" class="piccolo">' . $lang['insert_file'] . '</a>]';
					}
					else {
						$link_inserisci = '[<a href="javascript:;" onclick="addFile(\'[img]' . $file_url . '/' . $fa[0] . '[/img]\'); return(false);" class="piccolo">' . $lang['insert_file'] . '</a>]';
					}
            }
            else {
				$size_file = round(filesize($dir . '/' . $fa[0]) / 1024, 1) > 1024 ? round(filesize($dir . '/' . $fa[0]) / 1024 / 1024, 1) . ' MiB' : round(filesize($dir . '/' . $fa[0]) / 1024, 1) . ' KiB';
                $link_inserisci = '[<a href="javascript:;" onclick="addFile(\' [url=' . $file_url . '/' . $fa[0] . ']' . $fa[0] . ' ('. $size_file . ')[/url] \'); return(false);" class="piccolo">' . $lang['insert_file'] . '</a>]';
            }
        }
        else {
            $link_inserisci = '';
        }

		$size_file = round(filesize($dir . '/' . $fa[0]) / 1024, 1) > 1024 ? round(filesize($dir . '/' . $fa[0]) / 1024 / 1024, 1) . ' MiB' : round(filesize($dir . '/' . $fa[0]) / 1024, 1) . ' KiB';
        echo '<td class="text2" align="left" bgcolor="#EEEEEE" >' . $icon_file . ' <a href="' . $dir . '/' . $fa[0] . '" target="blank" class="piccolo">' . $fa[0] . '</a> ' . $link_inserisci . '</td>';
        echo '<td class="text2" align="center" bgcolor="#EEEEEE">' . $size_file . '</td>';
        echo '<td class="text2" align="center" bgcolor="#EEEEEE">' . $data . '</td></tr>';
    }
    echo '<tr><td colspan="5" class="text2" align="left">* ' . $lang['files_orfani_descr2'] . ' <br />' . $lang['attenzione_file'] . '<br /><br />' . $lang['select'] . ' <a href="javascript:onClick=checkTutti()" class="piccolo">' . $lang['select_all'] . '</a>, <a href="javascript:onClick=uncheckTutti()" class="piccolo">' . $lang['select_none'] . '</a> <input type="hidden" name="post_token" value="' . sha1(session_id()) . '" />  <input type="submit" name="canc_file" value="' . $lang['delete'] . '" onclick="return confirmSubmit()" style="font-weight: bold;" ' . $submit_disabled . ' /></td></tr>';
    echo '</table></form><br />';
    
    if (isset($_POST['canc_file'])) {
        
        if (isset($_POST['cb_id'])) {
            $uid = implode(",", $_POST['cb_id']);
            
            if (count($_POST['cb_id']) == 1) {
                @unlink($dir . '/' . $uid);
                echo '<div id="success">' . $lang['file_cancellato'] . '</div>';
                echo '<script language="JavaScript" type="text/javascript">
		<!--
		function doRedirect() { location.href = "files.php?' . $get_modo . 'user_id=' . $get_dir . '"; }
		window.setTimeout("doRedirect()", 1500);
		//-->
		</script>';
            }
            else {
                $dirs = explode(",", $uid);
                
                foreach ($dirs as $del_dirs) {
                    @unlink($dir . '/' . $del_dirs);
                    $del_msg = '<div id="success">' . $lang['file_cancellati'] . '</div>
				<script language="JavaScript" type="text/javascript">
				<!--
				function doRedirect() { location.href = "files.php?' . $get_modo . 'user_id=' . $get_dir . '"; }
				window.setTimeout("doRedirect()", 1500);
				//-->
				</script>';
                }
                echo $del_msg;
            }
        }
    }
}
else {
    die("No user");
}
?>         
    </div>     
  </body>
</html>
