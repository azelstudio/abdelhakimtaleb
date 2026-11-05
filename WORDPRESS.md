# Installation WordPress

Le fichier à téléverser dans **Apparence → Thèmes → Ajouter un thème → Téléverser un thème** est :

[**Télécharger hakim-taleb-wordpress.zip**](https://github.com/azelstudio/abdelhakimtaleb/raw/refs/heads/main/dist/hakim-taleb-wordpress.zip)

Utiliser ce ZIP directement : le ZIP « Code → Download ZIP » du dépôt contient aussi les sources et ne constitue pas le paquet installable du thème.

Faire une sauvegarde de l’ancien WordPress avant activation. Le thème crée ses neuf pages et configure l’accueil ; les anciennes pages ne sont pas supprimées. Les liens, images, polices et scripts utilisent les URL du thème et de WordPress.

Le visuel repose sur les pages fixes de la version validée. Elles ne sont pas converties en blocs Gutenberg : les retouches de contenu se font dans les fichiers du thème. Les intégrations externes Calendly et Google Maps sont conservées.

Les sources sont dans `wordpress/hakim-taleb/`. Pour actualiser les fichiers du site dans le thème et reconstruire l’archive :

```sh
python3 tools/build-wordpress.py
```

Les fichiers HTML/CSS à la racine restent la source de la version statique. Le script les copie dans `wordpress/hakim-taleb/site/`, puis crée `dist/hakim-taleb-wordpress.zip`.

Les détails et les précautions de mise en ligne sont dans [LISEZ-MOI.txt](wordpress/hakim-taleb/LISEZ-MOI.txt).

## Vérifications réalisées

Installation du ZIP final avec le mécanisme `Theme_Upgrader` de WordPress 6.8.3 / PHP 8.3, activation sur une nouvelle requête, neuf pages créées, accueil affecté, anciens contenus conservés et absence de doublons après réexécution. Retour aux réglages d’accueil précédents lors du changement de thème vérifié.

Les neuf pages ont été ouvertes dans Chromium à 390 et 1440 px : réponses HTTP correctes, ressources locales présentes, absence d’erreur JavaScript, fenêtre kinésiologie et retour à l’accueil fonctionnels. Les 18 captures WordPress sont identiques aux captures HTML de référence. Ces vérifications ont été effectuées avec les permaliens simples, puis dans un sous-dossier avec des permaliens personnalisés.

Les services externes ont été neutralisés pour les comparaisons visuelles ; les URL Calendly et Google Maps existantes sont conservées, mais leur fonctionnement distant n’est pas couvert par ces tests. La compatibilité avec les extensions et la configuration de l’hébergement de production reste à vérifier sur une copie de ce WordPress.
