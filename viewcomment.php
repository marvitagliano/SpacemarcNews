<?php

/*****************************************************************
 *  Spacemarc News
 *  Author and copyright (C): Marcello Vitagliano
 *  Web site: www.spacemarc.it
 *  License: GNU General Public License
 *
 *  This program is free software: you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation, either version 3
 *  of the License, or (at your option) any later version.
 *****************************************************************/
 
session_start();
header('Content-type: text/html; charset=ISO-8859-1');

//se l'id è valido visualizzo il commento

if ( isset($_GET['id_comm']) && preg_match('/^[0-9]{1,8}$/', $_GET['id_comm']) ) {
	$get_id_comm = intval($_GET['id_comm']);
} else {
	die('No comment');
}

//includo il file di configurazione
require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/lang/' . $language . '.php');

//connessione a mysql
$db = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

$redirect_report = NULL;

    //estraggo alcune configurazioni
    $sql_conf = @mysqli_query($db, "SELECT nome_sito, url_sito, disattivazione_commenti, formato_data, (SELECT email FROM `$tab_utenti` WHERE livello_id=1 LIMIT 1) AS EmailAdmin FROM `$tab_config`");
    $rowconf = @mysqli_fetch_array($sql_conf);

//controllo se i commenti sono disattivati globalmente
if ( $rowconf['disattivazione_commenti'] == 0 ) {
	
	//controllo se un autore ha effettuato l'accesso con sessione
    
    if (isset($_SESSION['loggato'])) {
        $sql_sessione = @mysqli_fetch_array(mysqli_query($db, "SELECT user_id, email FROM `$tab_utenti` WHERE user_id=" . $_SESSION['user_id'] . " AND attivo=1 LIMIT 1"));
        
        if ($_SESSION['user_id'] == $sql_sessione['user_id']) {
            
            $email_value = $sql_sessione['email'];
            
        }
        else {
            
            $email_value = NULL;
        }
    }

    //controllo se un autore ha effettuato l'accesso con cookie
    elseif (isset($_COOKIE['accesso_news'])) {

        $sql_cookie = @mysqli_fetch_array(mysqli_query($db, "SELECT user_id, nome_cognome, email, livello_id, attivo, token FROM `$tab_utenti` WHERE sha1(token)='" . $_COOKIE['accesso_news'] . "' AND attivo=1 LIMIT 1"));
        
        if (sha1($sql_cookie['token']) == $_COOKIE['accesso_news']) {

            //se c'è solo il cookie avvio anche le altre sessioni
            $_SESSION['loggato'] = "login_ok";
            $_SESSION['user_id'] = $sql_cookie['user_id'];
            $_SESSION['email'] = $sql_cookie['email'];             
            $_SESSION['livello_id'] = $sql_cookie['livello_id'];
            $_SESSION['nome_cognome_sess'] = $sql_cookie['nome_cognome'];
            $email_value = $sql_cookie['email'];
        
        }
        else {
           
            $email_value = NULL;
        }
    }
    else {
		
		$email_value = NULL;

    }
	
    $comm_in_attesa = (isset($_SESSION['loggato']) && $_SESSION['livello_id'] == 1) ? '0,1' : '1';

	$sql_commenti = @mysqli_query($db, "SELECT nc.id_comm, nc.id_news, nc.approvato, nc.commento, nc.autore, nc.data_comm, nc.email_autore, nc.sito_autore, nt.titolo FROM `$tab_commenti` nc, `$tab_utenti` nu, `$tab_news` nt WHERE nc.id_comm=$get_id_comm AND nt.id = nc.id_news AND nc.approvato IN ($comm_in_attesa) LIMIT 1");
    $riga_comm = @mysqli_fetch_array($sql_commenti);
    
    if (@mysqli_num_rows($sql_commenti) < 1) {
		die('No comment');
	}

	$sql_email = @mysqli_query($db, "SELECT email FROM `$tab_utenti` WHERE user_id IN (SELECT user_id FROM `$tab_news` WHERE id=" . $riga_comm['id_news'] . ") LIMIT 1");
    $riga_email = @mysqli_fetch_array($sql_email);
        		
        $colore = ($riga_comm['email_autore'] == $riga_email['email']) ? '#EEEEFF' : '#F6F6F6';
        
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
        
   	if ( isset($_POST['rbr']) ) {
		
		if (!empty($_POST['web'])) {
			
			@mysqli_query($db, "INSERT INTO `$tab_ban` (ban_ip, dataora, login_errati) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', " . time() . ", 0)");
			
		} else { 
		
			$rbr_array = array('' . $lang['report_spam'] . '', '' . $lang['report_molestie'] . '', '' . $lang['report_ot'] . '');
			$rbr_value = in_array($_POST['rbr'], $rbr_array) ? $_POST['rbr'] : 'a';
			
			setcookie("report_comment_" . $riga_comm['id_comm'], $rbr_value, time() + 604800, "/" . $news_dir);

			$phpversion = (!@phpversion()) ? "N/A" : phpversion();
			$header = "From: " . $_SERVER['SERVER_ADMIN'] . "\n";
			$header.= "Reply-To: " . $_SERVER['SERVER_ADMIN'] . "\n";
			$header.= "Return-Path: " . $_SERVER['SERVER_ADMIN'] . "\n";
			$header.= "X-Mailer: PHP " . $phpversion . "\n";
			$header.= "MIME-Version: 1.0\n";
			$header.= "Content-type: text/plain; charset=ISO-8859-1\n";
			$header.= "Content-Transfer-encoding: 7bit\n";
			@mail($rowconf['EmailAdmin'], "" . $rowconf['nome_sito'] . ": " . $lang['report_email_oggetto'] . " ID " . $riga_comm['id_comm'] . "", "" . $lang['report_email_testo'] . " (" .  $rbr_value . "):\n " . $rowconf['url_sito'] . "/" . $news_dir . "/viewcomment.php?id_comm=" . $riga_comm['id_comm'] . "\n Reported by: " . $_SERVER['REMOTE_ADDR'] .  "\n-- \n" . $rowconf['url_sito'] . "", $header);
			
			$redirect_report = '
			<script language="JavaScript" type="text/javascript">
			<!--
			function doRedirect() { location.href = "' . $rowconf['url_sito'] . '/' . $news_dir . '/viewcomment.php?id_comm=' . $get_id_comm . '"; }
			window.setTimeout("doRedirect()", 2000);
			//-->
			</script>';
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>
    <title><?php echo $rowconf['nome_sito'] . ' - ' . $lang['commento'] . ' Nr. '. $riga_comm['id_comm']; ?></title>        
    <link rel="stylesheet" href="style.css" type="text/css" />
	<script language="JavaScript" src="javascript.js" type="text/JavaScript"></script>      
  </head>        
  <body>
<?php
		echo '<div id="commenti" class="text" style="width: 100%">';
        echo '<a name="commento-' . $riga_comm['id_comm'] . '"></a><div id="testo_commento" class="text" align="left" style="background-color: ' . $colore . '; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 3px;">' . preg_replace('/([(http|https|ftp)]+:\/\/[\w-?&:;#!~=\.\/\@]+[\w\/])/i', '<a href="$1" target="_blank" rel="nofollow">$1</a>', nl2br($riga_comm['commento'])) . '<br /><br />
			<hr style="color: #444444; border-style: dashed; border-width: 1px 0px 0px 0px; width: 99%; margin-bottom: 4px;" />
			<span style="font-size: 10px;"> <img src="' . $img_path . '/commpub.png" border="0" alt="" /> ' . $link_autore . ' &nbsp; &#128338; ' . $data_comm . ' &nbsp; &#9888; <a href="javascript:void(0);" onclick="reportComment(\'report_' . $riga_comm['id_comm'] . '\');">' . $lang['report'] . '</a></span> ' . $comm_approvato . '</div><div id="report_' . $riga_comm['id_comm'] . '" class="report">';

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
		
if ( isset ( $_COOKIE['report_comment_' . $riga_comm['id_comm'] . ''] ) || $bloccaIP == TRUE ) {
	
	echo $lang['report_segnalato'] . ' &nbsp; <a href="javascript:void(0);" onclick="reportComment(\'report_' . $riga_comm['id_comm'] . '\');"><img src="' . $img_path . '/chiudi.png" alt="" /></a>';

} else {
	
echo '
	<form action="viewcomment.php?id_comm=' . $get_id_comm . '" method="post" id="form_report_' . $riga_comm['id_comm'] . '" name="form_report_' . $riga_comm['id_comm'] . '">
	<input type="radio" id="rb_spam_' . $riga_comm['id_comm'] . '" name="rbr" value="' . $lang['report_spam'] . '" /><label for="rb_spam_' . $riga_comm['id_comm'] . '">' . $lang['report_spam'] . '</label> 
	<input type="radio" id="rb_molestie_' . $riga_comm['id_comm'] . '" name="rbr" value="' . $lang['report_molestie'] . '" /><label for="rb_molestie_' . $riga_comm['id_comm'] . '">' . $lang['report_molestie'] . '</label>
	<input type="radio" id="rb_ot_' . $riga_comm['id_comm'] . '" name="rbr" value="' . $lang['report_ot'] . '" checked="checked" /><label for="rb_ot_' . $riga_comm['id_comm'] . '">' . $lang['report_ot'] . '</label> 
	<input type="text" name="web" size="10" value="" class="hp" /> 	
	 &nbsp; <a href="#" onclick="document[\'form_report_' . $riga_comm['id_comm'] . '\'].submit()">' . $lang['invia'] .  '</a> &nbsp; &nbsp; <a href="javascript:void(0);" onclick="reportComment(\'report_' . $riga_comm['id_comm'] . '\');"><img src="' . $img_path . '/chiudi.png" alt="" /></a>
	</form>';
	echo $redirect_report;
	}

echo '</div>
<br /><br /> &#11013; <a href="' . $rowconf['url_sito'] . '/' . $news_dir . '/view.php?id=' . $riga_comm['id_news'] . '">' . $lang['back_news'] . '</a>: ' . $riga_comm['titolo'] . '
</div>';

    mysqli_close($db);
	
	} else {

		echo 'No comment';

		}
?>            
    </body>
</html>
