@extends('legal.layout')
@section('title', 'Politique de Confidentialité (Traitement RGPD)')
@section('content')
<p>Nous savons que les données de votre entreprise, vos chantiers et l'identité de vos clients sont confidentielles. Postelio s'engage à une transparence totale sur la manière dont vos flux de travail sont traités et respecte rigoureusement le Règlement Général européen sur la Protection des Données (RGPD).</p>

<h2>1. Données directement collectées</h2>
<p>La structuration et le bon fonctionnement de l'application Postelio nécessitent l'enregistrement des données suivantes :</p>
<ul>
    <li><strong>Données de base du compte :</strong> Nom, Prénom, Raison Sociale, Email professionnel, et votre mot de passe (qui est chiffré mathématiquement, Postelio ne connait pas votre mot de passe "en clair").</li>
    <li><strong>Données de transaction :</strong> La gestion pécuniaire et de contrôle de la fraude est exclusivement prise en charge en marque blanche par <em>Stripe</em>. Postelio ne collecte et ne stocke <strong>aucune</strong> coordonnée de carte bancaire sur ses propres serveurs, pour une protection et une traçabilité inviolables de votre moyen de règlement.</li>
    <li><strong>Jetons de synchronisation sociale (OAuth) :</strong> Lors de la connexion à vos réseaux professionnels, Facebook/Instagram renvoient un Access Token (Jeton d'accès de l'API Meta). Ces jetons sont chiffrés directement dans nos bases de données via la clef secrète de l'application et n'ont qu'une seule vocation absolue : valider vos demandes légitimes de publication sur <em>vos</em> réseaux de façon centralisée. L'application ne "lit" pas et ne s'immisce dans aucun de vos messages ou historique personnels sur le réseau social.</li>
    <li><strong>Ressources brutes (Médias et Audios) :</strong> L'application stocke provisoirement vos photos et la note vocale que vous transmettez (le fichier audio compressé de votre micro).</li>
</ul>

<h2>2. Transfert sécurisé aux technologies de génération (OpenAI, Anthropic)</h2>
<p>L'innovation principale de Postelio consiste à transcrire le son en texte puis formater le sujet via nos algorithmes de génération.<br>
Vos pistes vocales, de même que le transcript brut, communiquent avec le modèle de génération en l'interrogeant par <strong>API (Machine-à-Machine)</strong>. 
Conformément aux directives Entreprise (B2B) des principaux moteurs du marché, <strong>vos contenus professionnels et vocaux injectés dans l'API de façon automatisée ne sont jamais réutilisés ou indexés afin d'entraîner le modèle global public de ces technologies.</strong> Vos chantiers restent vos chantiers, aucune donnée d'entraînement ne viendra fuiter votre savoir faire technique.</p>

<h2>3. Vos Droits d'Accès et à Oubli (Droit de Rectification)</h2>
<p>Postelio ne vous soumettra à aucun traitement purement publicitaire avec des entreprises-tiers ou du "Démarchage Crossover". L'adresse email récoltée à l'abonnement sera exclusivement utilisée pour des actions comptables, fonctionnelles ou informatives de sécurité impérieuses.</p>
<p>Sous la protection directe du RGPD et LIL, vous détenez le droit discrétionnaire inaliénable de demander accès, portabilité claire, mise en pause ou la suppression (droit à l’oubli) de vos métadonnées informatiques sur demande à l'équipe technique à <strong>hello@postelio.fr</strong>, le tout validé par l'adresse d'origine du créateur du compte.</p>

<h2>4. Durée de conservation informatique</h2>
<ul>
    <li>En cas d'annulation de l'abonnement, le compte restera présent pour vous simplifier une future résurgence. Les posts publiés seront quant à eux archivés dans un but statistique pour votre entreprise.</li>
    <li>De par une opération de <strong>Suppression de Compte Totale</strong> depuis les configurations ou par demande explicite au support métier, la totalité des jetons Meta (OAuth) et le panel exhaustif complet de vos images uploads brutes de chantier, sont écrasées dans une fenêtre légale de 7 jours logicielles et ne laisseront subsister aucune trace résiliente du fichier, exceptions des registres de journalisation obligatoires de facturation par obligation de la DIRECCTE.</li>
</ul>

<h2>5. Gestion des Cookies</h2>
<p>Notre philosophie vise à concevoir l'application la moins intrusive et fluide possible. Postelio applique 0 Tracking et implémente uniquement des Cookies d'infrastructures fonctionnels qui vous reconnaissent entre deux recharges de fenêtres et pour des impératifs liés aux authentifications de session (Token CSRF).</p>
@endsection
