<?php

class Trad {

		# Mots

	const W_SECONDE = 'seconde';
	const W_MINUTE = 'minute';
	const W_HOUR = 'heure';
	const W_DAY = 'jour';
	const W_WEEK = 'semaine';
	const W_MONTH = 'mois';
	const W_YEAR = 'année';
	const W_DECADE = 'décennie';
	const W_SECONDE_P = 'secondes';
	const W_MINUTE_P = 'minutes';
	const W_HOUR_P = 'heures';
	const W_DAY_P = 'jours';
	const W_WEEK_P = 'semaines';
	const W_MONTH_P = 'mois';
	const W_YEAR_P = 'années';
	const W_DECADE_P = 'décennies';
	const W_BACK = 'Retour';
	const W_DATE = 'Date : ';
	const W_DESC = 'Synopsis : ';
	const W_MORE = 'lire…';
	const W_ACTIVATED = 'Activé';
	const W_DESACTIVATED = 'Désactivé';

	const W_SEASON_NB = 'Saison %nb%';

		# Phrases

	const S_AGO = 'il y a %duration% %pediod%';
	const S_NOTFOUND = 'La page que vous recherchez n\'existe pas…';

	const S_NO_EPISODE = 'Aucun épisode en attente…';

		# Verbes

	const V_LOGIN = 'Se connecter';
	const V_CONTINUE = 'Continuer';
	const V_SAVE = 'Enregistrer';
	const V_ADD = 'Ajouter';
	const V_SEARCH = 'Rechercher';

		# Forms

	const F_USERNAME = 'Nom d\'utilisateur :';
	const F_PASSWORD = 'Mot de passe :';
	const F_COOKIE = 'Type de connexion :';
	const F_COOKIE_FALSE = 'Ordinateur public';
	const F_COOKIE_TRUE = 'Ordinateur privé (rester connecté)';
	const F_URL = 'URL :';
	const F_URL_REWRITING = 'URL rewriting :';
	const F_LANGUAGE = 'Langue :';
	const F_ADDIC7ED = 'Addic7ed :';
	const F_NAME = 'Nom :';
	const F_DOWNLOAD = 'Téléchargement :';
	const F_TORRENT_DIR = 'Dossier des torrents :';
	const F_APIKEY = 'Clé API TheTVDB :';

	const F_SHOWNAME = 'Nom de la série :';

	const F_TIP_PASSWORD = 'Laissez vide pour ne pas le changer.';
	const F_TIP_URL_REWRITING = 'Laissez vide pour désactiver l\'URL rewriting. Sinon, indiquez le chemin du dossier de Goofy Goose (en commençant et terminant par un "/") par rapport au nom de domaine.';

		# Titres

	const T_404 = 'Erreur 404 – Page non trouvée';
	const T_LOGIN = 'Connexion';
	const T_LOGOUT = 'Déconnexion';
	const T_INSTALLATION = 'Installation';
	const T_SETTINGS = 'Préférences';
	const T_GLOBAL_SETTINGS = 'Réglages généraux';
	const T_USER_SETTINGS = 'Utilisateur';
	const T_HOME = 'À voir';
	const T_ADD = 'Ajouter une série';
	const T_SEARCH_RESULT = 'Résultats pour « %showname% »';
	const T_SHOWS = 'Séries';
	const T_DATA = 'Données';
	const T_SOON = 'Bientôt…';
	const T_TORRENT = 'Torrent';
	const T_SUBTITLES = 'Sous-titres';
	const T_INFOS = 'Infos';
	const T_OPTIONS = 'Options';

		# Alertes

	const A_ERROR_LOGIN = 'Mauvais nom d\'utilisateur ou mot de passe.';
	const A_ERROR_LOGIN_WAIT = 'Merci de patienter %duration% %period% avant de réessayer. Ceci est une protection contre les attaques malveillantes.';
	const A_ERROR_FORM = 'Merci de remplir tous les champs.';
	const A_ERROR_AJAX = 'Une erreur est survenue. Merci de réessayer.';
	const A_ERROR_AJAX_LOGIN = 'Vous êtes déconnecté. Raffraichissez la page, connectez-vous, puis vous pourrez réessayer.';

	const A_ERROR_NOSHOW = 'Aucune série n\'a été trouvée.';
	const A_ERROR_ADD = 'Impossible d\'ajouter la série. Vérifiez votre clé API de TheTVDB.';
	const A_ERROR_NETWORK = 'Impossible de récupérer le fichier distant.';

	const A_SUCCESS_INSTALL = 'Goofy Goose est maintenant correctement installé. Connectez-vous pour commencer à l\'utiliser.';
	const A_SUCCESS_SETTINGS = 'Les préférences ont bien été enregistrées.';

	const A_SUCCESS_ADD = 'La série a bien été ajoutée.';
	const A_SUCCESS_UPDATE = 'La série a bien été mise à jour.';

	public static $settings = array(
		'validate_url' => 'L\'url n\'est pas valide.'
	);

	public static $days = array(
		0 => 'Dimanche',
		1 => 'Lundi',
		2 => 'Mardi',
		3 => 'Mercredi',
		4 => 'Jeudi',
		5 => 'Vendredi',
		6 => 'Samedi'
	);

}

?>