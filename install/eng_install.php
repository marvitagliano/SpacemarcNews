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

if (basename($_SERVER['SCRIPT_NAME']) == 'eng_install.php') {
    die("Internal file");
}
$lang = Array(
	'pagina' => 'new installation',
	'guida' => 'tutorial',
	'licenza' => 'License Agreement',
	'licenza2' => 'I accept the terms of the GNU GENERAL PUBLIC LICENSE',
	'impostazioni' => 'Initial Settings',
	'nome_cognome' => 'Name and surname',
	'nome_cognome2' => 'Your name or Nickname',
	'email' => 'Valid email address',
	'email2' => 'Will need to access the system',
	'password' => 'Choose a password',
	'password2' => 'Choose a password for access',
	'sito' => 'Name of web site',
	'sito2' => 'Example: Spacemarc.it',
	'url' => 'Web site URL',
	'url2' => 'With http:// and without final /',
	'installa' => 'Install',
	'errore' => 'All fields and acceptance of the license are required',
	'tabella' => 'Table',
	'creata' => 'successfully created...',
	'popolata' => 'successfully populated...',
	'completato' => 'Done. Now delete, move or rename the directory &quot;install&quot; and get access to', 
	'pannello' => 'control panel',
  	'aggiornamento' => 'version upgrade',
	'aggiornamento2' => 'Upgrade <b>Spacemarc News</b> from <b>1.2.4</b> to <b>1.2.5</b>',
	'pwd_attuale' => 'Current password',
    'pwd_no_corr' => 'The password does not match',	
  	'btn_aggiorna' => 'Upgrade',
	'conferma' => '(Click then wait until the confirm message)',
  	'errore2' => 'The acceptance of the license is required'  
);
?>
