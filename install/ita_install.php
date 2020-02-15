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

if (basename($_SERVER['SCRIPT_NAME']) == 'ita_install.php') {
    die("Internal file");
}
$lang = Array(
	'pagina' => 'nuova installazione',
	'guida' => 'guida',
	'licenza' => 'Accettazione licenza',
	'licenza2' => 'Accetto i termini della licenza GNU GENERAL PUBLIC LICENSE',
	'impostazioni' => 'Impostazioni iniziali',
	'nome_cognome' => 'Nome e cognome',
	'nome_cognome2' => 'Tuo nome e cognome o Nickname',
	'email' => 'Email valida',
	'email2' => 'Servir&agrave; per accedere al sistema',
	'password' => 'Scegli la password',
	'password2' => 'Scegli una password di accesso',
	'sito' => 'Nome del sito',
	'sito2' => 'Esempio: Spacemarc.it',
	'url' => 'URL sito',
	'url2' => 'Con http:// e senza / finale',
	'installa' => 'Installa',
	'errore' => 'Tutti i campi e l&apos;accettazione della licenza sono obbligatori',
	'tabella' => 'Tabella',
	'creata' => 'creata con successo...',
	'popolata' => 'popolata con successo...',
	'completato' => 'Fatto. Ora cancella, sposta o rinomina la directory &quot;install&quot; e accedi al', 
	'pannello' => 'pannello di controllo',
  	'aggiornamento' => 'aggiornamento versione',
  	'aggiornamento2' => 'Aggiornamento di <b>Spacemarc News</b> dalla versione <b>1.2.4</b> alla <b>1.2.5</b>',
    'pwd_attuale' => 'Password attuale',
    'pwd_no_corr' => 'La password non corrisponde',    
  	'btn_aggiorna' => 'Aggiorna',
  	'conferma' => '(Clicca e poi attendi fino al messaggio di conferma)',
  	'errore2' => 'L&apos;accettazione della licenza &eacute; obbligatoria'  
);
?>
