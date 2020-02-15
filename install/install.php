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
 
header('Content-type: text/html; charset=ISO-8859-1');

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/' . $language . '_install.php');

//connessione a mysql
$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);

//aumento il tempo di timeout dello script a 180 secondi
set_time_limit(180);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">        
  <head>    
    <title>Spacemarc News <?php echo $version . ' - ' . $lang['pagina']; ?>
    </title>              
    <link rel="stylesheet" href="../style.css" type="text/css" />         
  </head>        
  <body>              
    <div align="center" class="text">                       
      <form name="install" action="install.php#installazione" method="post">                      
        <table width="900" border="0" cellpadding="2" cellspacing="2">                                     
          <tr>                              
            <td class="text" align="center" valign="top">              
              <img src="../images/logonews.gif" alt="logo" /><br /><br /><img src="../docs/<?php echo $language; ?>.png" alt="Lang" /> <b>Spacemarc News</b> <b><?php echo $version; ?></b> (<a href="../docs/index_<?php echo $language; ?>.html"><?php echo $lang['guida']; ?></a> - <a href="../docs/changelog.html">changelog</a>)<br /><br />
              <br />
              </td>                       
          </tr>                           
          <tr>                                
            <td class="text2" align="center" bgcolor="#EEEEEE"><b><?php echo $lang['licenza']; ?></b><br /></td>                          
          </tr>                                         
          <tr>                                  
            <td class="text2" align="center">      
<?php
$file = '../docs/gpl-3.0.txt';
$op = fopen($file, "r") or die('Impossibile aprire il file ../docs/gpl-3.0.txt');
$rd = fread($op, filesize($file));
fclose($op);
echo '<textarea name="license" rows="10" cols="100" readonly="readonly">' . $rd . '</textarea>';
?><br />                                      
              <input type="checkbox" name="lic_OK" id="lic_OK" />                                 
              <label for="lic_OK"><?php echo $lang['licenza2']; ?>                          
              </label><br /><br /><br /></td>                              
          </tr>                                             
          <tr>                                  
            <td class="text2" align="center" bgcolor="#EEEEEE"><b><?php echo $lang['impostazioni']; ?> </b><br /></td>                              
          </tr>                                             
          <tr>                                  
            <td align="center">     
            <br />                          
              <label for="nome_cognome" style="font-size: 11px;	cursor: help; border-bottom: 1px dotted #000;" title="<?php echo $lang['nome_cognome2']; ?>"><?php echo $lang['nome_cognome']; ?></label>&nbsp;              
              <input type="text" name="nome_cognome" id="nome_cognome" size="20" maxlength="40" />               
              <label for="email" style="font-size: 11px; cursor: help; border-bottom: 1px dotted #000;" title="<?php echo $lang['email2']; ?>"><?php echo $lang['email']; ?></label>&nbsp; 
              <input type="text" name="email" id="email" size="20" maxlength="50" /><br /><br />                               
              <label for="password" style="font-size: 11px;	cursor: help; border-bottom: 1px dotted #000;" title="<?php echo $lang['password2']; ?>"><?php echo $lang['password']; ?></label>&nbsp; 
              <input type="text" name="user_password" id="password" size="20" />                               
              <label for="nome_sito" style="font-size: 11px; cursor: help; border-bottom: 1px dotted #000;" title="<?php echo $lang['sito2']; ?>"><?php echo $lang['sito']; ?></label>&nbsp;                 
              <input type="text" name="nome_sito" id="nome_sito" size="20" maxlength="40" />
              <label for="url_sito" style="font-size: 11px;	cursor: help; border-bottom: 1px dotted #000;" title="<?php echo $lang['url2']; ?>"><?php echo $lang['url']; ?></label>&nbsp;  
              <input type="text" name="url_sito" id="url_sito" size="20" value="http://" maxlength="50" /><br /><br /></td>                              
          </tr>                                                 
          <tr>                                  
            <td align="center"><br />                                 
              <input type="submit" name="installazione" value="<?php echo $lang['installa']; ?>" style="font-weight: bold;" /><br /><br /></td>                              
          </tr>                                        
          <tr>                           
            <td align="center" class="text2" bgcolor="#C2D8FE">Spacemarc News 
              <?php echo $version; ?> &copy;                
              <a href="http://www.spacemarc.it" target="_blank" class="piccolo">Spacemarc.it</a></td>                         
          </tr>                           
        </table>                  
      </form>  <br /> 
		<a name="installazione"></a>   
      <table width="900" cellpadding="2" cellspacing="2" border="0">        
        <tr><td>
<?php

if (isset($_POST['installazione'])) {
    
    if (!isset($_POST['lic_OK']) || trim($_POST['nome_cognome']) == '' || trim($_POST['email']) == '' || trim($_POST['user_password']) == '' || trim($_POST['nome_sito']) == '' || trim($_POST['url_sito']) == 'http://') {
        die('<span style="color: rgb(255,0,0)"><b>' . $lang['errore'] . '</b></span></td></tr></table></div></body></html>');
    }

    //CREO LA TABELLA BAN
    
    if (mysqli_query($db,"CREATE TABLE IF NOT EXISTS `$tab_ban` (
  `id_ban` smallint(1) unsigned NOT NULL AUTO_INCREMENT,
  `ban_word` varchar(255) DEFAULT NULL,
  `ban_ip` varchar(15) DEFAULT NULL,
  `dataora` INT(10) UNSIGNED NOT NULL,
  `login_errati` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `white_list` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_ban`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
")) {
        echo $lang['tabella'] . ' ' . $tab_ban . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL1: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //CREO LA TABELLA DI CONFIGURAZIONE
    
    if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_config` (
  `nome_sito` varchar(40) NOT NULL,
  `url_sito` varchar(50) NOT NULL,
  `max_archivio` tinyint(1) UNSIGNED NOT NULL DEFAULT '10',
  `max_archivio_parole` smallint(1) UNSIGNED NOT NULL DEFAULT '50',
  `max_ricerche` tinyint(1) UNSIGNED NOT NULL DEFAULT '20',
  `commenti_per_page` tinyint(1) UNSIGNED NOT NULL DEFAULT '20', 
  `tags_per_page` tinyint(1) UNSIGNED NOT NULL DEFAULT '20',   
  `moderazione_commenti` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `disattivazione_commenti` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',  
  `max_tit_include` tinyint(1) UNSIGNED NOT NULL DEFAULT '5',
  `max_parole_include` smallint(1) UNSIGNED NOT NULL DEFAULT '0',
  `formato_data` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `max_gest_news` tinyint(1) UNSIGNED NOT NULL DEFAULT '20',
  `max_utenti` tinyint(1) UNSIGNED NOT NULL DEFAULT '20',
  `max_news_personali` tinyint(1) UNSIGNED NOT NULL DEFAULT '20',
  `max_gest_comm` tinyint(1) UNSIGNED NOT NULL DEFAULT '20', 
  `nuova_news_day` tinyint(1) UNSIGNED NOT NULL DEFAULT '10',
  `max_file_size` mediumint(1) unsigned NOT NULL DEFAULT '51200',
  PRIMARY KEY (`nome_sito`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;")) {
        echo $lang['tabella'] . ' ' . $tab_config . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL2: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //POPOLO LA TABELLA DI CONFIGURAZIONE
    
    if (mysqli_query($db, "INSERT INTO `$tab_config` (`nome_sito`, `url_sito`, `max_archivio`, `max_archivio_parole`, `max_ricerche`, `commenti_per_page`, `tags_per_page`, `moderazione_commenti`, `max_tit_include`, `max_parole_include`, `formato_data`, `max_gest_news`, `max_utenti`, `max_news_personali`, `max_gest_comm`, `nuova_news_day`) VALUES ('" . mysqli_real_escape_string($db, $_POST['nome_sito']) . "', '" . mysqli_real_escape_string($db, $_POST['url_sito']) . "', 15, 0, 15, 20, 20, 0, 6, 0, 1, 20, 20, 20, 20, 1)")) {
        echo $lang['tabella'] . ' ' . $tab_config . ' ' . $lang['popolata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL3: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //CREO LA TABELLA DEGLI UTENTI
    
    if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_utenti` (
  `user_id` smallint(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_cognome` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `livello_id` tinyint(1) UNSIGNED NOT NULL DEFAULT '3',
  `user_password` varchar(255) NOT NULL,
  `attivo` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `permessi` enum('letture','nessuno','tutto','upload') NOT NULL DEFAULT 'tutto',
  `autorizza_news` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `mostra_link` varchar(8) NOT NULL DEFAULT 'nome',
  `email_nascosta` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `sito` varchar(50) DEFAULT NULL,
  `im` varchar(8) DEFAULT NULL,
  `im_num` varchar(32) DEFAULT NULL,
  `facebook` varchar(50) DEFAULT NULL,
  `twitter` varchar(15) DEFAULT NULL,
  `instagram` varchar(30) DEFAULT NULL,
  `data_nascita` char(10) DEFAULT NULL,
  `citta` varchar(50) DEFAULT NULL,
  `occupazione` varchar(50) DEFAULT NULL,
  `hobby` varchar(255) DEFAULT NULL,
  `ultimo_accesso` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data_registrazione` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `token` char(40) DEFAULT NULL,
  `cookie` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `new_pwd` varchar(255) DEFAULT NULL,
  `key_pwd` char(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `nome_cognome` (`nome_cognome`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")) {
        echo $lang['tabella'] . ' ' . $tab_utenti . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL4: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //POPOLO LA TABELLA DEGLI UTENTI
	$password_utente = password_hash($_POST['user_password'], PASSWORD_DEFAULT);
    
    if (mysqli_query($db, "INSERT INTO `$tab_utenti` (nome_cognome, email, livello_id, attivo, user_password, permessi, mostra_link, email_nascosta, ultimo_accesso, data_registrazione) VALUES ('" . htmlspecialchars($_POST['nome_cognome'], ENT_QUOTES) . "', '" . mysqli_real_escape_string($db, $_POST['email']) . "', 1, 1, '$password_utente', 'tutto', 'nome', 1, 0, " . time() . ")")) {
        echo $lang['tabella'] . ' ' . $tab_utenti . ' ' . $lang['popolata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL5: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //CREO LA TABELLA DELLE NEWS
    
    if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_news` (
  `id` mediumint(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titolo` varchar(150) NOT NULL,
  `testo` text NOT NULL,
  `user_id` smallint(1) UNSIGNED NOT NULL,
  `id_cat` tinyint(1) UNSIGNED NOT NULL,
  `data_pubb` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `letture` mediumint(1) UNSIGNED NOT NULL DEFAULT '0',
  `immagine` varchar(100) NULL DEFAULT NULL,
  `nosmile` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `news_approvata` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `abilita_commenti` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `notifica_commenti` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `ip` VARCHAR(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `titolo` (`titolo`),
  FULLTEXT KEY `testo` (`testo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")) {
        echo $lang['tabella'] . ' ' . $tab_news . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL6: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //CREO LA TABELLA DEI LIVELLI
    
    if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_livelli` (
  `livello_id` tinyint(1) UNSIGNED NOT NULL DEFAULT '3',
  `nome_livello` varchar(14) NOT NULL,
  PRIMARY KEY (`livello_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;")) {
        echo $lang['tabella'] . ' ' . $tab_livelli . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL7: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //POPOLO LA TABELLA DEI LIVELLI
    
    if (mysqli_query($db, "INSERT INTO `$tab_livelli` (livello_id, nome_livello) VALUES (1, 'Amministratore'), (3, 'Redattore');")) {
        echo $lang['tabella'] . ' ' . $tab_livelli . ' ' . $lang['popolata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL8: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //CREO LA TABELLA DELLE CATEGORIE
    
    if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_categorie` (
  `id_cat` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(30) NOT NULL,
  `img_categoria` varchar(70) NOT NULL DEFAULT '/$news_dir/$img_dir/cat_d.png',
  PRIMARY KEY (`id_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")) {
        echo $lang['tabella'] . ' ' . $tab_categorie . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL9: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //POPOLO LA TABELLA DELLE CATEGORIE
    
    if (mysqli_query($db, "INSERT INTO `$tab_categorie` (`nome_categoria`) VALUES ('Annunci');")) {
        echo $lang['tabella'] . ' ' . $tab_categorie . ' ' . $lang['popolata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL10: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

    //CREO LA TABELLA DEI COMMENTI
    
    if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_commenti` (
  `id_comm` int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_news` mediumint(1) UNSIGNED NOT NULL,
  `approvato` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `commento` text CHARACTER SET latin1 NOT NULL,
  `autore` varchar(40) CHARACTER SET latin1 NOT NULL,
  `data_comm` int(10) UNSIGNED NOT NULL,
  `email_autore` varchar(50) CHARACTER SET latin1 NOT NULL,
  `sito_autore` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `ip_autore` varchar(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_comm`),
  FULLTEXT KEY `commento` (`commento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")) {
        echo $lang['tabella'] . ' ' . $tab_commenti . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL11: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }
    
    //CREO LA TABELLA DEI TAGS
     if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_tags` (
	`id_tag` SMALLINT(1) UNSIGNED NOT NULL AUTO_INCREMENT , 
	`tag` VARCHAR(20) NOT NULL ,
	 PRIMARY KEY (`id_tag`)) 
	 ENGINE = MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")) {
        echo $lang['tabella'] . ' ' . $tab_tags . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL12: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }

	//CREO LA TABELLA RELAZIONI TAGS-NEWS
     if (mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_tags_rel` (
	`id_tag_rel` int(1) NOT NULL AUTO_INCREMENT,
	`id_news` mediumint(1) UNSIGNED NOT NULL ,
	`id_tag` smallint(1) UNSIGNED NOT NULL ,
	`visibile` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
	`data_pubb_tag` int(10) UNSIGNED NOT NULL DEFAULT '0',
	 PRIMARY KEY (`id_tag_rel`)) 
	 ENGINE = MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")) {
        echo $lang['tabella'] . ' ' . $tab_tags_rel . ' ' . $lang['creata'] . ' <b>OK</b><br />';
    }
    else {
        echo '<font color="#FF0000">SQL13: ' . mysqli_error($db) . ' ... ERROR</font><br />';
    }


    
    echo '<br /><b>' . $lang['completato'] . ' <a href="../admin/login.php">' . $lang['pannello'] . '</a>.</b>';
}
?><br /><br /></td>        
        </tr>      
      </table>         
    </div>          
  </body>      
</html>
