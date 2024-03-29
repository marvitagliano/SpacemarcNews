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

if (basename($_SERVER['SCRIPT_NAME']) == 'fra.php') {
    die("Internal file");
}
$lang = Array(

  	/* ARCHIVES */
  	'pagina_archivio' => 'Archives des nouvelles',
  	'archivio' => 'Archives',
  	'piu_commentate'=> 'les plus comment&egrave;s',
  	'autore_az' => 'auteur (A-Z)',
  	'tutte' => 'TOUS',
  	'arch_letture' => 'lectures',
  	'arch_commenti' => 'commentaires',
  
  	/* NOUVELLES */
  	'cerca_titolo' => 'Rechercher un mot dans les nouvelles',
  	'condividi' => 'Partager',
  	'di_news' => 'de',
  	'di' => 'de',
  	'categorie_select' => '-CATEGORIES-',
  	'stampa' => 'Imprimer',
  	'inserisci_commento' => 'Poster un commentaire',
  	'nome_commento' => 'Nom (obligatoire)',
  	'email_commento' => 'Email (obligatoire, ne sera pas publi&egrave;)',
  	'sitoweb_commento' => 'Site Web',
  	'quote' => 'R&egrave;pondre',
  	'risposta_a' => 'En r&egrave;ponse &aacute;',
  	'da_approvare' => '&Ecirc;tre approuv&egrave;',
  	'report' => 'Rapports',  	
  	'report_spam' => 'Spam',
  	'report_molestie' => 'Harc&eacute;lement',  	
  	'report_ot' => 'Hors sujet',  	
	'report_segnalato' => 'Commentaire d&egrave;j&aacute; rapport&eacute;',
  	'invia' => 'Envoyer',	
	'report_email_oggetto' => 'Ch&eacute;que commentaire',
	'report_email_testo' => 'Vous &#39;etes pri&egrave;s de v&egrave;rifier le commentaire suivant pour un contenu illicite possible',	  	
	'back_news' => 'Retour aux nouvelles',
  	'nuovo_commento_email' => 'Le nouveaux commentaire pour le nouvelle',
  	'compila_correttamente' => 'Remplissez les champs correctement',
  	'segnala_nome' => 'Nom',
  	'segnala_email' => 'Email',
  	'segnala_email_dest' => 'Email destinataire',
  	'leggi_news1' => 'Lire ce nouvelle sur',
  	'leggi_news2' => 'rendra compte le nouvelle suivante',
  	'leggi_news3' => 'Vous pouvez lire à le site Web suivante',
  	'leggi_news_disclaimer' => 'Email envoyè par un visiteur de',
  	'leggi_news_disclaimer2' => 'Si elle contient des commentaires de spam ou offensant contacter le webmaster qui porte l\'entete du message et le texte.',
  	'pagina_stampa' => 'Version imprimable',
  	'senza_immagini' => 'Aucune image',
  	'con_immagini' => 'Comprenant des images',
  	'copyright' => 'Tous droits r&egrave;serv&egrave;s',
  	'pagina' => 'Page',
  	'notizia_inesistente' => 'D&egrave;sol&egrave;, les nouvelles que vous avez s&egrave;lectionn&egrave; n&#39;existe pas.',
    'citazione' => 'Citer',
    'codice' => 'Code',
    'immagine' => 'Image',

    /* ACCEDER */
    'password' => 'Mot de passe',
    'ricorda' => 'Restez connect&egrave;',
    'ricorda_title' => 'Pendant 30 jours ou jusqu&#39;&agrave; ce que vous cliquez sur D&egrave;connexion',
    'signin' => 'Entre',
    'showpwd' => 'montre le mot de passe',    
    'pwdsend' => 'Mot de passe oubli&egrave;?',
    'user_non_aut' => 'Vous n&#39;etes pas autoris&egrave;',
    'invalid_user_pass' => 'Email ou mot de passe invalide',
    'ip_bloccato' => 'Trop de tentatives de connexion, essayez de nouveau en quelques minutes',
    'ultimo_tentativo' => 'Attention: vous pouvez faire une derni&eacute;re tentative apr&eacute;s quoi vous devrez attendre quelques minutes pour essayer &aacute; nouveau',
    'send_pwd_ok' => 'Nous vous avons envoy&egrave; un email avec votre nouveau mot de passe et un lien pour activer',
    'send_pwd_error' => 'Email introuvable',
    'send_pwd_subject' => 'Administration Nouvelles: nouveau mot de passe',
    'send_pwd_body1' => 'Demander nouveau mot de passe pour l\'utilisateur ',
    'send_pwd_body2' => 'Le nouveau mot de passe:',
    'send_pwd_body3' => 'Cliquez sur ce lien pour activer:',
    'send_pwd_body4' => 'Si ce n\'est pas vous demander il peut tout simplement ignorer ce message.',
    'new_pwd_active' => 'Nouveau mot de passe activ&egrave;',
    'invalid_key' => 'Code invalide',
    'logout' => 'D&egrave;connexion',

    /* MENU */
    'inserisci' => 'Ecrire des nouvelles',
    'gestione_news' => 'Gestion des nouvelles',
    'categorie' => 'Cat&egrave;gories',
    'commenti' => 'Commentaires',
    'elenco_utenti' => 'Utilisateurs',
    'ricerca_news' => 'Recherche',
    'profilo_utente' => 'Profil',
    'profilo_admin' => 'Profil',
    'impostazioni' => 'Param&egrave;tres',

    /* INSERTION ET MODIFICATION NOUVELLES */
    'pagina_inserisci' => 'Ecrire des nouvelles',
    'titolo' => 'Titre',
    'testo' => 'Texte',
    'img_apertura' => 'Image d&#39;ouverture',
    'letture' => 'Lectures',
    'tags' => '&Eacute;tiquettes',
    'posts_tags' => 'Nouvelles avec &eacute;tiquette: ',
    'tags_descr' => 'S&eacute;&eacute;par&eacute;es par des virgules',
	'no_tag' => 'Pas de nouvelles avec ce &eacute;tiquette',    
    'inserisci_tag' => 'Entrer &eacute;tiquettes',
	'rimuovi_tag' => 'Supprimer &eacute;tiquettes',
	'tags_orfani' => '&Eacute;tiquettes orphelins',	
    'opzioni' => 'Options',  
    'commenti_on' => 'Activer les commentaires',
    'commenti_email' => 'Email de notification nouveau commentaire',
    'data_pubblicazione' => 'Date de sortie',
    'data_futura' => 'jj/mm/aaaa hh:mm',
    'codes' => 'BBCode activ&egrave;<br />&Egrave;motic&ocirc;nes activ&egrave;<br />HTML desactiv&egrave;',
    'uploadtext' => '<br />apk, ipa, bmp, gif, jpg, jpeg, png, psd, gpx, kml, kmz, gz, m4a, mp3, mp4, rar, zip, torrent, doc, docx, ods, odt, pdf, xml, xls, xlsx - ',
    'chmod' => ' Permissions des r&egrave;pertoires ',
    'numletture' => 'Nombre de lectures initiale',
    'file_upload' => 'Transf&egrave;rer des fichiers',
    'nosmilies' => 'D&egrave;sactiver les &Egrave;motic&ocirc;nes',
    'cambia_data' => 'Changer de date',
    'canc_news' => 'Supprimer ces nouvelles',
    'preview' => 'Preview nouvelle:',
    'canc_news_ok' => 'Nouvelles supprim&egrave; avec succ&egrave;s',
    'canc_news_error' => 'Impossible de supprimer le nouvelle',
    'tit_text_obbl' => 'Les champs Titre et texte sont obligatoire',
    'edit_news_ok' => 'Nouvelle modifi&egrave; avec succ&egrave;s',
    'edit_news_error' => 'Impossible de modifier le nouvelle',
    'insert_news_ok' => 'Nouvelle ins&egrave;r&egrave; avec succ&egrave;s',
    'insert_news_error' => 'Impossible d&#39;ins&egrave;rer les nouvelles',
    'insert_tags_ok' => '&Eacute;tiquettes ins&egrave;r&egrave; avec succ&egrave;s',
    'insert_tags_error' => 'Impossible d&#39;ins&egrave;rer les &eacute;tiquettes',        
	'canc_tags_ok' => '&Eacute;tiquettes supprim&egrave; avec succ&egrave;s',
    'canc_tags_error' => 'Impossible d&#39;ins&egrave;rer les &eacute;tiquettes',
    'inserisci_un_tag' => 'Entrez au moins un &eacute;tiquette',
    'seleziona_un_tag' => 'S&eacute;lectionner au moins un &eacute;tiquette',    
    'btn_insert' => 'Poster',
    'btn_preview' => 'Avant-premi&egrave;re',
    'btn_modifica' => 'Modification',
    'btn_cancella' => 'R&egrave;initialiser',
    'hide_preview' => 'cacher',
    'show_preview' => 'montrer',
    'dim_normale' => 'Normal',
    'dim_piccolo' => 'Petit',
    'dim_grande' => 'Grand',
    'dim_mgrande' => 'Tr&egrave;s grand',

    /* GESTION NOUVELLES */
    'canc_news_user_ok' => 'Nouvelles supprim&egrave; avec succ&egrave;s',
    'canc_news_user_error' => 'Impossible de supprim&egrave; les nouvelles',
    'selez_news_error' => 'Vous n&#39;avez pas s&egrave;lectionn&egrave; des nouvelles',
    'azzera_ls_ok' => 'Z&egrave;ro lectures',
    'azzera_ls_error' => 'Impossible d&#39;effacer lectures',
    'associazione_ok' => 'Nouvelles se sont reli&egrave;s &agrave; le nouvel auteur',
    'associazione_errore' => 'Impossible de d&egrave;placer les nouvelles',
    'autore' => 'Auteur',
    'data' => 'Date',
    'categoria' => 'Cat&egrave;gorie',
    'news_approvata' => 'Approuv&egrave;',
    'sposta_news' => 'D&egrave;placer les nouvelles in',
    'modifica' => 'Modification',
    'leggi' => 'Voir',
    'operazioni' => '-OPÉRATIONS-',
    'azzera_letture' => 'R&egrave;initialise lectures',
    'cancella_news' => 'Supprim&egrave; nouvelles',
    'cambia_autore' => 'Changer l&#39;auteur',
    'cancella_commenti' => 'Supprim&egrave; commentaires',
    'commenti_off' => 'D&egrave;sactiver commentaires',
    'notifica_commenti_on' => 'Notification des nouveaux commentaires',
    'notifica_commenti_off' => 'D&egrave;sactiver la notification commentaires',
    'scegli' => '-CHOISIR-',
    'nopopup' => 'Pour ce faire, vous devez activer les popups',
    'comm_disab_icon' => 'Commentaires ne sont pas activ&egrave;s',
    'notifica_disab_icon' => 'Notification commentaires d&egrave;sactiv&egrave;',
    'vai' => 'Envoyer',
    'pagina_sposta' => 'Li&egrave; les nouvelles &agrave; un autre auteur',
    'chiudi' => 'Fermer',
    'chiudi_popup' => 'Fermer et mises &agrave; jour',

    /* GESTION COMMENTAIRES */
    'approvato' => 'Approuv&egrave;',
    'news_cancellata' => 'Nouvelle annul&egrave;',
    'no' => 'NON',
    'approva' => 'Approuver',
    'approva_commenti' => 'Approuver les commentaires',
    'disapprova_commenti' => 'Desapprouver les commentaires',
    'modifica_commento' => 'Modifier le commentaire',
    'approva_news' => 'Approuver nouvelles',
    'disapprova_news' => 'D&egrave;sapprouve nouvelles',
    'attenzione_commento' => 'ATTENTION: supprimer le commentaire ne peut pas &ecirc;tre recuper&egrave;',
    'parole_ip_descr' => 'Les mots et les adresses IP en vue d&#39;interdire',
    'ip_range' => '* pour les classes - pour la gamme',
    'selez_comm_error' => 'Vous n&#39;avez selectionn&egrave; aucun commentaires',
    'canc_commenti_ok' => 'Commentaires supprim&egrave;s avec succ&egrave;s',
    'canc_commenti_error' => 'Impossible de supprimer les commentaires',
    'abilita_commenti_ok' => 'Commentaires permis succ&egrave;s',
    'abilita_commenti_error' => 'Impossible d&#39;activer les commentaires',
    'disabilita_commenti_ok' => 'Commentaires desactiv&egrave;s avec succ&egrave;s',
    'disabilita_commenti_error' => 'Impossible de desactiver les commentaires',
    'notifica_commenti_ok' => 'Notification des nouveaux commentaires activ&egrave;',
    'notifica_commenti_error' => 'Impossible d&#39;activer la notification commentaire',
    'disab_notifica_commenti_ok' => 'Notification de nouveaux commentaires desactiv&egrave;',
    'disab_notifica_commenti_error' => 'Impossible desactiver la notification commentaire',
    'news_approvata_ok' => 'Nouvelles approuv&egrave; succ&eacute;s',
    'news_approvata_error' => 'Pourrait ne pas approuver les nouvelles',
    'news_disapprovata_ok' => 'Nouvelles d&egrave;sapprouvent succ&eacute;s',
    'news_disapprovata_error' => 'Impossible d&egrave;sapprouver nouvelles',
    'approva_commenti_ok' => 'Commentaires approuv&egrave; avec succ&egrave;s',
    'approva_commenti_error' => 'Impossible d&#39;approuver les commentaires',
    'disapprova_commenti_ok' => 'Commentaires desapprouv&egrave; avec succ&egrave;s',
    'disapprova_commenti_error' => 'Impossible d&egrave;sapprouv&egrave; les commentaires',
    'commenti_campi_obb' => 'Nom, email et commentaires obligatoire',
    'modifica_commento_ok' => 'Commentaire modifi&egrave; avec succ&egrave;s',
    'modifica_commento_error' => 'Impossible de changer le commentaire',
    'inserisci_commento_ok' => 'Commenter &egrave;crit avec succ&egrave;s',
    'inserisci_commento_error' => 'Impossible d&#39;&egrave;crire un commentaire',
    'commento_da_approvare' => 'Vos commentaires et en attente d&#39;approbation',
    'commenti_disabilitati' => 'Commentaires temporairement d&egrave;sactiv&egrave;s',
    'testo_moderazione' => 'Les commentaires seront soumis &agrave; mod&egrave;ration',
    'errore_parola_ban' => 'Syntaxe mot invalide',
    'parola_ban_ok' => 'Parole inser&egrave; avec succ&egrave;s',
    'errore_rimozione_parola_ban' => 'S&egrave;lectionnez au moins un mot',
    'rimozione_parola_ban_ok' => 'Mot supprim&egrave; avec succ&egrave;s',
    'errore_ip_ban' => 'Syntaxe IP invalide',
    'ip_ban_ok' => 'IP a plac&egrave; avec succ&egrave;s',
    'errore_rimozione_ip_ban' => 'S&egrave;lectionnez au moins un IP',
    'rimozione_ip_ban_ok' => 'IP supprim&egrave; avec succ&egrave;s',
    'login_errato' => ' - connexion incorrect',
    'ban_commento' => ' - commentaire interdit',
    'tot_login_errati' => 'Connexion incorrects:',
    'tot_ban_commenti' => 'Commentaires interdits:',
    'antispam_numb' => 'Antispam: &egrave;crire la somme des nombres<br />',
    'antispam_word' => 'Antispam: il suffit d&#39;&egrave;crire les lettres (majuscules et minuscules)<br />',
    'antispam_only_numb' => 'Antispam: il suffit d&#39;&egrave;crire les nombres<br />',
    'antispam_first_last_nr' => 'Antispam: &egrave;crire les premiers et derniers num&egrave;ros<br />',    
    'antispam_first_last_lt' => 'Antispam: &egrave;crire la premi&eacute;re et la derni&eacute;re lettre (majuscule et minuscule)<br />',
	'antispam_first_lt_last_nr' => 'Antispam: &egrave;crire la premi&eacute;re lettre et la derni&eacute;re num&egrave;ro<br />',
	'antispam_tutto' => 'Antispam: &egrave;crire toute la s&eacute;quence (majuscules et minuscules)<br />',    
	'antispam_sec_nr_thi_lt' => 'Antispam: &egrave;crire le deuxi&acute;me chiffre et la troisi&eacute;me lettre<br />',	

    /* GESTION CATEGORIES */
    'rinomina' => 'Renommer',
    'modifica_categorie_ok' => 'Cat&egrave;gorie renomm&egrave; avec succ&egrave;s',
    'cancella_categoria_ok' => 'Cat&egrave;gorie supprim&egrave; avec succ&egrave;s',
    'cancella_categoria_errore' => 'Vous ne pouvez supprimer des cat&egrave;gories qui n&#39;ont pas de nouvelles',
    'cancella_categoria_news_ok' => 'Nouvelles supprim&egrave; avec succ&egrave;s',
    'cancella_categoria_news_errore' => 'Erreur de suppression des nouvelles',
    'nuova_categoria_errore' => 'Entrez le nom correctement',
    'nuova_categoria_ok' => 'Cat&egrave;gorie cr&egrave;&egrave; avec succ&egrave;s',
    'news_nuova_categoria_ok' => 'Nouvelles d&egrave;plac&egrave; de la cat&egrave;gorie nouvelle',
    'news_nuova_categoria_errore' => 'Erreur lors du d&egrave;placement nouvelles',
    'attenzione_categorie' => 'AVERTISSEMENT: VOUS &Ecirc;TES POUR EFFACER',
    'new_cat' => 'Cr&egrave;er une nouveau',
    'sposta_news_in' => 'D&egrave;placer les nouvelles de',
    'a' => '&agrave;',
    'sposta_categoria' => 'D&egrave;placez la cat&egrave;gorie',
    'sposta_news_di' => 'D&egrave;placer les nouvelles de',
    'tu' => 'Vous',
    'in_categoria' => 'dans la cat&egrave;gorie',
    'sposta_news' => 'D&egrave;placez nouvelles',
    'img_cat' => 'Images des cat&egrave;gories', 
    'associa_cat' => 'associ&egrave; &agrave; la cat&egrave;gorie',
    'btn_cancella_news' => 'Supprimer nouvelles',

    /* GESTION UTILISATEURS */
	'account' => 'Compte',
    'news' => 'Nouvelles',
    'aut_news' => 'Aut. nouvelles',
    'nome_utente' => 'Nom et pr&egrave;nom',
    'permessi' => 'Autorisations',
    'attivo' => 'Activ&egrave;',
    'disattivo' => 'D&egrave;sactiv&egrave;',
    'autorizzato' => 'Autoris&egrave;',
    'non_autorizzato' => 'Non autoris&egrave;',
    'data_reg' => 'Date enregistrement',
    'user_news_canc_ok' => 'Utilisateur et les nouvelles ont &egrave;t&egrave; supprim&egrave;s',
    'user_news_canc_error' => 'Impossible de supprimer l&#39;utilisateur et les nouvelles',
    'canc_utenti_ok' => 'Utilisateurs supprim&egrave; avec succ&egrave;s',
    'canc_utenti_error' => 'Impossible de supprimer des utilisateurs',
    'utenti_attivati_ok' => 'Utilisateurs activ&egrave; avec succ&egrave;s',
    'utenti_attivati_error' => 'Impossible activer aux utilisateurs',
    'utenti_disattivati_ok' => 'Les utilisateurs d&egrave;sactive avec succ&egrave;s',
    'utenti_disattivati_error' => 'Impossible de d&egrave;sactiver des utilisateurs',
    'edit_permessi_ok' => 'Autorisations chang&egrave; avec succ&egrave;s',
    'edit_permessi_error' => 'Impossible de changer les permissions',
    'selez_utente' => 'Vous n&#39;avez pas choisi quel utilisateur',
    'campi_obbligatori' => 'Tous les champs sont obligatoire',
    'user_email_exists' => 'Email est d&egrave;j&agrave; pr&egrave;sent',
    'utente_ok' => 'Vous avez entr&egrave;. Les informations de connexion ont &egrave;t&egrave; envoy&egrave;s &agrave; son email.',
    'utente_ok_stampa' => 'Vous avez entr&egrave;.',    
    'utente_error' => 'Impossible de cr&egrave;er l&#39;utilisateur',
    'email_utenti_descr' => 'Envoyer un email (en CCI) &agrave; s&egrave;lectionn&egrave;',
    'email_utenti_descr2' => 'Envoyer une copie &agrave; mon adresse email',
    'email_utenti_campi' => 'Remplissez les champs et s&egrave;lectionnez l&#39;email',
    'email_utenti_ok' => 'Email envoy&egrave; avec succ&egrave;s',
    'email_utenti_error' => 'Envoi email a &egrave;chou&egrave;',
    'attiva_utenti' => 'Activer les utilisateurs',
    'disattiva_utenti' => 'D&egrave;sactiver les utilisateurs',
    'cancella_file' => 'Supprimer les fichiers',
    'permessi_upload' => 'Autorisations t&egrave;l&egrave;chargement',
    'permessi_letture' => 'Autorisations lectures',
    'tutti_permessi' => 'Tous les autorisations',
    'nessun_permesso' => 'Aucun autorisation',
    'blocca_news' => 'Nouvelles bloquer',
    'invia_email' => 'Envoyer email',
    'oggetto' => 'Objet',
    'destinatari' => 'Les r&egrave;cipiendaires',
    'messaggio' => 'Message',
    'account_attivato' => 'Panneau des nouvelles activè',
    'ciao' => 'Bonjour',
    'corpo' => 'L\'administrateur vient de crèer votre nouveau compte pour ècrire les nouvelles. Connectez-vous à votre Panneau de configuration:',
    'firma_email' => 'Message envoy&egrave; par un administrateur des nouvelles',
    'nuovo_utente' => 'Entrez un nouvel utilisateur',
	'invia_accesso' => 'Envoyer des donnes d&apos;acc&egrave;s par email',

    /* PROFIL */
    'status_disattivo' => 'd&egrave;sactiv&egrave; - ',
    'user_autorizza_news' => 'Les nouvelles doivent &ecirc;tre approuv&egrave;s par un administrateur',
    'newsinserite' => 'Nouvelles &egrave;crites',
    'nomecognomedescr' => 'Sera affich&egrave; comme l&#39;auteur des nouvelles (obligatoire)',
    'emailnascosta' => 'Invisible &agrave; tous',
    'emaildescr' => 'Entrer une adresse email valide (obligatoire)',
    'pwd_attuale' => 'Mot de passe actuel',
    'new_pwd' => 'Nouveau mot de passe',
    'conferma_new_pwd' => 'Retaper le nouveau mot de passe',
    'passwordattdescr' => '&Egrave;crivez votre mot de passe actuel si vous voulez le changer',
    'sitowebdescr' => 'Inclus http:// ou https://',
    'interessidescr' => 'Limite de 255 caract&egrave;res:',
    'interessidescr2' => 'HTML, &Egrave;motic&ocirc;nes et BBCode d&egrave;sactiv&egrave;',
    'opzionidescr' => 'Choisissez la fa&ccedil;on de lier votre nom dans les nouvelles',
    'solonome' => 'Nom',
    'mostraemail' => 'Email',
    'mostrasito' => 'Site Web',
    'mostraprofilo' => 'Profil',
    'mostrafb' => 'Facebook',
    'mostratw' => 'Twitter',
	'mostraig' => 'Instagram',    
    'data_nascita' => 'Date de naissance',
    'nascitadescr' => 'jj/mm/aaaa',
    'lavoro' => 'Travaille',
    'citta' => 'Ville',
    'hobby' => 'Int&egrave;rets',
    'conta' => 'compter', 
    'autorizza_news' => 'Autorise Nouvelles',
    'canc_disatt' => 'Suppression et d&egrave;sactivation',
    'edit_prof_ok' => 'Profil mis &agrave; jour avec succ&egrave;s',
    'edit_prof_error' => 'Impossible de mettre &agrave; jour votre profil',
    'livello' => 'Niveau',
    'ultimo_accesso' => 'Derni&egrave;re connexion',
    'ultima' => 'Dernier:',
    'per_giorno' => 'par jour',
    'dettagli' => 'd&egrave;tails',
    'attenzione_utente' => 'AVERTISSEMENT: Les donn&egrave;s effac&egrave;s ne peuvent pas &ecirc;tre r&egrave;cup&egrave;r&egrave;s',
    'attenzione_news' => 'AVERTISSEMENT: Les nouvelles effac&egrave;s ne peuvent pas &ecirc;tre r&egrave;cup&egrave;r&egrave;s',
    'attenzione_news_cb' => 'Cochez la case pour confirmer',    
    'attenzione_file' => 'AVERTISSEMENT: Les fichiers supprim&egrave;s ne peuvent pas &ecirc;tre r&egrave;cup&egrave;r&egrave;s',
    'attenzione_utente_disattivato' => 'Si un utilisateur est d&egrave;sactiv&egrave; ne peut pas acc&egrave;der au syst&egrave;me',
    'attiva_account' => 'Activez le profil',
    'disattiva_account' => 'D&egrave;sactiver le profil',
    'cancella_utente' => 'Supprimer un utilisateur',
    'non_disponibile' => 'Indisponible',
    'modifica_letture' => 'Changer lectures',

    /* RECHERCHER E REMPLACER */
    'pagina_cerca' => 'Rechercher et remplacer',
    'cerca' => 'Rechercher',
    'sostituisci' => 'Remplacer',
    'scritte_da' => '&egrave;crite par',
    'da_tutti' => 'TOUS',
    'entrambi' => '&agrave; la fois',
    'ordina_per' => 'trier par',
    'settimana' => 'semaine derni&egrave;re',
    'mese' => 'mois derni&egrave;re',
    'anno' => 'ann&egrave; derni&egrave;re',
    'sempre' => 'toujours',
    'titoli_az' => 'titres (A-Z)',
    'categoria_az' => 'cat&egrave;gorie (A-Z)',
    'piu_recenti' => 'les plus r&egrave;centes',
    'piu_lette' => 'les plus consult&egrave;s',
    'pertinenza' => 'pertinence',
    'max_min_chars' => 'Le mot doit avoir au moins 4 caract&egrave;res',
    'max_min_chars_tag' => '&Eacute;tiquette doit avoir au moins 2 caract&egrave;res',
    'no_results' => 'D&egrave;sol&egrave;, aucun document trouv&egrave;',
    'no_results_google' => 'Essayez de rechercher avec Google',
    'campi_sost_vuoti' => 'Entrez terme de recherche et un autre &agrave; &ecirc;tre remplac&egrave;',
    'sostituzione_ok' => 'Nouvelles affect&egrave;e par le remplacement:',
	'sostituzione_tag_ok' => '&Eacute;tiquette remplac&egrave;e',    
	'sostituzione_commenti_ok' => 'Commentaires affect&egrave;e par le remplacement:',	
    'sostituzione_errore' => 'Aucune substitution fait',
    'risultati' => 'de r&egrave;sultats pour le mot',
    'commenti_trovati' => 'commentaires qui contiennent le mot',
    'commento' => 'Commentaire',

    /* ERREURS */
    'pwd_no_corr' => 'Le mot de passe ne correspond pas',
    'pwd_att' => 'Aussi &egrave;crire le mot de passe actuel',
    'pwd_min_chars' => 'Le mot de passe doit &ecirc;tre sup&egrave;rieure &agrave; 7 caract&egrave;res',
    'pwd_new' => 'Aussi &egrave;crire le nouveau mot de passe',
    'pwd_new_diverse' => 'Les nouveaux mots de passe ne correspondent pas',
    'required' => 'Champs obligatoire',
    'wrong_email' => 'Email invalide',
    'wrong_date' => 'Le format de date n&#39;est pas valide',
    'facebook_errato' => 'Nom Facebook invalid',
    'twitter_errato' => 'Nom Twitter invalid',
	'instagram_errato' => 'Nom Instagram invalid',
    'wrong_file' => 'Le type de fichier est invalide',
    'big_file' => 'Le fichier est trop volumineux',
    'utente_cancellato' => 'L&#39;utilisateur a &egrave;t&egrave; supprim&egrave;',
    'canc_user_error' => 'Erreur de suppression d&#39;utilisateur',
    'utente_disattivato' => 'L&#39;utilisateur a &egrave;t&egrave; d&egrave;sactiv&egrave;',
    'utente_attivato' => 'L&#39;utilisateur a &egrave;t&egrave; activ&egrave;',
    'attiva_user_error' => 'Erreur dans l&#39;activation de l&#39;utilisateur',
    'disatt_user_error' => 'Erreur dans la d&egrave;sactivation d&#39;utilisateur',
    'anteprima' => 'Pour avoir une avant-premi&egrave;re, vous devez remplir champ de texte',
    'capslock' => 'Caps Lock activ&egrave;',
    'antispam_error' => 'Code anti-spam incorrecte',

    /* FICHIERS ENVOYES */
    'file' => 'Fichiers',
    'file_inviati' => 'Les fichiers envoy&egrave;s par',
    'file_cancellato' => 'Le fichier a &egrave;t&egrave; supprim&egrave;',
    'file_cancellati' => 'Les fichiers ont &egrave;t&egrave; supprim&egrave;s',
    'permessi_non_validi' => 'AVERTISSEMENT: Les autorisations non valides pour le r&egrave;pertoire',
    'canc_dir_files_ok' => 'Les fichiers et r&egrave;pertoires supprim&egrave;s avec succ&egrave;s',
    'upload_ok' => 'Le fichier a &egrave;t&egrave; envoy&egrave;',
    'insert_file' => 'ins&egrave;rer',
    'data_file' => 'Date de d&egrave;pôt',
    'select' => 'S&egrave;lectionner:',
    'select_all' => 'tous',
    'select_none' => 'aucun',
    'delete' => 'Supprime',

    /* PARAMETRES */
    'impostazioni_pagina' => 'Param&egrave;tres',
    'avanti' => 'Suivant',
    'indietro' => 'Pr&egrave;c&egrave;dent',
    'totale' => 'Total:',
    'solo_numeri' => 'Vous devez saisir uniquement des chiffres',
    'conf_updated' => 'Param&egrave;tres correctement mis &agrave; jour',
    'conf_problem' => 'Erreur dans mise &agrave; jour',
    'nome_url' => 'Nom et adresse du site Web',
    'nome_sito' => 'Nom de site Web',
    'url_sito' => 'URL site',
    'archivio_notizie' => 'Nouvelles archives',
    'lettere1' => 'Vous montre les premi&egrave;res',
    'lettere2' => 'lettres du texte',  
    'ricerche_commenti' => 'Recherches, commentaires, &eacute;tiquettes',  
    'commenti_per_pagina' => 'Commentaires par page',
    'ultime_notizie' => 'Derni&egrave;res nouvelles',    
    'url_sito_descr' => 'URL avec http:// ou https:// et sans / finale',
    'per_page' => 'Nombre de nouvelles par page',
    'per_page_search' => 'Les r&egrave;sultats par page',
    'commenti_per_page' => 'Commentaires par page',
	'tags_per_page' => '&Eacute;tiquettes par page',        
    'img_nuova_news' => 'Image pour les derni&egrave;re nouvelle',
    'admin_mostra_record' => 'Administration: les enregistrements affich&egrave;s',
    'formato_data' => 'Format date et heure',
    'dim_upload_allegati' => 'Taille du t&egrave;l&egrave;charger et pi&egrave;ces jointes orphelins',
    'nome_livelli' => 'Nom des niveaux',
    'nome_livelli_descr' => 'Administrateur et utilisateur',
    'info_backup' => 'Information et sauvegarde',
    'dimensione' => 'Taille',
    'dimensione_totale' => 'Taille total',
    'backup_compresso' => 'Comprim&egrave; de sauvegarde',
    'backup_save' => 'Copier dans ',
	'backup_download' => 'D&egrave;charge', 
	'backup_save_error' => 'Impossible d&apos;enregistrer le fichier de sauvegarde',
	'backup_query_error' => 'Erreur lors de la sauvegarde de la base de donn&egrave;es',	
	'backup_file_canc' => '&Egrave;limine le fichier de sauvegarde',	
    'struttura_tabella' => 'Structure de la table',
    'dati_tabella' => 'Copie des valeurs de la table',
    'moderazione_commenti' => 'Mod&egrave;ration des commentaires',
	'disattivazione_commenti' => 'D&egrave;sactivation commentaires',        
    'max_include' => 'Nouvelles affich&egrave;s',
    'nuova_news_day' => 'L&#39;image doit &ecirc;tre de nouvelles nouvelles pendant des jours',
    'ip_white_list' => 'IP d&egrave;verrouill&egrave;',
    'ip_white_list_descr' => 'IPs qui n\'ont pas besoin d\'&ecirc;tre verrouill&ecirc;s en cas de connexion incorrecte',        
    'files_orfani' => 'Orphelins jointes',
    'files_orfani_descr' => 'Les fichiers supprim&egrave;s envoy&egrave;s par les utilisateurs',
    'files_orfani_descr2' => 'Nouvelles totale qui contiennent les fichiers: les supprimer, les liens vers ces fichiers ne seront plus valables.',
    'err_tabella' => 'S&egrave;lectionnez au moins une table',
    'in_eccesso' => 'Au-del&agrave; de',
    'ottimizza' => 'optimise',
    'optimized_ok' => 'Table optimis&egrave; avec succ&egrave;s',
    'optimized_error' => 'Impossible d&#39;optimiser la table',
    'time_gen' => 'Cette page a &egrave;t&egrave; g&egrave;n&egrave;r&egrave;e en'
);
?>
