<section class="mdl-card mdl-shadow--2dp" id="user-panel">
    <div class="mdl-card__title mdl-color--indigo" style="height: 60px;">
        <h2 class="mdl-card__title-text mdl-color-text--white">{{ $utilisateur->getPrenom() }} {{ $utilisateur->getNom()[0] }}.</h2>
    </div>
    <div class="mdl-card__supporting-text">
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--amber mdl-color-text--white">{{ $statistiques['accessibles'] }}</span>
            <span class="mdl-chip__text">Fiches accessibles</span>
        </span>
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--cyan mdl-color-text--white">{{ $statistiques['rendues'] }}</span>
            <span class="mdl-chip__text">Fiches rendues</span>
        </span>
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--red mdl-color-text--white">{{ $statistiques['refusees'] }}</span>
            <span class="mdl-chip__text">Fiches refusées</span>
        </span>
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--light-green mdl-color-text--white">{{ $statistiques['validees'] }}</span>
            <span class="mdl-chip__text">Fiches validées</span>
        </span>
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--lime mdl-color-text--white">{{ $statistiques['miniProjets'] }}</span>
            <span class="mdl-chip__text">Mini-projets validés</span>
        </span>
    </div>
    <div class="mdl-card__supporting-text" style="border-top: 1px solid #eee;">
        Vous pouvez double-cliquer sur une fiche accessible ou refusée pour y répondre.
    </div>
    <div class="mdl-card__menu">
        <a class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect mdl-color-text--white" href="{{ route('deconnexion') }}">
            <i class="material-icons">power_settings_new</i>
        </a>
    </div>
</section>