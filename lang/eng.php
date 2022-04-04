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

if (basename($_SERVER['SCRIPT_NAME']) == 'eng.php') {
    die("Internal file");
}
$lang = Array(

  	/* ARCHIVIO */
  	'pagina_archivio' => 'News archive',
  	'archivio' => 'Archive',
  	'piu_commentate' => 'most commented',
  	'autore_az' => 'author (A-Z)',
  	'tutte' => 'ALL',
  	'arch_letture' => 'visits',
  	'arch_commenti' => 'comments',
  
  	/* NEWS */
  	'cerca_titolo' => 'Search the news',
  	'condividi' => 'Share',
  	'di_news' => 'by',
  	'di' => 'of',
  	'categorie_select' => '-CATEGORIES-',
  	'stampa' => 'Print',
  	'inserisci_commento' => 'Insert comment',
  	'nome_commento' => 'Name (required)',
  	'email_commento' => 'Email (required, it will not be published)',
  	'sitoweb_commento' => 'Web site',
  	'quote' => 'Quote',
  	'risposta_a' => 'In reply to:',  	
  	'da_approvare' => 'To be approved',
  	'report' => 'Report',  
  	'report_spam' => 'Spam',
  	'report_molestie' => 'Harassment',  	
  	'report_ot' => 'OffTopic',  	
	'report_segnalato' => 'Comment already reported',	
  	'invia' => 'Send',	
	'report_email_oggetto' => 'Check comment',
	'report_email_testo' => 'You are requested to check the following comment for possible illicit content',	  	
	'back_news' => 'Back to news',		
  	'nuovo_commento_email' => 'New comment for news',
  	'compila_correttamente' => 'Fill the fields correctly',
  	'segnala_nome' => 'Your name',
  	'segnala_email' => 'Your email',
  	'segnala_email_dest' => 'Recipient email',
  	'leggi_news1' => 'Read this news at',
  	'leggi_news2' => 'reports to you the following news',
  	'leggi_news3' => 'You can read this news at',
  	'leggi_news_disclaimer' => 'Email sent by a visitor from',
  	'leggi_news_disclaimer2' => 'If it contains spam or offensive comments contact the webmaster bringing the message header and text.',
  	'pagina_stampa' => 'Printable version',
  	'senza_immagini' => 'Hide images',
  	'con_immagini' => 'Show images',
  	'copyright' => 'All right reserved',
  	'pagina' => 'Page',
  	'notizia_inesistente' => 'Sorry, the news that you have selected does not exist.',
    'citazione' => 'Quote',
    'codice' => 'Code',
    'immagine' => 'Image',
  
    /* LOGIN */
    'password' => 'Password',
    'ricorda' => 'Stay logged',
    'ricorda_title' => 'For 30 days or until you click Logout',
    'signin' => 'Log in',
    'showpwd' => 'Show password',
    'pwdsend' => 'Forgot your password?',
    'user_non_aut' => 'Unauthorized user',
    'invalid_user_pass' => 'Invalid email or password',
    'ip_bloccato' => 'Too many login attempts, try again in few minutes',
    'ultimo_tentativo' => 'Warning: you can make a last attempt after which you will have to wait a few minutes to try again',
    'send_pwd_ok' => 'A new password was sent to your email address',
    'send_pwd_error' => 'Email not found',
    'send_pwd_subject' => 'News Admin: new password',
    'send_pwd_body1' => 'New password for user ',
    'send_pwd_body2' => 'New password:',
    'send_pwd_body3' => 'Clik the link to activate it:',
    'send_pwd_body4' => 'If you did not request this notification please delete it.',
    'new_pwd_active' => 'New password activeted',
    'invalid_key' => 'Invalid key',
    'logout' => 'Logout',

    /* MENU */
    'inserisci' => 'Insert news',
    'gestione_news' => 'All news',
    'categorie' => 'Categories',
    'commenti' => 'Comments',
    'elenco_utenti' => 'Users',
    'ricerca_news' => 'Search',
    'profilo_utente' => 'Profile',
    'profilo_admin' => 'Profile',
    'impostazioni' => 'Settings',

    /* INSERT - EDIT NEWS */
    'pagina_inserisci' => 'Insert news',
    'titolo' => 'Title',
    'testo' => 'Text',
    'img_apertura' => 'Opening image',
    'letture' => 'Visits',
    'tags' => 'Tags',
    'tags_descr' => 'comma separated',
	'posts_tags' => 'News with tag: ', 
	'no_tag' => 'No news with this tag',
    'inserisci_tag' => 'Add tag',
	'rimuovi_tag' => 'Delete tag', 
	'tags_orfani' => 'Orphaned tags',
    'opzioni' => 'Options',
    'commenti_on' => 'Allow comments',
    'commenti_email' => 'New comment email notify',
    'data_pubblicazione' => 'Publication date', 
    'data_futura' => 'dd/mm/yyyy hh:mm',
    'codes' => 'BBCodes On<br />Smilies On<br />HTML Off',
    'uploadtext' => '<br />apk, ipa, bmp, gif, jpg, jpeg, png, psd, gpx, kml, kmz, gz, m4a, mp3, mp4, rar, zip, torrent, doc, docx, ods, odt, pdf, xml, xls, xlsx - ',
    'chmod' => ' Directory permission ',
    'numletture' => 'Initial visits',
    'file_upload' => 'File upload',
    'nosmilies' => 'Disable smilies',
    'cambia_data' => 'Change date',
    'canc_news' => 'Delete this news',
    'preview' => 'News preview:',
    'canc_news_ok' => 'News successfully deleted',
    'canc_news_error' => 'Unable to delete the news',
    'tit_text_obbl' => 'Title and Text required',
    'edit_news_ok' => 'News successfully edited',
    'edit_news_error' => 'Unable to edit the news',
    'insert_news_ok' => 'News successfully inserted',
    'insert_news_error' => 'Unable to insert the news',
    'insert_tags_ok' => 'Tags successfully added',
    'insert_tags_error' => 'Unable to add the tags',
    'canc_tags_ok' => 'Tags successfully deleted',
    'canc_tags_error' => 'Unable to delete the tags', 
	'inserisci_un_tag' => 'Enter at least one tag',
    'seleziona_un_tag' => 'Select at least one tag',
    'btn_insert' => 'Insert',
    'btn_preview' => 'Preview',
    'btn_modifica' => 'Edit',
    'btn_cancella' => 'Cancel',
    'hide_preview' => 'hide',
    'show_preview' => 'show',
    'dim_normale' => 'Medium',
    'dim_piccolo' => 'Small',
    'dim_grande' => 'Large',
    'dim_mgrande' => 'X-Large',

    /* ALL NEWS */
    'canc_news_user_ok' => 'News successfully deleted',
    'canc_news_user_error' => 'Unable to delete the news',
    'selez_news_error' => 'You have not selected any news',
    'azzera_ls_ok' => 'Resetted visits',
    'azzera_ls_error' => 'Unable to reset visits',
    'associazione_ok' => 'News moved to new author',
    'associazione_errore' => 'Unable to move the news',
    'autore' => 'Author',
    'data' => 'Date',
    'categoria' => 'Category',
    'news_approvata' => 'Approved',
    'sposta_news' => 'Move news in',
    'modifica' => 'Edit',
    'leggi' => 'View',
    'operazioni' => '-OPERATIONS-',
    'azzera_letture' => 'Reset visits',
    'cancella_news' => 'Delete news',
    'cambia_autore' => 'Change author',
    'cancella_commenti' => 'Delete comments',
    'commenti_off' => 'Disable comments',
    'notifica_commenti_on' => 'New comments notify',
    'notifica_commenti_off' => 'Disable comms notify',
    'scegli' => '-SELECT-',
    'nopopup' => 'You must to enable popup',
    'comm_disab_icon' => 'Disabled comments',
    'notifica_disab_icon' => 'Disabled comments notify',
    'vai' => 'Go',
    'pagina_sposta' => 'Move news to another author',
    'chiudi' => 'Close',
    'chiudi_popup' => 'Close and refresh',

    /* COMMENTS */
    'approvato' => 'Approved',
    'news_cancellata' => 'Deleted news',
    'no' => 'NO',
    'approva' => 'Approve',
    'approva_commenti' => 'Approve comment',
    'disapprova_commenti' => 'Disapprove comment',
    'modifica_commento' => 'Edit comment',
    'approva_news' => 'Approve news',
    'disapprova_news' => 'Disapprove news',
    'attenzione_commento' => 'WARNING: deleted comment can not be recovered',
    'parole_ip_descr' => 'Words and IPs to ban',
    'ip_range' => '* for classes - for range',
    'selez_comm_error' => 'You have not selected any comments',
    'canc_commenti_ok' => 'Comments successfully deleted',
    'canc_commenti_error' => 'Unable to delete the comments',
    'abilita_commenti_ok' => 'Comments successfully enabled',
    'abilita_commenti_error' => 'Unable to active the comments',
    'disabilita_commenti_ok' => 'Comments successfully disabled',
    'disabilita_commenti_error' => 'Unable to disable the comments',
    'notifica_commenti_ok' => 'New comments notify enabled',
    'notifica_commenti_error' => 'Unable to active new comments notify',
    'disab_notifica_commenti_ok' => 'New comments notify disabled',
    'disab_notifica_commenti_error' => 'Unable to disable new comments notify',
    'news_approvata_ok' => 'News successfully approved',
    'news_approvata_error' => 'Unable to approve the news',
    'news_disapprovata_ok' => 'News successfully disapproved',
    'news_disapprovata_error' => 'Unable to disapprove the news',
    'approva_commenti_ok' => 'Comments successfully approved',
    'approva_commenti_error' => 'Unable to approve the comments',
    'disapprova_commenti_ok' => 'Comments successfully disapproved',
    'disapprova_commenti_error' => 'Unable to disapprove the comments',
    'commenti_campi_obb' => 'Name, email and comment required',
    'modifica_commento_ok' => 'Comment successfully edited',
    'modifica_commento_error' => 'Unable to edit the comment',
    'inserisci_commento_ok' => 'Comment successfully inserted',
    'inserisci_commento_error' => 'Unable to insert the comments',
    'commento_da_approvare' => 'Comment inserted and waiting to approve',
    'commenti_disabilitati' => 'Comments temporarily disabled',
    'testo_moderazione' => 'Comments will be moderated',
    'errore_parola_ban' => 'Invalid word syntax',
    'parola_ban_ok' => 'Word successfully inserted',
    'errore_rimozione_parola_ban' => 'Select at least one word',
    'rimozione_parola_ban_ok' => 'Word successfully deleted',
    'errore_ip_ban' => 'Invalid IP syntax',
    'ip_ban_ok' => 'IP successfully inserted',
    'errore_rimozione_ip_ban' => 'Select at least one IP',
    'rimozione_ip_ban_ok' => 'IP successfully deleted',
    'login_errato' => ' - incorrect login',
    'ban_commento' => ' - comment ban',    
    'tot_login_errati' => 'Incorrect Login:',
    'tot_ban_commenti' => 'Bans comments:',
    'antispam_numb' => 'Antispam: type the sum of numbers<br />',
    'antispam_word' => 'Antispam: type letters only (case sensitive)<br />',
    'antispam_only_numb' => 'Antispam: type numbers only<br />',    
    'antispam_first_last_nr' => 'Antispam: type the first and last numbers<br />',
    'antispam_first_last_lt' => 'Antispam: type the first and last letter (case sensitive)<br />',
	'antispam_first_lt_last_nr' => 'Antispam: type the first letter and the last number<br />',
	'antispam_tutto' => 'Antispam: type the whole sequence (case sensitive)<br />',	    
	'antispam_sec_nr_thi_lt' => 'Antispam: type the second number and the third letter<br />',		

    /* CATEGORIES */
    'rinomina' => 'Rename',
    'modifica_categorie_ok' => 'Category successfully renamed',
    'cancella_categoria_ok' => 'Category successfully deleted',
    'cancella_categoria_errore' => 'You can delete categories with no news',
    'cancella_categoria_news_ok' => 'News successfully deleted',
    'cancella_categoria_news_errore' => 'Error deleting news',
    'nuova_categoria_errore' => 'Type the name correctly',
    'nuova_categoria_ok' => 'Category successfully inserted',
    'news_nuova_categoria_ok' => 'News moved to new category',
    'news_nuova_categoria_errore' => 'Error moving the news',
    'attenzione_categorie' => 'WARNING: YOU WILL DELETE',
    'new_cat' => 'New category',
    'sposta_news_in' => 'Move news from',
    'a' => 'to',
    'sposta_categoria' => 'Move category',
    'sposta_news_di' => 'Move news of',
    'tu' => 'You',
    'in_categoria' => 'in category',
    'sposta_news' => 'Move news',
    'img_cat' => 'Categories images',
    'associa_cat' => 'relate to category',
    'btn_cancella_news' => 'Delete news',

    /* USERS */
	'account' => 'Account',
    'news' => 'News',
    'aut_news' => 'Auth. news',
    'nome_utente' => 'Full name',
    'permessi' => 'Permissions',
    'attivo' => 'Enabled',
    'disattivo' => 'Disabled',
    'autorizzato' => 'Authorized',
    'non_autorizzato' => 'Unauthorized',
    'data_reg' => 'Registration date',
    'user_news_canc_ok' => 'User and news are be deleted',
    'user_news_canc_error' => 'Unable to delete user and news',
    'canc_utenti_ok' => 'User and news successfully deleted',
    'canc_utenti_error' => 'Unable to delete the users',
    'utenti_attivati_ok' => 'Users successfully actived',
    'utenti_attivati_error' => 'Unable to active the users',
    'utenti_disattivati_ok' => 'Users successfully disabled',
    'utenti_disattivati_error' => 'Unable to disable the users',
    'edit_permessi_ok' => 'Permissions successfully changed',
    'edit_permessi_error' => 'Unable to change the permissions',
    'selez_utente' => 'You have not selected any users',
    'campi_obbligatori' => 'All fields required',
    'user_email_exists' => 'Email is already in use',
    'utente_ok' => 'Inserted user. The password was sent to her email.',
    'utente_ok_stampa' => 'Inserted user.',        
    'utente_error' => 'Unable to insert the user',
    'email_utenti_descr' => 'Send an email (BCC) to selected users',
    'email_utenti_descr2' => 'Send a copy to my email address',
    'email_utenti_campi' => 'Fill the fields and select the email',
    'email_utenti_ok' => 'Email successfully sent',
    'email_utenti_error' => 'Sending email failed',
    'attiva_utenti' => 'Enable users',
    'disattiva_utenti' => 'Disable users',
    'cancella_file' => 'Delete file',
    'permessi_upload' => 'Upload permissions',
    'permessi_letture' => 'Visits permissions',
    'tutti_permessi' => 'All permissions',
    'nessun_permesso' => 'No permissions',
    'blocca_news' => 'Unauthorize news',
    'invia_email' => 'Send email',
    'oggetto' => 'Subject',
    'destinatari' => 'Recipients',
    'messaggio' => 'Message',
    'account_attivato' => 'Account News enabled',
    'ciao' => 'Hi',
    'corpo' => 'your News account is now active. Your control panel:',
    'firma_email' => 'This email was sent by News Administrator',
    'nuovo_utente' => 'Insert new user',
	'invia_accesso' => 'Send access data via e-mail',    

    /* PROFILE */
    'status_disattivo' => 'disabled - ',
    'user_autorizza_news' => 'News must be approved by Administrator',
    'newsinserite' => 'Inserted news',
    'nomecognomedescr' => 'It will be displayed as news author (required)',
    'emailnascosta' => 'Hidden',
    'emaildescr' => 'Type a valid email address (required)',
    'pwd_attuale' => 'Current password',
    'new_pwd' => 'New password',
    'conferma_new_pwd' => 'Confirm new password',
    'passwordattdescr' => 'Type your current password',
    'sitowebdescr' => 'With http:// or https://',
    'interessidescr' => '255 chars limit:',
    'interessidescr2' => 'HTML, Smilies and BBCode Off',
    'opzionidescr' => 'Link for your name',
    'solonome' => 'Name',
    'mostraemail' => 'Email',
    'mostrasito' => 'Web site',
    'mostraprofilo' => 'Profile',
    'mostrafb' => 'Facebook',
    'mostratw' => 'Twitter',
	'mostraig' => 'Instagram',    
    'data_nascita' => 'Date of birth',
    'nascitadescr' => 'mm/dd/yyyy',
    'lavoro' => 'Job',
    'citta' => 'Town',
    'hobby' => 'Hobby',
    'conta' => 'count',
    'autorizza_news' => 'Allow news',
    'canc_disatt' => 'Delete and disable',
    'edit_prof_ok' => 'Profile successfully updated',
    'edit_prof_error' => 'Unable to update the profile',
    'livello' => 'Level',
    'ultimo_accesso' => 'Latest login',
    'ultima' => 'Latest:',
    'per_giorno' => 'per day',
    'dettagli' => 'details',
    'attenzione_utente' => 'WARNING: deleted user can not be recovered',
    'attenzione_news' => 'WARNING: deleted news can not be recovered',
    'attenzione_news_cb' => 'Select the checkbox to confirm',       
    'attenzione_file' => 'WARNING: deleted files can not be recovered',
    'attenzione_utente_disattivato' => 'Disabled user can not login',
    'attiva_account' => 'Enable account',
    'disattiva_account' => 'Disable account',
    'cancella_utente' => 'Delete user',
	'non_disponibile' => 'N/A',
	'modifica_letture' => 'Edit visits',

    /* SEARCH AND REPLACE */
    'pagina_cerca' => 'Search and replace',
    'cerca' => 'Search',
    'sostituisci' => 'Replace',
    'scritte_da' => 'by',
    'da_tutti' => 'ALL',
    'entrambi' => 'both',
    'ordina_per' => 'order by',
    'settimana' => 'in last week',
    'mese' => 'in last month',
    'anno' => 'in last year',
    'sempre' => 'all time',
    'titoli_az' => 'title (A-Z)',
    'categoria_az' => 'category (A-Z)',
    'piu_recenti' => 'latest',
    'piu_lette' => 'most visited',
    'pertinenza' => 'relevancy',
    'max_min_chars' => 'The word must have at least 4 characters',
    'max_min_chars_tag' => 'The tag must have at least 2 characters',        
    'no_results' => 'Sorry, no result',
    'no_results_google' => 'Search with Google',
    'campi_sost_vuoti' => 'Type the word to search and the word to replace',
    'sostituzione_ok' => 'News affected by replacement:',
	'sostituzione_tag_ok' => 'Tag replaced',   
	'sostituzione_commenti_ok' => 'Comments affected by replacement:',
    'sostituzione_errore' => 'No word replaced',
    'risultati' => 'results for',
    'commenti_trovati' => 'comments contain the word',
    'commento' => 'Comment',

    /* ERRORS */
    'pwd_no_corr' => 'The password does not match',
    'pwd_att' => 'Type your current password',
    'pwd_min_chars' => 'The password must have more than 7 characters',
    'pwd_new' => 'Type the new password',
    'pwd_new_diverse' => 'The passwords you entered aren\'t equals',
    'required' => 'Required field',
    'wrong_email' => 'Invalid email',
    'wrong_date' => 'Invalid date syntax',
    'facebook_errato' => 'Invalid Facebook username',
    'twitter_errato' => 'Invalid Twitter username',
    'instagram_errato' => 'Invalid Instagram username',
    'wrong_file' => 'File not allowed',
    'big_file' => 'File too big',
    'utente_cancellato' => 'User successfully deleted',
    'canc_user_error' => 'Error deleting user',
    'utente_disattivato' => 'User successfully disabled',
    'utente_attivato' => 'User successfully enabled',
    'attiva_user_error' => 'Error enabling user',
    'disatt_user_error' => 'Error disabling user',
    'anteprima' => 'For preview you must fill Text field',
    'capslock' => 'Caps lock enabled',
    'antispam_error' => 'Invalid antispam code',

    /* UPLOADED FILES */
    'file' => 'Files',
    'file_inviati' => 'Uploaded file by',
    'file_cancellato' => 'File successfully deleted',
    'file_cancellati' => 'Files successfully deleted',
    'permessi_non_validi' => 'WARNING: invalid permissions for directory',
    'canc_dir_files_ok' => 'File and directory successfully deleted',
    'upload_ok' => 'File successfully uploaded',
    'insert_file' => 'insert it',
    'data_file' => 'File date',
    'select' => 'Select:',
    'select_all' => 'all',
    'select_none' => 'none',
    'delete' => 'Delete',

    /* SETTINGS */
    'impostazioni_pagina' => 'Settings',
    'avanti' => 'Next',
    'indietro' => 'Prev',
    'totale' => 'Total:',
    'solo_numeri' => 'Numbers only',
    'conf_updated' => 'Settings successfully updated',
    'conf_problem' => 'Error updating', 
    'nome_url' => 'Name and URL Web site',
    'nome_sito' => 'Web site name',
    'url_sito' => 'Web site URL',
    'archivio_notizie' => 'News archive',
    'lettere1' => 'Display the first',
    'lettere2' => 'text letters',
    'ricerche_commenti' => 'Search, comments, tags',
    'commenti_per_pagina' => 'Comments per page',
    'ultime_notizie' => 'Latest news',
    'url_sito_descr' => 'URL with http:// or https:// and without trailing slash /',
    'per_page' => 'News per page',
    'per_page_search' => 'Results per page',
    'commenti_per_page' => 'Commenti per pagina',
	'tags_per_page' => 'Tags per pagina',    
    'img_nuova_news' => 'Updated news image',
    'admin_mostra_record' => 'Administration: display record',
    'formato_data' => 'Date and hour format',
    'dim_upload_allegati' => 'Upload size and orphaned attachments',
    'nome_livelli' => 'Levels name',
    'nome_livelli_descr' => 'Administrator and users',
    'info_backup' => 'Info and backup',
    'dimensione' => 'Size',
    'dimensione_totale' => 'Total size',
    'backup_compresso' => 'Zipped backup',
    'backup_save' => 'Save it in ',
	'backup_download' => 'Download',
	'backup_save_error' => 'Impossible to save the backup file',
	'backup_query_error' => 'Error in the database backup',	
	'backup_file_canc' => 'Remove backup file',
    'struttura_tabella' => 'Table structure for table',
    'dati_tabella' => 'Dumping data for table',
    'moderazione_commenti' => 'Moderation comments',
	'disattivazione_commenti' => 'Deactivation comments',
    'max_include' => 'Displayed news',
    'nuova_news_day' => 'Updated news image for days',
    'ip_white_list' => 'IPs unlocked',
    'ip_white_list_descr' => 'IPs that need not be locked in case of incorrect login',      
    'files_orfani' => 'Orphaned attachments',
    'files_orfani_descr' => 'Uploaded files by deleted users',
    'files_orfani_descr2' => 'News containing the files: deleting, the links to files will be invalid.',
    'err_tabella' => 'Select at least one db table',
    'in_eccesso' => 'Overhead',
    'ottimizza' => 'optimize',
    'optimized_ok' => 'Table successfully optimized',
    'optimized_error' => 'Unable to optimize the table',
    'time_gen' => 'Page generation time'
);
?>
