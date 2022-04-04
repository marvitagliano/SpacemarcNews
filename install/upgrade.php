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
require_once (dirname(__FILE__) . '/' . $language . '_install.php');

//connessione a mysql
$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);

//aumento il tempo di timeout dello script a 180 secondi
set_time_limit(180);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">           
  <head>         
    <title>Spacemarc News: <?php echo $lang['aggiornamento']; ?>                    
    </title>                   
    <link rel="stylesheet" href="../style.css" type="text/css" />            
  </head>           
  <body>                   
    <div align="center" class="text">                              
      <form name="upgrade" action="upgrade.php#aggiornamento" method="post">                               
        <table width="900" border="0" cellpadding="2" cellspacing="2">                                                
          <tr>                                           
            <td class="text" align="center" valign="top">                             
              <img src="../images/logonews.gif" alt="logo" /><br /><img src="../docs/<?php echo $language; ?>.png" alt="Lang" /> <?php echo $lang['aggiornamento2']; ?> (<a href="../docs/index_<?php echo $language; ?>.html"><?php echo $lang['guida']; ?></a> - <a href="../docs/changelog.html">changelog</a>)<br /><br />            </td>                                  
          </tr>                                      
          <tr>                                             
            <td class="text2" align="center" bgcolor="#EEEEEE"><b><?php echo $lang['licenza']; ?></b><br />              </td>                                     
          </tr>                                                    
          <tr>                                               
            <td class="text2" align="center">       
<?php
$file = "../docs/gpl-3.0.txt";
$op = fopen($file, "r") or die("Impossibile aprire il file ../docs/gpl-3.0.txt");
$rd = fread($op, filesize($file));
fclose($op);
echo '<textarea name="license" rows="10" cols="100" readonly="readonly">' . $rd . '</textarea>';
?><br />                                                     
              <input type="checkbox" name="lic_OK" id="lic_OK" />                                                
              <label for="lic_OK"><?php echo $lang['licenza2']; ?></label><br /><br />  </td>                                         
          </tr>                                 
          <tr>                                               
            <td align="center"><br /> 
              <input type="submit" name="upgrade" value="<?php echo $lang['btn_aggiorna']; ?>" style="font-weight: bold;" /><br />
              <?php echo $lang['conferma']; ?>
              <br /><br /></td>                                         
          </tr>                                                   
          <tr>                                        
            <td align="center" class="text2" bgcolor="#C2D8FE">Spacemarc News                
              <?php echo $version; ?>  &copy; </td>                                    
          </tr>                                    
        </table>                         
      </form>  <br />  
		<a name="aggiornamento"></a>              
      <table width="900" cellpadding="2" cellspacing="2" border="0">                 
        <tr><td>
<?php

if (isset($_POST['upgrade'])) {
    
    if (!isset($_POST['lic_OK'])) {
        die('<font color=red><b>' . $lang['errore2'] . '</b></font></td></tr></table></div></body></html>');
    }

		//APPLICO LE MODIFICHE ALLE TABELLE
		mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_tags` (
		`id_tag` SMALLINT(1) UNSIGNED NOT NULL AUTO_INCREMENT , 
		`tag` VARCHAR(20) NOT NULL ,
		 PRIMARY KEY (`id_tag`)) 
		 ENGINE = MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
		 
		 mysqli_query($db, "CREATE TABLE IF NOT EXISTS `$tab_tags_rel` (
		`id_tag_rel` int(1) NOT NULL AUTO_INCREMENT,
		`id_news` mediumint(1) UNSIGNED NOT NULL ,
		`id_tag` smallint(1) UNSIGNED NOT NULL ,
		`visibile` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
		`data_pubb_tag` int(10) UNSIGNED NOT NULL DEFAULT '0',		
		 PRIMARY KEY (`id_tag_rel`)) 
		 ENGINE = MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

		mysqli_query($db, "ALTER TABLE `$tab_config` ADD `tags_per_page` TINYINT(1) UNSIGNED NOT NULL DEFAULT '20' AFTER `commenti_per_page`");
		mysqli_query($db, "ALTER TABLE `$tab_config` ADD `disattivazione_commenti` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `moderazione_commenti`");
		
		echo '<b>' . $lang['completato'] . ' <a href="../admin/login.php">' . $lang['pannello'] . '</a>.</b>';

}
?><br /><br /></td>                 
        </tr>             
      </table>              
    </div>             
  </body>       
</html>
