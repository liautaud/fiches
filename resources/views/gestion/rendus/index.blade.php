@extends('layouts.app')

@section('title', 'Gestion des rendus')

@section('content')
<section class="mdl-card mdl-shadow--2dp" id="fiche-submit">
    <div class="mdl-card__title mdl-color--indigo">
        <h2 class="mdl-card__title-text mdl-color-text--white">Gestion des rendus</h2>
    </div>
    <div class="mdl-card__supporting-text paddingless" id="rendus-list">
        <table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable inline-table" id="rendus-liste">
            <thead>
            <tr>
                <th class="mdl-data-table__cell--non-numeric">Titre de la fiche</th>
                <th class="mdl-data-table__cell--non-numeric">Nom de l'élève</th>
                <th class="mdl-data-table__cell--non-numeric">Date du rendu</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($rendus as $rendu)
                <tr data-valider-url="{{ route('rendus.valider', $rendu->getId()) }}"
                    data-refuser-url="{{ route('rendus.refuser', $rendu->getId()) }}">
                    <td class="mdl-data-table__cell--non-numeric">{{ $rendu->getFiche()->getTitre()  }}</td>
                    <td class="mdl-data-table__cell--non-numeric">{{ $rendu->getUtilisateur()->getNomComplet() }}</td>
                    <td class="mdl-data-table__cell--non-numeric">{{ strftime('%c', $rendu->getDateCreation()->getTimestamp()) }}</td>
                    <td>
                        <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('rendus.telecharger', $rendu->getId()) }}">
                            Télécharger le rendu
                            <i class="material-icons">attach_file</i>
                        </a>
                        <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('rendus.afficher', $rendu->getId()) }}">
                            Voir le rendu
                            <i class="material-icons">visibility</i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mdl-card__actions mdl-card--border mdl-textfield--align-right">
        <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" data-action="refuser" data-target="#rendus-liste">
            Refuser tous les rendus sélectionnés
            <i class="material-icons">block</i>
        </a>
        <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" data-action="valider" data-target="#rendus-liste">
            Valider tous les rendus sélectionnés
            <i class="material-icons">done</i>
        </a>
    </div>
    <div class="mdl-card__menu">
        <a class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect mdl-color-text--white" href="{{ route('index') }}">
            <i class="material-icons">clear</i>
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="{{ asset('js/actionable.js') }}"></script>
@endpush