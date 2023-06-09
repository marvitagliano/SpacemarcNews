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

if (is_dir('../install')) {
    die("ATTENZIONE! DEVI CANCELLARE, SPOSTARE O RINOMINARE LA DIRECTORY ../install");
}

function check_login() {
    global $dir_admin, $tab_utenti, $db;
    $cookie = (isset($_COOKIE['accesso_news'])) ? $_COOKIE['accesso_news'] : NULL;
    
    if ($cookie) {

        $result = mysqli_query($db, "SELECT user_id, nome_cognome, email, livello_id, attivo, token FROM `$tab_utenti` WHERE sha1(token)='$cookie' LIMIT 1");
        $riga = mysqli_fetch_assoc($result);
        
        if (sha1($riga['token']) !== $cookie || $riga['attivo'] == 0) {
            header('Location: ' . $dir_admin . '/logout.php');
            exit();
        }
        else {
            $_SESSION['loggato'] = "login_ok";
            $_SESSION['user_id'] = $riga['user_id'];
            $_SESSION['email'] = $riga['email'];            
            $_SESSION['livello_id'] = $riga['livello_id'];
            $_SESSION['nome_cognome_sess'] = $riga['nome_cognome'];

            //controllo ultima attività dell'utente (ogni 5 minuti)
            
            if (!isset($_SESSION['ultimo_accesso']) || time() - $_SESSION['ultimo_accesso'] > 300) {
                mysqli_query($db, "UPDATE `$tab_utenti` SET ultimo_accesso=" . time() . " WHERE user_id=" . $riga['user_id']);
                $_SESSION['ultimo_accesso'] = time();
				}
			}
		}
		
    elseif (isset($_SESSION['loggato'])) {
        $result = mysqli_query($db, "SELECT user_id, attivo FROM `$tab_utenti` WHERE user_id=" . $_SESSION['user_id'] . " LIMIT 1");
        $riga = mysqli_fetch_assoc($result);
        
        if ($riga['attivo'] == 0) {
            header('Location: ' . $dir_admin . '/logout.php');
            exit();
        }

        //controllo ultima attività dell'utente (ogni 5 minuti)
        
        if (time() - $_SESSION['ultimo_accesso'] > 300) {
            mysqli_query($db, "UPDATE `$tab_utenti` SET ultimo_accesso=" . time() . " WHERE user_id=" . $riga['user_id']);
            $_SESSION['ultimo_accesso'] = time();
            }
		}
    else {
        header('Location: ' . $dir_admin . '/logout.php');
        exit();
    }
}

function passwords() {

    global $lang, $user_password_new, $user_password1_error, $user_password2_error, $user_password2_short, $user_password2_empty, $user_password3_error, $user_password_ok, $q_riga;
    
    if (trim($_POST['user_password1']) != '' && !password_verify($_POST['user_password1'], $q_riga['user_password']) ) {
        $user_password1_error = '<div id="error2">' . $lang['pwd_no_corr'] . '</div>';
        $user_password2_error = NULL;
        $user_password2_short = NULL;
        $user_password_new = $q_riga['user_password'];
        $user_password_ok = 0;
    }
    elseif (trim($_POST['user_password2']) != '' && trim($_POST['user_password1']) == '') {
        $user_password1_error = NULL;
        $user_password2_error = '<div id="error2">' . $lang['pwd_att'] . '</div>';
        $user_password2_short = NULL;
        $user_password_new = $q_riga['user_password'];
        $user_password_ok = 0;
    }
    elseif (trim($_POST['user_password2']) != '' && trim(strlen($_POST['user_password2'])) < 8) {
        $user_password2_short = '<div id="error2">' . $lang['pwd_min_chars'] . '</div>';
        $user_password_new = $q_riga['user_password'];
        $user_password_ok = 0;
    }
    elseif (trim($_POST['user_password1']) != '' && trim($_POST['user_password2']) == '') {
        $user_password2_empty = '<div id="error2">' . $lang['pwd_new'] . '</div>';
        $user_password_new = $q_riga['user_password'];
        $user_password_ok = 0;
    }
    elseif (trim($_POST['user_password1']) == '' && trim($_POST['user_password2']) == '') {
        $user_password1_error = NULL;
        $user_password_new = $q_riga['user_password'];
        $user_password_ok = 1;
    }
    elseif (trim($_POST['user_password2']) != '' && $_POST['user_password3'] != $_POST['user_password2']) {
        $user_password3_error = '<div id="error2">' . $lang['pwd_new_diverse'] . '</div>';
        $user_password_new = $q_riga['user_password'];
        $user_password_ok = 0;
    }
    else {
        $user_password_new = password_hash($_POST['user_password2'], PASSWORD_DEFAULT);
        $user_password_ok = 1;
    }
}

function check_form () {
	if ( count($_POST) > 0 && ( !isset($_POST['post_token']) || $_POST['post_token'] !== sha1(session_id()) )) {
		die ('Error');
    }  
}

function nome_cognome() {

    //controllo nome_cognome
    global $lang, $q_riga, $nome_cognome_errato, $user_nome_cognome_ok, $nome_cognome;
    
    if (isset($_POST['nome_cognome']) && $_POST['nome_cognome'] != $q_riga['nome_cognome']) {
        $nome_cognome = trim($_POST['nome_cognome']);
    }
    else {
        $nome_cognome = $q_riga['nome_cognome'];
    }
    
    if (trim($_POST['nome_cognome']) == '') {
        $nome_cognome_errato = '<div id="error2">' . $lang['required'] . '</span>';
        $user_nome_cognome_ok = 0;
    }
    else {
        $nome_cognome_errato = NULL;
        $user_nome_cognome_ok = 1;
    }
}

function check_email() {
    global $lang, $email_errata, $email_esiste, $email_ok, $user_email_ok, $user_email_nascosta_val, $tab_utenti, $q_riga, $email, $db;
    
    if (isset($_POST['email']) && $_POST['email'] != $q_riga['email']) {
        $email = trim($_POST['email']);
    }
    else {
        $email = $q_riga['email'];
    }

    //controllo validità indirizzo email
    
    if (!preg_match('/^[.a-z0-9_-]+@[.a-z0-9_-]+\.[a-z]{2,4}$/', $email)) {
        $email_errata = '<div id="error2">' . $lang['wrong_email'] . '</div>';
        $user_email_ok = 0;
    }
    else {
        $email_errata = NULL;
        $user_email_ok = 1;
    }
    
    if (isset($_POST['user_email_nascosta'])) {
        $user_email_nascosta_val = 1;
    }
    else {
        $user_email_nascosta_val = 0;
    }

    //verifico se l'email già esiste
    
    if (isset($_GET['user_id'])) {
        
		$_POST['email'] = mysqli_real_escape_string($db, $_POST['email']);
        $email_result = mysqli_query($db, "SELECT user_id, email FROM `$tab_utenti` WHERE user_id !=" . intval($_GET['user_id']) . " AND email='" . $_POST['email'] . "' LIMIT 1");
    }
    else {
        
		$_POST['email'] = mysqli_real_escape_string($db, $_POST['email']);
        $email_result = mysqli_query($db, "SELECT user_id, email FROM `$tab_utenti` WHERE user_id !=" . intval($_SESSION['user_id']) . " AND email='" . $_POST['email'] . "' LIMIT 1");
    }
    $riga = mysqli_fetch_array($email_result);
    
    if (mysqli_num_rows($email_result) > 0) {
        $email_esiste = '<div id="error2">' . $lang['user_email_exists'] . '</div>';
        $email_ok = 0;
    }
    else {
        $email_esiste = NULL;
        $email_ok = 1;
    }
    
    if ($email_ok == 0 && $user_email_ok == 0) {
        $email_esiste = NULL;
    }
}

function data_nascita() {
    global $lang, $data_nascita_errata, $user_data_nascita_ok, $user_data_nascita2;
    
    if (trim($_POST['data_nascita']) != '') {
        $data_nascita = $_POST['data_nascita'];
        
        if (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $data_nascita)) {
            $data_nascita_errata = '<div id="error2">' . $lang['wrong_date'] . '</div>';
            $user_data_nascita_ok = 0;
        }
        else {
            $data_nascita_errata = NULL;
            $user_data_nascita_ok = 1;
        }
    }
    else {
        $data_nascita = NULL;
        $data_nascita_errata = NULL;
        $user_data_nascita_ok = 1;
    }
    $user_data_nascita2 = $data_nascita;
}

function socialnet() {
    global $lang, $facebook_errato, $facebook_ok, $twitter_errato, $twitter_ok, $instagram_errato, $instagram_ok;
    
    if (trim($_POST['facebook']) != '') {
        $facebook = $_POST['facebook'];
        
        if (!preg_match('/^([.a-zA-Z0-9]{5,50})$/', $facebook)) {
            $facebook_errato = '<div id="error2">' . $lang['facebook_errato'] . '</div>';
            $facebook_ok = 0;
        }
        else {
            $facebook_errato = NULL;
            $facebook_ok = 1;
        }
    }
    else {
        $facebook = NULL;
        $facebook_errato = NULL;
        $facebook_ok = 1;
    }
    
    if (trim($_POST['twitter']) != '') {
        $twitter = $_POST['twitter'];
        
        if (!preg_match('/^([a-zA-Z0-9_]{1,15})$/', $twitter)) {
            $twitter_errato = '<div id="error2">' . $lang['twitter_errato'] . '</div>';
            $twitter_ok = 0;
        }
        else {
            $twitter_errato = NULL;
            $twitter_ok = 1;
        }
    }
    else {
        $twitter = NULL;
        $twitter_errato = NULL;
        $twitter_ok = 1;
    }
    
    if (trim($_POST['instagram']) != '') {
        $instagram = $_POST['instagram'];
        
        if (!preg_match('/^([a-zA-Z0-9_.]{1,30})$/', $instagram)) {
            $instagram_errato = '<div id="error2">' . $lang['instagram_errato'] . '</div>';
            $instagram_ok = 0;
        }
        else {
            $instagram_errato = NULL;
            $instagram_ok = 1;
        }
    }
    else {
        $instagram = NULL;
        $instagram_errato = NULL;
        $instagram_ok = 1;
    }

}

function altri_campi() {
	global $lang, $user_attivo_val, $rb_mostra_link, $rb_permessi_utente, $q_riga, $sito, $im_num, $occupazione, $citta, $hobby, $facebook, $twitter, $instagram, $user_socialnet_ok, $socialnet_errato, $user_autorizza_news_val;

    //controllo checkbox user attivo/disattivo
    
    if (isset($_POST['user_attivo']) && $q_riga['attivo'] == 1) {
        $user_attivo_val = 0;
    }
    elseif (isset($_POST['user_attivo']) && $q_riga['attivo'] == 0) {
        $user_attivo_val = 1;
    }
    else {
        $user_attivo_val = $q_riga['attivo'];
    }

    //controllo sito
    
    if (isset($_POST['sito']) && $_POST['sito'] != $q_riga['sito']) {
        $sito = trim($_POST['sito']);
    }
    else {
        $sito = $q_riga['sito'];
    }

    //controllo numero im
    
    if (isset($_POST['im_num']) && $_POST['im_num'] != $q_riga['im_num']) {
        $im_num = trim($_POST['im_num']);
    }
    else {
        $im_num = $_POST['im_num'];
    }

    //controllo account social network
    
    if (isset($_POST['facebook']) && $_POST['facebook'] != $q_riga['facebook']) {
        $facebook = trim($_POST['facebook']);
    }
    else {
        $facebook = $q_riga['facebook'];
    }
    
    if (isset($_POST['twitter']) && $_POST['twitter'] != $q_riga['twitter']) {
        $twitter = trim($_POST['twitter']);
    }
    else {
        $twitter = $q_riga['twitter'];
    }
    
    if (isset($_POST['instagram']) && $_POST['instagram'] != $q_riga['instagram']) {
        $instagram = trim($_POST['instagram']);
    }
    else {
        $instagram = $q_riga['instagram'];
    }

    //controllo occupazione
    
    if (isset($_POST['occupazione']) && $_POST['occupazione'] != $q_riga['occupazione']) {
        $occupazione = trim($_POST['occupazione']);
    }
    else {
        $occupazione = $q_riga['occupazione'];
    }

    //controllo città
    
    if (isset($_POST['citta']) && $_POST['citta'] != $q_riga['citta']) {
        $citta = trim($_POST['citta']);
    }
    else {
        $citta = $q_riga['citta'];
    }

    //controllo hobby
    
    if (isset($_POST['hobby']) && $_POST['hobby'] != $q_riga['hobby']) {
        $hobby = trim($_POST['hobby']);
    }
    else {
        $hobby = $q_riga['hobby'];
    }

    //controllo mostra_link
    $rb_mostra_link = (isset($_POST['rb'])) ? $_POST['rb'] : $q_riga['mostra_link'];

    //controllo permessi utente
    $rb_permessi_utente = (isset($_POST['rbp'])) ? $_POST['rbp'] : $q_riga['permessi'];

    //controllo autorizzazione news
    
    if (isset($_POST['user_autorizza_news'])) {
        $user_autorizza_news_val = 1;
    }
    else {
        $user_autorizza_news_val = 0;
    }
}

function post_im() {

    //controllo im
    global $im_selected1, $im_selected2, $im_selected3, $im_selected4, $im_selected5, $im_selected6, $im_selected7, $im_selected8;
    
    switch ($_POST['im']) {
        case 'scegli':
            $im_selected1 = 'selected="selected"';
            $im_selected2 = NULL;
            $im_selected3 = NULL;
            $im_selected5 = NULL;
            $im_selected6 = NULL;
            $im_selected7 = NULL;
        break;
        case 'whatsapp':
            $im_selected1 = NULL;
            $im_selected2 = 'selected="selected"';
            $im_selected3 = NULL;
            $im_selected5 = NULL;
            $im_selected6 = NULL;
            $im_selected7 = NULL;
        break;
        case 'icq':
            $im_selected1 = NULL;
            $im_selected2 = NULL;
            $im_selected3 = 'selected="selected"';
            $im_selected5 = NULL;
            $im_selected6 = NULL;
            $im_selected7 = NULL;          
        break;
        case 'skype':
            $im_selected1 = NULL;
            $im_selected2 = NULL;
            $im_selected3 = NULL;
            $im_selected5 = 'selected="selected"';
            $im_selected6 = NULL;
            $im_selected7 = NULL;           
        break;
		case 'telegram':
            $im_selected1 = NULL;
            $im_selected2 = NULL;
            $im_selected3 = NULL;
            $im_selected5 = NULL;
            $im_selected6 = 'selected="selected"';
            $im_selected7 = NULL;           
        break;
		case 'signal':
            $im_selected1 = NULL;
            $im_selected2 = NULL;
            $im_selected3 = NULL;
            $im_selected5 = NULL;
            $im_selected6 = NULL;
            $im_selected7 = 'selected="selected"';         
        break;
    }
}

function upload() {
    global $lang, $upload_msg, $file_sul_server, $upload_path, $file_dir, $tab_config, $news_dir, $db;

    $sql_val_img = mysqli_query($db, "SELECT max_file_size FROM `$tab_config`");
    $val_img = mysqli_fetch_array($sql_val_img);
    
    if (!isset($_FILES['userfile'])) {
        $_FILES['userfile']['tmp_name'] = NULL;
    }
    $check_chmod = substr(sprintf('%o', @fileperms('../' . $file_dir)) , -3);
    $chmod_validi = array(
        777
    );
    
    if (!in_array($check_chmod, $chmod_validi)) {
        $upload_msg = NULL;
    }
    else {

        //se non è stato caricato nessun file non scrivo alcun messaggio
        
        if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            $upload_msg = NULL;
        }
        else {

            //se il file caricato ha il Mime Type differente da quelli validi mostro il messaggio di errore e cancello il file temporaneo
            $estensioni_permesse = array(
				"image/bmp",
                "image/gif",
                "image/pjpeg",
                "image/jpeg",
                "image/png",
                "image/vnd.adobe.photoshop",
                "application/octet-stream",
                "application/xml",
                "application/vnd.google-earth.kml+xml",
                "application/vnd.google-earth.kmz",
				"application/download",
				"application/gzip",
                "application/zip",
                "application/x-zip-compressed",
                "application/x-rar",
                "application/x-rar-compressed",
                "application/x-bittorrent",
                "application/pdf",
                "application/vnd.oasis.opendocument.text",
                "application/vnd.oasis.opendocument.spreadsheet",
                "application/msword",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                "application/vnd.ms-excel",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "text/xml",
                "audio/mp3",
                "audio/x-m4a",
                "video/mp4",
                "audio/mpeg",
				"application/vnd.android.package-archive"
            );
            
            if (!in_array($_FILES['userfile']['type'], $estensioni_permesse)) {
                unlink($_FILES['userfile']['tmp_name']);
                $upload_msg = ' - <div id="error2">' . $lang['wrong_file'] . '</div>';
            }
            else {

                //se il file supera la dimensione massima mostro il messaggio di errore
                
                if ($_FILES['userfile']['size'] > $val_img['max_file_size']) {
                    unlink($_FILES['userfile']['tmp_name']);
                    $upload_msg = ' - <div id="error2">' . $lang['big_file'] . '</div>';
                }
                else {

                    //rimuovo caratteri pericolosi dal nome del file
                    $replace_chars = array(
                        '"' => '',
                        '\'' => '',
                        '%' => '',
                        ' ' => '+',
                        '&' => 'e',
                        ';' => '',
                        '\\' => '',
                        '?' => '',
                        '<' => '',
                        '>' => '',
                        '(' => '-',
                        ')' => '-',
                        '[' => '-',
                        ']' => '-'
                    );
                    $_FILES['userfile']['name'] = strtolower(strtr(basename($_FILES['userfile']['name']) , $replace_chars));
                    $user_dir = '../' . $file_dir . "/" . $_SESSION['user_id'];
                    
                    //se il file inviato esiste lo rinomino anteponendo la data (ggmmaa) e un numero random per evitare di sovrascrivere quello esistente
                    if (file_exists($user_dir . '/' . $_FILES['userfile']['name'])) {
						
						$random = mt_rand(111, 999);
						$data_file = date("dmy");
						$file_sul_server = $data_file . "_" . $random . "_" . $_FILES['userfile']['name'];
						
					} else {
						$file_sul_server = $_FILES['userfile']['name'];
					}

                    //se non c'è la directory dell'utente che fa l'upload la creo con il file indice al suo interno
                    
                    if (!is_dir('../' . $file_dir . '/' . $_SESSION['user_id'])) {
                        @mkdir($user_dir, 0777);
                        $file_index = @fopen("$user_dir/index.html", "w");
                        @fclose($file_index);

                        //se il file è valido lo copio nella directory di destinazione e rimuovo il file temporaneo
                        copy($_FILES['userfile']['tmp_name'], "$user_dir/" . $file_sul_server);
                        unlink($_FILES['userfile']['tmp_name']);
                    }
                    else {

                        //se il file è valido lo copio nella directory di destinazione e rimuovo il file temporaneo
                        copy($_FILES['userfile']['tmp_name'], "$user_dir/" . $file_sul_server);
                        unlink($_FILES['userfile']['tmp_name']);
                    }

                    //se il file non è una immagine mostro solo il tag BBcode altrimenti anche il link diretto
                    $uploadnew = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $upload_path . '/' . $_SESSION['user_id'] . '/' . $file_sul_server;
                    
					if ($_FILES['userfile']['type'] == 'image/bmp' || $_FILES['userfile']['type'] == 'application/zip' || $_FILES['userfile']['type'] == "application/x-zip-compressed" || $_FILES['userfile']['type'] == 'application/x-rar-compressed' || $_FILES['userfile']['type'] == 'application/x-rar' || $_FILES['userfile']['type'] == 'application/pdf' || $_FILES['userfile']['type'] == 'application/vnd.oasis.opendocument.text' || $_FILES['userfile']['type'] == 'application/vnd.oasis.opendocument.spreadsheet' || $_FILES['userfile']['type'] == 'application/msword' || $_FILES['userfile']['type'] == 'application/vnd.ms-excel' || $_FILES['userfile']['type'] == 'application/octet-stream' || $_FILES['userfile']['type'] == 'application/xml' || $_FILES['userfile']['type'] == 'application/vnd.google-earth.kml+xml' || $_FILES['userfile']['type'] == 'application/vnd.google-earth.kmz' || $_FILES['userfile']['type'] == 'application/download' || $_FILES['userfile']['type'] == 'application/gzip' || $_FILES['userfile']['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $_FILES['userfile']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $_FILES['userfile']['type'] == 'text/xml' || $_FILES['userfile']['type'] == 'application/vnd.android.package-archive'|| $_FILES['userfile']['type'] == "image/vnd.adobe.photoshop" || $_FILES['userfile']['type'] == 'application/x-bittorrent' || $_FILES['userfile']['type'] == 'audio/mp3' || $_FILES['userfile']['type'] == 'audio/x-m4a' || $_FILES['userfile']['type'] == 'video/mp4' || $_FILES['userfile']['type'] == 'audio/mpeg') {
                        $size_file = round(filesize($user_dir . '/' . $file_sul_server) / 1024, 1) > 1024 ? round(filesize($user_dir . '/' . $file_sul_server) / 1024 / 1024, 1) . ' MiB' : round(filesize($user_dir . '/' . $file_sul_server) / 1024, 1) . ' KiB';
                        $upload_msg = ' - ' . $lang['upload_ok'] . ' <a href="javascript:;" onclick="addText(\' [url=' . $uploadnew . ']' . $_FILES['userfile']['name'] . ' (' . $size_file . ')[/url] \'); return(false);" class="piccolo">' . $lang['insert_file'] . '</a>';
                    }
                    else {

                        //creo la thumbnail dell'immagine solo se è più larga del corpo news
                        list($width, $height, $type, $attr) = getimagesize($uploadnew);
                        
                        if ( ($width >= 605 || $height >= 500 ) && extension_loaded('gd')) {
                            $ext_file = pathinfo($uploadnew);
                            $ext_file['extension'];
                            $sr_length = ($ext_file['extension'] == 'jpeg') ? -5 : -4;
                            $tn = substr_replace($file_dir . '/' . $_SESSION['user_id'] . '/' . $file_sul_server, '_th.' . $ext_file['extension'], $sr_length);
                            $thumb = imagecreatetruecolor(100, 80);
                            
                            if ($_FILES['userfile']['type'] == 'image/pjpeg' || $_FILES['userfile']['type'] == 'image/jpeg') {
                                $source = imagecreatefromjpeg($uploadnew);
                                imagecopyresized($thumb, $source, 0, 0, 0, 0, 100, 80, $width, $height);
                                imagejpeg($thumb, '../' . $tn, 85);
                            }
                            elseif ($_FILES['userfile']['type'] == 'image/png') {
                                $source = imagecreatefrompng($uploadnew);
                                imagecopyresized($thumb, $source, 0, 0, 0, 0, 100, 80, $width, $height);
                                imagepng($thumb, '../' . $tn, 4);
                            }
                            elseif ($_FILES['userfile']['type'] == 'image/gif') {
                                $source = imagecreatefromgif($uploadnew);
                                imagecopyresized($thumb, $source, 0, 0, 0, 0, 100, 80, $width, $height);
                                imagegif($thumb, '../' . $tn);
                            }
                            $uploadnew_thumb = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $news_dir . '/' . $tn;
                            $upload_msg = ' - ' . $lang['upload_ok'] . ' <a href="javascript:;" onclick="addText(\'[url=' . $uploadnew . '][img]' . $uploadnew_thumb . '[/img][/url]\'); return(false);" class="piccolo">' . $lang['insert_file'] . '</a>';
                        }
                        else {
                            $upload_msg = ' - ' . $lang['upload_ok'] . ' <a href="javascript:;" onclick="addText(\'[img]' . $uploadnew . '[/img]\'); return(false);" class="piccolo">' . $lang['insert_file'] . '</a>';
                        }
                    }
                }
            }
        }
    }
}

function immagine_apertura() {
    global $immagine;
    
    if (isset($_POST['immagine']) && $_POST['immagine'] != 'http://' && $_POST['immagine'] != '') {
        $immagine = htmlspecialchars(trim($_POST['immagine']) , ENT_QUOTES, "ISO-8859-1");
    }
    else {
        $immagine = '';
    }
}

function sostituzione() {
    global $lang, $testo, $img_path, $nosmile;

    //sostituisco i BBcode con i tags HTML e con gli smilies
    $testo = nl2br(htmlspecialchars($_POST['testo'], ENT_QUOTES, "ISO-8859-1"));
    
    if (isset($_POST['nosmile'])) {
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
        $nosmile == 1;
        $testo = strtr($testo, $replace);
    }
    else {
        $nosmile == 0;
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

    //cerco eventuali BBcode...
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
        '<div style="background-color:#FFFFFF; margin:0 auto; width:90%;" class="text2"><b>' . $lang['citazione'] . '</b></div><div style="background-color:#F9F9F9; margin:0 auto; width:90%; height: auto; border: 1px solid #DEE3E7; padding: 3px;" class="text2">\\2</div>',
        '<div style="background-color:#FFFFFF; margin:0 auto; width:100%;" class="text2"><b>' . $lang['codice'] . '</b></div><div style="background-color:#F9F9F9; width: 560px; height: auto; padding:3px; line-height: 7px; border: 1px solid #E1E1E1; white-space: nowrap; overflow: auto;" class="text"><pre>\\2</pre></div>',
        '<object width="320" height="265"><param name="movie" value="http://www.youtube.com/v/\\1&hl=it&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/\\1&hl=it&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="320" height="265"></embed></object>',
        '<iframe src="\\4\\6" width="400" height="300" frameborder="0" style="border:0"></iframe>',
        '<img src="' . $img_path . '/icq.png" alt="" title="ICQ" /> \\1',
        '<img src="' . $img_path . '/skype.png" alt="" title="Skype" /> <a href="skype:\\1?chat" title="Skype">\\1</a>',
        '<img src="' . $img_path . '/whatsapp.png" alt="" title="Whatsapp" /> \\1',
		'<img src="' . $img_path . '/telegram.png" alt="" title="Telegram" /> <a href="https://telegram.me/\\1" target="_blank" title="Telegram">\\1</a>',
		'<img src="' . $img_path . '/signal.png" alt="" title="Signal" /> \\1'
    );
    $testo = preg_replace($testo_cerca, $testo_sostituisci, $testo);
}

function permessi() {
    global $lang, $q_riga_perm, $file_dir, $upload_msg, $letture, $tab_config, $db;

    //dimensione massima (in Bytes) per singolo file caricato: 51200 = 50KiB (Valori Binari)
	$sql_size = mysqli_query($db, "SELECT max_file_size FROM `$tab_config`");
    $row_size = mysqli_fetch_assoc($sql_size);    
    $maxfilesize = $row_size['max_file_size'];
    
    $check_chmod = substr(sprintf('%o', fileperms('../' . $file_dir)) , -3);
    $chmod_validi = array(
        777
    );
    
    if (in_array($check_chmod, $chmod_validi)) {
        $upload_ok = $lang['chmod'] . ' &quot;' . $file_dir . '&quot;' . ': ' . substr(sprintf('%o', fileperms('../' . $file_dir)) , -3) . $upload_msg;
    }
    else {
        $upload_ok = '<span style="color: rgb(255, 0, 0);"> ' . $lang['permessi_non_validi'] . ' &quot;' . $file_dir . '&quot;</span>';
    }

    //conto quanti file ha inserito l'utente loggato
    $files = 0;
    
    if ($aprodir = @opendir('../' . $file_dir . '/' . intval($_SESSION['user_id']))) {
        
        while (false !== ($ifile = readdir($aprodir))) {
            
            if ($ifile != '.' && $ifile != '..' && $ifile != 'index.html') {
                ++$files;
            }
        }
        closedir($aprodir);
    }
    $files = ($files == 0) ? 0 : $files;
    $link_files = ($files > 0) ? ' <a href="javascript:;" onclick="window.open(\'files.php?modo=news&amp;user_id=' . intval($_SESSION['user_id']) . '\', \'\', \'width=650, height=450, resizable=1, scrollbars=1, location=1, status=1\');" title="[Popup]"><b>' . $lang['file'] . '</b></a> - ' : NULL;
    
    if ($q_riga_perm['permessi'] == "upload") {
        $p_permessi = '<tr><td bgcolor="#DEE3E7" align="center" class="text"><b>' . $lang['file_upload'] . '</b> <span class="text2">' . $lang['uploadtext'] . $maxfilesize / 1024 . ' KiB</span></td>';
        $p_permessi.= '<td bgcolor="#EEEEEE" align="left" class="text2"><input type="file" name="userfile" size="20" /> ' . $link_files . '';
        $p_permessi.= $upload_ok . '</td></tr>';
    }
    elseif ($q_riga_perm['permessi'] == "letture") {
        $p_permessi = '<tr><td bgcolor="#DEE3E7" align="center" class="text"><b>' . $lang['letture'] . '</b></td>';
        $p_permessi.= '<td bgcolor="#EEEEEE" align="left" class="text2"><input type="text" name="letture" size="2" value="' . $letture . '" maxlength="8" onkeypress="return onlynumbers(event,\'0123456789\')" onfocus="this.select()" /> ' . $lang['numletture'] . ' - ' . $link_files . '</td></tr>';
    }
    elseif ($q_riga_perm['permessi'] == "tutto") {
        $p_permessi = '<tr><td bgcolor="#DEE3E7" align="center" class="text"><b>' . $lang['file_upload'] . '</b> <span class="text2">' . $lang['uploadtext'] . $maxfilesize / 1024 . ' KiB</span></td>';
        $p_permessi.= '<td bgcolor="#EEEEEE" align="left" class="text2"><input type="file" name="userfile" size="20" />' . $link_files . '';
        $p_permessi.= $upload_ok . '</td></tr>';
        $p_permessi.= '<tr><td bgcolor="#DEE3E7" align="center" class="text"><b>' . $lang['letture'] . '</b></td>';
        $p_permessi.= '<td bgcolor="#EEEEEE" align="left" class="text2"><input type="text" name="letture" size="6" value="' . $letture . '" maxlength="8" onkeypress="return onlynumbers(event,\'0123456789\')" onfocus="this.select()" /> ' . $lang['numletture'] . '</td></tr>';
    }
    elseif ($q_riga_perm['permessi'] == "nessuno" && $files > 0) {
        $p_permessi = '<tr><td bgcolor="#DEE3E7" align="center" class="text"><b>' . $lang['file_upload'] . '</b></td><td bgcolor="#EEEEEE" align="left" class="text2">' . $link_files . '</td></tr>';
    } 
    else {
        $p_permessi = NULL;
    }          

    return $p_permessi;
}

function operazioni_utente() {
    global $lang, $q_user_id_del, $tab_utenti, $tab_news, $img_path, $q_user_id, $db;

    //posso cancellare e disattivare solo se sono admin
    
    if ($_SESSION['livello_id'] == 1 && isset($_GET['user_id']) && $_GET['user_id'] != $_SESSION['user_id'] && preg_match('/^[0-9]{1,5}$/', $_GET['user_id'])) {

        //cancellazione utente
        
        if (isset($_POST['btndel'])) {
            $q_user_id_del = $_GET['user_id'];

            //posso cancellare solo l'utente che non ha inserito news
            $sql_num_totale = mysqli_query($db, "SELECT COUNT(id) AS NumTotale FROM `$tab_news` WHERE user_id=$q_user_id_del");
			$num_totale_riga = mysqli_fetch_array($sql_num_totale);

            if ($num_totale_riga['NumTotale'] == 0) {
                
                if (mysqli_query($db, "DELETE FROM `$tab_utenti` WHERE user_id=$q_user_id_del AND livello_id>1 LIMIT 1")) {
                    $delete_msg = '<div id="success">' . $lang['utente_cancellato'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                    header("Refresh: 2; url=utenti.php");
                }
                else {
                    $delete_msg = '<div align="center"><div id="error">' . $lang['canc_user_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
                }
                
                return $delete_msg;
            }
            else {
                
                return $delete_msg;
            }
        }

        //attivazione utente
        elseif (isset($_POST['user_attivo'])) {
            $q_user_id_del = $_GET['user_id'];
            $redirect = (isset($_GET['user_id'])) ? "profilo_admin.php?user_id=$q_user_id_del" : "profilo_admin.php";
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET attivo=1 WHERE user_id=$q_user_id_del AND attivo=0 LIMIT 1")) {
                $attivato_msg = '<div align="center"><span class="text"><b>' . $lang['utente_attivato'] . '</b></span> <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header("Refresh: 2; url=" . $redirect);
            }
            else {
                $attivato_msg = '<div id="error">' . $lang['attiva_user_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
            
            return $attivato_msg;
        }

        //disattivazione utente
        elseif (isset($_POST['user_disattivo'])) {
            $q_user_id_del = $_GET['user_id'];
            $redirect = (isset($_GET['user_id'])) ? "profilo_admin.php?user_id=$q_user_id_del" : "profilo_admin.php";
            
            if (mysqli_query($db, "UPDATE `$tab_utenti` SET attivo=0, cookie=0, token=NULL, new_pwd=NULL, key_pwd=NULL, mostra_link='nome' WHERE user_id=$q_user_id_del AND attivo=1 AND livello_id>1 LIMIT 1")) {
                $disattivato_msg = '<div align="center"><span class="text"><b>' . $lang['utente_disattivato'] . '</b></span> <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
                header("Refresh: 2; url=" . $redirect);                
            }
            else {
                $disattivato_msg = '<div id="error">' . $lang['disatta_user_error'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
            }
            
            return $disattivato_msg;
        }
    }
}

//paginazione

function page_bar($indirizzo, $pagina_attuale, $numero_pagine, $rec_page) {
    global $lang;
    $paginazione = NULL;
    
    if ($pagina_attuale > 1) {
        $paginazione.= '<a href="' . $indirizzo . '&amp;start=' . (($pagina_attuale - 2) * $rec_page) . '" class="pager">&#171; ' . $lang['indietro'] . '</a>&nbsp;&nbsp;';
    }
    
    if ($pagina_attuale > 5 && $numero_pagine > 6) {
        $pagina_inizio = $pagina_attuale - 2;
    }
    else {
        $pagina_inizio = 1;
    }
    
    if ($pagina_attuale < ($numero_pagine - 3)) {
        $pagina_fine = ($numero_pagine > 6 ? max(($pagina_attuale + 2) , 6) : $numero_pagine);
    }
    else {
        $pagina_fine = $numero_pagine;
    }
    
    if ($pagina_inizio > 1) {
        $paginazione.= '<a href="' . $indirizzo . '&amp;start=0" class="pager">1</a>&nbsp;... ';
    }
    
    for ($pagina = $pagina_inizio;$pagina <= $pagina_fine;++$pagina) {
        
        if ($pagina == $pagina_attuale) {
            $paginazione.= '<span class="pagertext">[<b>' . $pagina . '</b>]</span> ';
        }
        else {
            $paginazione.= '<a href="' . $indirizzo . '&amp;start=' . (($pagina - 1) * $rec_page) . '" class="pager">' . $pagina . '</a>&nbsp;';
        }
    }
    
    if (($numero_pagine - $pagina_fine) > 0) {
        $paginazione.= '... <a href="' . $indirizzo . '&amp;start=' . (($numero_pagine - 1) * $rec_page) . '" class="pager">' . $numero_pagine . '</a>&nbsp;';
    }
    
    if ($pagina_attuale < $numero_pagine) {
        $paginazione.= '&nbsp;<a href="' . $indirizzo . '&amp;start=' . (($pagina_attuale) * $rec_page) . '" class="pager">' . $lang['avanti'] . ' &#187;</a>';
    }
    
    return ($paginazione);
}

//cancella i file di upload dell'utente

function full_rmdir($dirname) {
    global $lang, $del_ok;
    
    if ($dirHandle = @opendir($dirname)) {
        $old_cwd = getcwd();
        chdir($dirname);
        
        while ($file = readdir($dirHandle)) {
            
            if ($file == '.' || $file == '..') continue;
            
            if (is_dir($file)) {
                
                if (!full_rmdir($file)) {
                    
                    return FALSE;
                }
            }
            else {

                //se non riesco a cancellare il file (es. permessi negati)
                
                if (!unlink($file)) {
                    
                    return FALSE;
                }
            }
        }
        closedir($dirHandle);
        chdir($old_cwd);
        
        if (!rmdir($dirname)) {
            
            return FALSE;
        }

        //ho cancellato la directory
        $del_ok = '<div id="success">' . $lang['canc_dir_files_ok'] . '</div><br />';
        
        return TRUE;
    }
    else {

        //non riesco ad accedere alla directory: non esiste o è già stata cancellata
        
        return FALSE;
    }
}

//genero nuova password

function NewPassword() {
    $caratteri = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_+/*=";
    srand((double)microtime() * 1000000);
    $elaborazione = '';
    
    for ($contatore = 0; $contatore < 8; ++$contatore) {
        $numeroCasuale = mt_rand(0, strlen($caratteri) - 1);
        $carattere = substr($caratteri, $numeroCasuale, 1);
        $elaborazione = $elaborazione . $carattere;
    }
    
    return $elaborazione;
}

//captcha alfanumerico

function captcha() {
    global $lang;
    $caratteri = "abcdefhijkmnpqrstuvwxyzACDEFGHKLMNPQRTUVWXYZ";
    $char1 = $caratteri[mt_rand(0, 43)];
    $char2 = $caratteri[mt_rand(0, 43)];
    $char3 = $caratteri[mt_rand(0, 43)];
    $numb1 = mt_rand(1, 9);
    $numb2 = mt_rand(1, 9);
    $numb3 = mt_rand(1, 9);
	
    $sololettere = $char1 . $char2 . $char3;
    $sommanumeri = $numb1 + $numb2 + $numb3;
    $solonumeri = $numb1 . $numb2 . $numb3;
    $primoultimonr = $numb1 . $numb3;
    $primaultimalt = $char1 . $char3;
	$primaltultimonr = $char1 . $numb3;
    $tutto = $char1 . $numb1 . $char2 . $numb2 . $char3 . $numb3;
    $secondonrterzalt = $numb1 . $char2;
    
    $frasi = array(
        1 => $sololettere,
        2 => $sommanumeri,
        3 => $solonumeri,
        4 => $primoultimonr,
        5 => $primaultimalt,
		6 => $primaltultimonr,
        7 => $tutto,
        8 => $secondonrterzalt
    );
    
    $rand = mt_rand(1, 8);
    
    $_SESSION['antispam'] = $frasi[$rand];
    
    $str1 = $char1 . ' ' . $numb1 . ' ' . $char2 . ' ' . $numb2 . ' ' . $char3 . ' ' . $numb3;
    $str2 = $numb1 . ' ' . $char1 . ' ' . $numb2 . ' ' . $char2 . ' ' . $numb3 . ' ' . $char3;
    $str3 = $numb1 . ' ' . $char1 . ' ' . $numb2 . ' ' . $char2 . ' ' . $numb3 . ' ' . $char3;
    $str4 = $numb1 . ' ' . $char1 . ' ' . $char2 . ' ' . $numb2 . ' ' . $char3 . ' ' . $numb3;
    $str5 = $char1 . ' ' . $numb1 . ' ' . $char2 . ' ' . $numb2 . ' ' . $char3 . ' ' . $numb3;    
	$str6 = $char1 . ' ' . $numb1 . ' ' . $char2 . ' ' . $numb2 . ' ' . $char3 . ' ' . $numb3;   
    $str7 = $char1 . ' ' . $numb1 . ' ' . $char2 . ' ' . $numb2 . ' ' . $char3 . ' ' . $numb3;
	$str8 = $char1 . ' ' . $numb1 . ' ' . $char2 . ' ' . $numb2 . ' ' . $char3 . ' ' . $numb3;    
      
    if ($rand == 1) {
        
        return $lang['antispam_word'] . ' ' . $str1;
    
    } elseif ($rand == 2) {
        
        return $lang['antispam_numb'] . ' ' . $str2;
    
    } elseif ($rand == 3) {
        
        return $lang['antispam_only_numb'] . ' ' . $str3;
    
    } elseif ($rand == 4) {
		
		return $lang['antispam_first_last_nr'] . ' ' . $str4;
	
	} elseif ($rand == 5) {
		
		return $lang['antispam_first_last_lt'] . ' ' . $str5;		

	} elseif ($rand == 6) {
		
		return $lang['antispam_first_lt_last_nr'] . ' ' . $str6;

	} elseif ($rand == 7) {
		
		return $lang['antispam_tutto'] . ' ' . $str7;
	
	} else {
	
		return $lang['antispam_sec_nr_thi_lt'] . ' ' . $str8;
	
	}
}
?>
