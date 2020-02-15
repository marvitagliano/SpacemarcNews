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

if (basename($_SERVER['SCRIPT_NAME']) == 'fra_install.php') {
    die("Internal file");
}
$lang = Array(
	'pagina' => 'nouvelle installation',
	'guida' => 'guider',
	'licenza' => 'Contrat de licence',
	'licenza2' => 'J&apos;accepte les termes de la licence GNU GENERAL PUBLIC LICENSE',
	'impostazioni' => 'R&eacute;glages initiaux',
	'nome_cognome' => 'Nom et pr&eacute;nom',
	'nome_cognome2' => 'Votre nom ou pseudo',
	'email' => 'Adresse email valide',
	'email2' => 'On aura besoin d&apos;acc&eacute;der au syst&egrave;me',
	'password' => 'Choisissez un mot de passe',
	'password2' => 'Choisissez un mot de passe pour l&apos;acc&egrave;s',
	'sito' => 'Nom du site',
	'sito2' => 'Exemple: Spacemarc.it',
	'url' => 'URL site',
	'url2' => 'Avec http:// et sans / finale',
	'installa' => 'Installer',
	'errore' => 'Tous les champs et l&apos;acceptation de la licence sont tenus',
	'tabella' => 'Table',
	'creata' => 'cr&eacute;e avec succ&egrave;s...',
	'popolata' => 'peupl&eacute;e avec succ&egrave;s...',
	'completato' => 'Termin&eacute;. Maintenant supprimer, d&eacute;placer ou renomme le r&eacute;pertoire &quot;install&quot; et tu auras acc&egrave;s &agrave;', 
	'pannello' => 'panneau de contr&ocirc;le',
	'aggiornamento' => 'mettre &aacute; jour la version',
	'aggiornamento2' => 'Mettre &aacute; jour <b>Spacemarc News</b> par <b>1.2.4</b> &aacute; <b>1.2.5</b>',
    'pwd_attuale' => 'Mot de passe actuel',
    'pwd_no_corr' => 'Le mot de passe ne correspond pas',   
  	'btn_aggiorna' => 'Mettre &aacute; jour',
	'conferma' => '(Cliquez ensuite attendre jusqu&apos;&aacute; ce que le message de confirmation)',
	'errore2' => 'L&apos;acceptation de la licence est requise'  
);
?>
