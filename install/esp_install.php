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

if (basename($_SERVER['SCRIPT_NAME']) == 'esp_install.php') {
    die("Internal file");
}
$lang = Array(
	'pagina' => 'nueva instalaci&oacute;n',
	'guida' => 'orientar',
	'licenza' => 'Acuerdo de licencia',
	'licenza2' => 'Acepto los t&eacute;rminos de la licencia GNU GENERAL PUBLIC LICENSE',
	'impostazioni' => 'Ajustes iniciales',
	'nome_cognome' => 'Nombre y apellidos',
	'nome_cognome2' => 'Tu nombre o apodo',
	'email' => 'Email v&aacute;lida',
	'email2' => 'Ser&aacute; necesario para acceder al sistema',
	'password' => 'Elija una contraseña',
	'password2' => 'Elegir una contraseña de acceso',
	'sito' => 'Nombre del sitio',
	'sito2' => 'Ejemplo: Spacemarc.it',
	'url' => 'URL sitio',
	'url2' => 'Con http:// y sin / final',
	'installa' => 'Instalar',
	'errore' => 'Todos los campos y la aceptaci&oacute;n de la licencia se requiere',
	'tabella' => 'Mesa',
	'creata' => 'creado con &eacute;xito...',
	'popolata' => 'rellena con &eacute;xito...',
	'completato' => 'Hecho. Ahora elimine, mover o cambiar el nombre el directorio &quot;install&quot; y obtener acceso a', 
	'pannello' => 'panel de control',
	'aggiornamento' => 'actualizar la versi&oacute;n',
	'aggiornamento2' => 'Actualizar <b>Spacemarc News</b> por <b>1.2.4</b> a <b>1.2.5</b>',
    'pwd_attuale' => 'Contrase&ntilde;a actual',
    'pwd_no_corr' => 'La contrase&ntilde;a no coincide con',
  	'btn_aggiorna' => 'Actualizar',
	'conferma' => '(Haga clic en esperar hasta que el mensaje de confirmaci&oacute;n)',
	'errore2' => 'La aceptaci&oacute;n de la licencia se requiere'  
);
?>
