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

if (session_id() == '') {
    session_start();
}

if (basename($_SERVER['SCRIPT_NAME']) == 'menu.php') {
    die("Internal file");
}

//includo i file di configurazione
require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/../lang/' . $language . '.php');

//se non c'è la sessione 'loggato' rimando alla pagina di login

if (!isset($_SESSION['loggato'])) {
    header('Location: ' . $dir_admin . '/login.php');
    exit();
}

//gestione menu amministratore
$name = array (
        $lang['inserisci'],
        $lang['gestione_news'],
        $lang['commenti'],
        $lang['categorie'],
        $lang['ricerca_news'],
        $lang['elenco_utenti'],
        $lang['profilo_admin'],
        $lang['impostazioni']
        );		
$page = array (
           'inserisci.php',
           'gestione_news.php',
           'commenti.php',
           'categorie.php',
           'searchadmin.php',
           'utenti.php',
		   'profilo_admin.php',		   
           'impostazioni.php'
           );
$img = array (
			'<img src="' . $img_path . '/insert.png" border="0" alt="" />',
			'<img src="' . $img_path . '/news.png" border="0" alt="" />',
			'<img src="' . $img_path . '/comm.png" border="0" alt="" />',
			'<img src="' . $img_path . '/categorie.gif" border="0" alt="" />',
			'<img src="' . $img_path . '/search.png" border="0" alt="" />',
			'<img src="' . $img_path . '/utenti.png" border="0" alt="" />',
			'<img src="' . $img_path . '/profilo.png" border="0" alt="" />',
			'<img src="' . $img_path . '/impost.png" border="0" alt="" />'
			);


//gestione menu utente
$nameu = array (
        $lang['inserisci'],
        $lang['gestione_news'],
        $lang['ricerca_news'],
        $lang['profilo_utente']
        );		
$pageu = array (
           'inserisci.php',
           'elenco_news.php',           
           'searchadmin.php',
		   'profilo_utente.php'
           );
$imgu = array (
			'<img src="' . $img_path . '/insert.png" border="0" alt="" />',
			'<img src="' . $img_path . '/news.png" border="0" alt="" />',
			'<img src="' . $img_path . '/search.png" border="0" alt="" />',
			'<img src="' . $img_path . '/profilo.png" border="0" alt="" />'
			);
			
$self = basename($_SERVER['SCRIPT_NAME']);
			
if ($_SESSION['livello_id'] == 1) {
	
	echo '<table width="100%" cellpadding="1" cellspacing="0" border="0" align="center">
    <tr> 
      <td width="15%" align="left" valign="top"><img src="' . $img_path . '/logonews.gif" border="0" alt="Logo" /></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="text2" align="right">' . $_SESSION['nome_cognome_sess'] . '<br /><a href="logout.php" class="piccolo"><b>' . $lang['logout'] . '</b></a><br /><br /><br /></td>
    </tr>
    <tr>';
	
	for ($i = 0; $i <= 7; ++$i) {
		
		if ( $page[$i] == $self ) {
			
			echo '<td class="toprowself" align="center">' . $img[$i] . ' ' . $name[$i] . '</td>';
			
		} else {
			
			echo '<td class="toprow" align="center">' . $img[$i] . ' <a href="' . $page[$i] . '">' . $name[$i] . '</a></td>';
		}
	}
		
    echo '</tr>
  </table>
<br />';
}
else {

    //menu di navigazione utente
    echo '<table width="100%" cellpadding="1" cellspacing="0" border="0" align="center">
    <tr> 
      <td width="20%" align="left" valign="top"><img src="' . $img_path . '/logonews.gif" border="0" alt="Logo" /></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td width="20%" class="text2" align="right">' . $_SESSION['nome_cognome_sess'] . '<br /><a href="logout.php" class="piccolo"><b>' . $lang['logout'] . '</b></a><br /><br /><br /></td>
    </tr>
    <tr>';
    
	for ($i = 0; $i <= 3; ++$i) {
		
		if ( $pageu[$i] == $self ) {
			
			echo '<td class="toprowself" align="center">' . $imgu[$i] . ' ' . $nameu[$i] . '</td>';
			
		} else {
			
			echo '<td class="toprow" align="center">' . $imgu[$i] . ' <a href="' . $pageu[$i] . '">' . $nameu[$i] . '</a></td>';
		}
	}
	      
   echo '</tr>
  </table>
<br />';
}
?>
