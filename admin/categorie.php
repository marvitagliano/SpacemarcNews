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

//calcolo il tempo di generazione della pagina (1a parte)
$mtime1 = explode(" ", microtime());
$starttime = $mtime1[1] + $mtime1[0];

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');

$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);
check_login();

//se NON sono un amministratore e voglio visualizzare questa pagina redirigo al proprio profilo

if ($_SESSION['livello_id'] != 1) {
    header('Location: ' . $dir_admin . '/elenco_news.php');
    exit();
}

check_form();

//dimensione massima (in Bytes) per singolo file caricato: 51200 = 50KiB (Valori Binari)
$sql_size = mysqli_query($db, "SELECT max_file_size FROM `$tab_config`");
$row_size = mysqli_fetch_assoc($sql_size);    
$maxfilesize = $row_size['max_file_size'];
$query_msg = NULL;
$upload_cat_msg = NULL;

//modifico nome categoria

if (isset($_POST['modifica_nome'])) {
    
    foreach ($_POST['modifica_nome'] as $key => $value) {
        
        if (!preg_match('/^([.a-zA-Z0-9- ]{1,30})$/', trim($_POST['nome_categoria'][$key]))) {
            $_POST['nome_categoria'][$key] = "Categoria " . $key;
        }
        mysqli_query($db, "UPDATE `$tab_categorie` SET nome_categoria = '" . trim($_POST['nome_categoria'][$key]) . "' WHERE id_cat=" . $key);
        $query_msg = '<div id="success">' . $lang['modifica_categorie_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
        header("Refresh:2; url=categorie.php");
    }
}

//cancello categoria
elseif (isset($_POST['cancella_categoria'])) {
    
    foreach ($_POST['cancella_categoria'] as $key => $value) {
        mysqli_query($db, "DELETE FROM `$tab_categorie` WHERE id_cat=" . intval($key) . " AND id_cat NOT IN (SELECT id_cat FROM `$tab_news`)");
        
        if (mysqli_affected_rows($db) > 0) {
            $query_msg = '<div id="success">' . $lang['cancella_categoria_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
            header("Refresh:2; url=categorie.php");
        }
        else {
            $query_msg = '<div id="error">' . $lang['cancella_categoria_errore'] . '</div><br />';
        }
    }
}

//cancello news sotto la categoria
elseif (isset($_POST['cancella_news'])) {
	
	if ( isset($_POST['cbdelnews']) ) { 
    
		foreach ($_POST['cancella_news'] as $key => $value) {
			
			// seleziono i tags associati alla notizie della categoria
			$sql_recount = mysqli_query($db, "SELECT id_tag FROM `$tab_tags` WHERE id_tag IN (SELECT id_tag FROM `$tab_tags_rel` WHERE id_news IN (SELECT id FROM `$tab_news` WHERE id_cat = " . intval($key) . "))");
			$alltags = array();

			while ($row_recount = mysqli_fetch_array($sql_recount)) {
				$alltags[] =  $row_recount['id_tag'];
			}
							
			// rimuovo la relazione tags-news
			mysqli_query($db, "DELETE FROM `$tab_tags_rel` WHERE id_news IN (SELECT id FROM `$tab_news` WHERE id_cat = " . intval($key) . ")");
		
			mysqli_query($db, "DELETE FROM `$tab_news` WHERE id_cat = " . intval($key));
			
			if (mysqli_affected_rows($db) > 0) {

				$query_msg = '<div id="success">' . $lang['cancella_categoria_news_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
				header("Refresh:2; url=categorie.php");
			}
			else {
				$query_msg = '<div id="error">' . $lang['cancella_categoria_news_errore'] . '</div><br />';
			}
		}
		
	} else {
		
		$query_msg = '<div id="error">' . $lang['attenzione_news_cb'] . '</div><br />';
	}
}

//creo nuova categoria
elseif (isset($_POST['crea_categoria'])) {

    if (!preg_match('/^([.a-zA-Z0-9- ]{1,30})$/', trim($_POST['nuova_categoria']))) {
        $query_msg = '<div id="error">' . $lang['nuova_categoria_errore'] . '</div><br />';
    }
    else {
        mysqli_query($db, "INSERT INTO `$tab_categorie` (nome_categoria) VALUES ('" . $_POST['nuova_categoria'] . "')");
        $query_msg = '<div id="success">' . $lang['nuova_categoria_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
        header("Refresh:2; url=categorie.php");
    }
}

//sposto le news da una categoria ad un'altra
elseif (isset($_POST['sposta_news'])) {
    
    if ($_POST['cat_da'] == 'scegli' || $_POST['cat_a'] == 'scegli' || $_POST['cat_da'] == $_POST['cat_a']) {
        $query_msg = '<div id="error">' . $lang['news_nuova_categoria_errore'] . '</div><br />';
    }
    else {
        
        if (mysqli_query($db, "UPDATE `$tab_news` SET id_cat=" . intval($_POST['cat_a']) . " WHERE id_cat=" . intval($_POST['cat_da']))) {
            $query_msg = '<div id="success">' . $lang['news_nuova_categoria_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
            header("Refresh:2; url=categorie.php");
        }
        else {
            $query_msg = '<div id="error">' . $lang['news_nuova_categoria_errore'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
        }
    }
}

//sposto le news di un autore
elseif (isset($_POST['sposta_autore'])) {
    
    if ($_POST['autore'] == 'scegli' || $_POST['cat_a_autore'] == 'scegli') {
        $query_msg = '<div id="error">' . $lang['news_nuova_categoria_errore'] . '</div><br />';
    }
    else {
        
        if (mysqli_query($db, "UPDATE `$tab_news` SET id_cat=" . intval($_POST['cat_a_autore']) . " WHERE user_id=" . intval($_POST['autore']))) {
            $query_msg = '<div id="success">' . $lang['news_nuova_categoria_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
            header("Refresh:2; url=categorie.php");
        }
        else {
            $query_msg = '<div id="error">' . $lang['news_nuova_categoria_errore'] . '</div><br /><span class="text2">' . mysqli_error($db) . '</span><br /><br />';
        }
    }
}

//upload immagine categoria
elseif (isset($_POST['upload_img'])) {
    
    if (!isset($_FILES['userfile'])) {
        $_FILES['userfile']['tmp_name'] = NULL;
    }
    $check_chmod = substr(sprintf('%o', @fileperms('../' . $file_dir)) , -3);
    $chmod_validi = array(
        777
    );
    
    if (!in_array($check_chmod, $chmod_validi)) {
        $upload_cat_msg = '<div id="error">' . $lang['permessi_non_validi'] . ' &quot;' . $file_dir . '&quot;</div><br />';
    }
    else {
        $upload_cat_msg = NULL;

        //se non è stato caricato nessun file non scrivo alcun messaggio
        
        if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            $upload_cat_msg = NULL;
        }
        else {

            //se il file caricato ha il Mime Type differente da quelli validi mostro il messaggio di errore e cancello il file temporaneo
            $estensioni_permesse = array(
                "image/gif",
                "image/pjpeg",
                "image/jpeg",
                "image/png"
            );
            
            if (!in_array($_FILES['userfile']['type'], $estensioni_permesse)) {
                unlink($_FILES['userfile']['tmp_name']);
                $upload_cat_msg = '<div id="error">' . $lang['wrong_file'] . '</div><br />';
            }
            else {

                //se il file supera la dimensione massima mostro il messaggio di errore
                
                if ($_FILES['userfile']['size'] > $maxfilesize) {
                    unlink($_FILES['userfile']['tmp_name']);
                    $upload_cat_msg = '<div id="error">' . $lang['big_file'] . '</div><br />';
                }
                else {
                    $estensione = pathinfo($_FILES['userfile']['name']);
                    $file_rinominato = "cat_" . intval($_POST['cat_img']) . "." . $estensione['extension'];
					$user_dir = '../' . $file_dir . "/" . $_SESSION['user_id'];

                    //se non c'è la directory dell'utente che fa l'upload la creo con il file indice al suo interno
                    
                    if (!is_dir('../' . $file_dir . '/' . $_SESSION['user_id'])) {
                        @mkdir($user_dir, 0777);
                        $file_index = @fopen("$user_dir/index.html", "w");
                        @fclose($file_index);

                        //se il file è valido lo copio nella directory di destinazione e rimuovo il file temporaneo
                        copy($_FILES['userfile']['tmp_name'], "$user_dir/" . $file_rinominato);
                        unlink($_FILES['userfile']['tmp_name']);
                    }
                    else {

                        //se il file è valido lo copio nella directory di destinazione e rimuovo il file temporaneo
                        copy($_FILES['userfile']['tmp_name'], "$user_dir/" . $file_rinominato);
                        unlink($_FILES['userfile']['tmp_name']);
                    }
                    $uploadnew = "http://" . $_SERVER['HTTP_HOST'] . "/" . $upload_path . "/" . $_SESSION['user_id'];

                    //aggiorno il path in tabella
                    mysqli_query($db, "UPDATE `$tab_categorie` SET img_categoria = '" . $uploadnew . '/' . $file_rinominato . "' WHERE id_cat=" . intval($_POST['cat_img']));
                    $upload_cat_msg = '<div id="success">' . $lang['upload_ok'] . ' <img src="' . $img_path . '/attendi.gif" title="" alt="" /></div><br />';
					header("Refresh:2; url=categorie.php");
                }
            }
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">     
  <head>         
    <title><?php echo $lang['categorie']; ?>
    </title>         
    <link rel="stylesheet" href="../style.css" type="text/css" />		 
<script language="JavaScript" src="../javascript.js" type="text/JavaScript"></script>  
  </head>     
  <body>
<?php
require_once ("menu.php");
echo $query_msg;
echo $upload_cat_msg;
?>          
    <form action="categorie.php" method="post" name="admin" enctype='multipart/form-data'>             
      <table width="100%" align="center" style="border: 3px solid #DDDDDD;" cellpadding="4" cellspacing="2" class="text">                     
        <tr>                            
          <td bgcolor="#DEE3E7" align="left" width="18%" valign="top"><b><?php echo $lang['categorie']; ?></b></td>                            
          <td bgcolor="#EEEEEE" align="left" class="text">						
<?php

//stampo le categorie e i totali
$sql_categorie = mysqli_query($db, "SELECT DISTINCT nca.id_cat, nca.nome_categoria, nca.img_categoria, (SELECT COUNT(nt.id_cat) FROM `$tab_news` nt WHERE nt.id_cat=nca.id_cat) AS TotaleNews, (SELECT COUNT(nco.id_comm) FROM `$tab_commenti` nco WHERE nco.id_news IN (SELECT nt.id FROM `$tab_news` nt WHERE nt.id_cat = nca.id_cat)) AS TotaleCommenti FROM `$tab_categorie` nca LEFT JOIN `$tab_news` nt ON nt.id_cat=nca.id_cat ORDER BY nca.nome_categoria ASC");

while ($categorie_val = mysqli_fetch_array($sql_categorie)) {
    $submit_disabled_cat = ($categorie_val['TotaleNews'] > 0 || mysqli_num_rows($sql_categorie) == 1) ? 'disabled="disabled"' : '';
    $submit_disabled_news = ($categorie_val['TotaleNews'] == 0) ? 'disabled="disabled"' : '';
    $TotaleCommenti = ($categorie_val['TotaleCommenti'] == NULL) ? '0' : number_format($categorie_val['TotaleCommenti'], 0, '', '.');
    echo '<img src="' . $categorie_val['img_categoria'] . '" width="30" height="30" alt="" title="' . basename($categorie_val['img_categoria']) . '" /> <input type="text" name="nome_categoria[' . $categorie_val['id_cat'] . ']" value="' . $categorie_val['nome_categoria'] . '" maxlength="30" size="20" title="ID ' . $categorie_val['id_cat'] . '" />
					<input type="submit" name="modifica_nome[' . $categorie_val['id_cat'] . ']" style="font-weight: bold;" value="' . $lang['rinomina'] . '" />
					<input type="submit" name="cancella_categoria[' . $categorie_val['id_cat'] . ']" style="font-weight: bold;" value="' . $lang['delete'] . '" ' . $submit_disabled_cat . ' onclick="return confirmSubmit();" /> 
					<input type="submit" name="cancella_news[' . $categorie_val['id_cat'] . ']" style="font-weight: bold;" value="' . $lang['btn_cancella_news'] . ' &#9888; " ' . $submit_disabled_news . ' onclick="alert(\'' . $lang['attenzione_categorie'] . ' ' . $categorie_val['TotaleNews'] . ' ' . $lang['news'] . '\'); return confirmSubmit()" /> ' . $lang['news'] . ' ' . $categorie_val['TotaleNews'] . ' - ' . $lang['commenti'] . ' ' . $TotaleCommenti . '<br /><br />';
}
?>
            <br />&nbsp; &nbsp; &nbsp; &nbsp;<input type="text" name="nuova_categoria" maxlength="30" size="20" /> 
            <input type="submit" name="crea_categoria" style="font-weight: bold;" value="<?php echo $lang['new_cat']; ?>" /> (.a-zA-Z0-9-&lt;space&gt; 1,30) &nbsp; <span style="color: rgb(255, 0, 0);"><label for="cbdelnews"><?php echo $lang['btn_cancella_news']; ?></label></span> &#129154;<input type="checkbox" name="cbdelnews" id="cbdelnews" title="<?php echo $lang['cancella_news']; ?>" /> <br /><br /></td>
        </tr>
        <tr>
          <td bgcolor="#DEE3E7" align="left" valign="top"><b><?php echo $lang['opzioni']; ?></b></td>
          <td bgcolor="#EEEEEE" align="left" class="text2"><?php echo $lang['sposta_news_in']; ?>
            <select name="cat_da">
              <option value="scegli" selected="selected"><?php echo $lang['scegli']; ?>
              </option>
<?php
$cat_sel = mysqli_query($db, "SELECT id_cat, nome_categoria FROM `$tab_categorie` ORDER BY nome_categoria ASC");
$categorie = array();

while ($row_sel = mysqli_fetch_array($cat_sel)) {
    $categorie[] = '<option value="' . $row_sel['id_cat'] . '">' . $row_sel['nome_categoria'] . '</option>';
    echo "\n";
}

foreach ($categorie as $categoria) {
    echo $categoria;
}
?>           
            </select> <?php echo $lang['a']; ?> 
            <select name="cat_a">
              <option value="scegli" selected="selected"><?php echo $lang['scegli']; ?>
              </option>
<?php

foreach ($categorie as $categoria) {
    echo $categoria;
}
?>           
            </select> 
            <input type="submit" name="sposta_news" style="font-weight: bold;" value="<?php echo $lang['sposta_categoria']; ?>" onclick="return confirmSubmit();" /><br /> <br /><br /><?php echo $lang['sposta_news_di']; ?> 
            <select name="autore">                           
              <option value="scegli" selected="selected"><?php echo $lang['scegli']; ?>
              </option>
<?php
$res_sel = mysqli_query($db, "SELECT nu.user_id, nu.nome_cognome, nu.email, (SELECT COUNT(nt.id) FROM `$tab_news` nt WHERE nt.user_id=nu.user_id) AS TotaleNews FROM `$tab_utenti` nu JOIN `$tab_news` nt ON nt.user_id=nu.user_id GROUP BY nu.user_id HAVING COUNT(nt.user_id)>0 ORDER BY nu.nome_cognome ASC");

if (mysqli_num_rows($res_sel) != 0) {
    
    while ($row_sel = mysqli_fetch_array($res_sel)) {
        $utente = ($row_sel['user_id'] == $_SESSION['user_id']) ? $lang['tu'] : $row_sel['TotaleNews'];
        echo '<option value="' . $row_sel['user_id'] . '" title="' . $row_sel['email'] . '">' . $row_sel['nome_cognome'] . ' (' . $utente . ')</option>';
        echo "\n";
    }
}
?>
            </select> <?php echo $lang['in_categoria']; ?> 
            <select name="cat_a_autore">
              <option value="scegli" selected="selected"><?php echo $lang['scegli']; ?>
              </option>
<?php

foreach ($categorie as $categoria) {
    echo $categoria;
}
?>
            </select> 
            <input type="submit" name="sposta_autore" style="font-weight: bold;" value="<?php echo $lang['sposta_news']; ?>" onclick="return confirmSubmit();" /><br /><br /></td>
        </tr>  
        <tr>
          <td bgcolor="#DEE3E7" align="left" class="text"><b><?php echo $lang['img_cat']; ?></b><br /><span class="text2">gif, jpg, png<br />Max <?php echo $maxfilesize / 1024; ?> KiB - 30 x 30</span></td>
          <td bgcolor="#EEEEEE" align="left" class="text2"><input type="file" name="userfile" size="20" />
          <?php echo $lang['associa_cat']; ?> <select name="cat_img">
<?php

foreach ($categorie as $categoria) {
    echo $categoria;
}
?>
            </select> <input type="hidden" name="post_token" value="<?php echo sha1(session_id()); ?>" /> <input type="submit" name="upload_img" style="font-weight: bold;" value="Upload" />
		</td>
        </tr>        			
      </table>		
    </form><br /><br />    
    <?php require_once ("footer.php"); ?>     
  </body>
</html>
