<section class="mdl-card mdl-shadow--2dp" id="user-panel">
    <div class="mdl-card__title mdl-color--indigo" style="height: 60px;">
        <h2 class="mdl-card__title-text mdl-color-text--white">{{ $utilisateur->getPrenom() }} {{ $utilisateur->getNom()[0] }}.</h2>
    </div>
    <div class="mdl-card__supporting-text">
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--amber mdl-color-text--white">{{ $statistiques['traiter'] }}</span>
            <span class="mdl-chip__text">Fiches à traiter</span>
        </span>
        <span class="mdl-chip mdl-chip--contact">
            <span class="mdl-chip__contact mdl-color--light-green mdl-color-text--white">{{ $statistiques['validees'] }}</span>
            <span class="mdl-chip__text">Fiches validées</span>
        </span>
    </div>
    <div class="mdl-card__actions mdl-card--border">
        <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="{{ route('rendus.index') }}">
            Gérer les rendus
        </a>
        <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="{{ route('utilisateurs.index') }}">
            Gérer les utilisateurs
        </a>
        @if ($utilisateur->isAdministrateur())
        <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="{{ route('fiches.index') }}">
            Gérer les fiches
        </a>
        <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="{{ route('compte-rendu') }}">
            Compte-rendu
        </a>
        @endif
    </div>
    <div class="mdl-card__menu">
        <a class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect mdl-color-text--white" href="{{ route('deconnexion') }}">
            <i class="material-icons">power_settings_new</i>
        </a>
    </div>
</section>