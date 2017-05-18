@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
    <section class="mdl-card mdl-shadow--2dp" id="fiche-submit">
        <div class="mdl-card__title mdl-color--indigo">
            <h2 class="mdl-card__title-text mdl-color-text--white">Gestion des utilisateurs</h2>
        </div>
        <div class="mdl-card__supporting-text paddingless" id="utilisateurs-list">
            <table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable inline-table">
                <thead>
                <tr>
                    <th class="mdl-data-table__cell--non-numeric">Nom</th>
                    <th class="mdl-data-table__cell--non-numeric">Prénom</th>
                    <th class="mdl-data-table__cell--non-numeric">Adresse e-mail</th>
                    <th class="mdl-data-table__cell--non-numeric">Reponsable</th>
                    <th>Fiches rendues</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($utilisateurs as $utilisateur)
                    <tr data-supprimer-url="{{ route('utilisateurs.destroy', $utilisateur->getId()) }}" data-supprimer-method="DELETE">
                        <td class="mdl-data-table__cell--non-numeric">{{ $utilisateur->getNom() }}</td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $utilisateur->getPrenom() }}</td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $utilisateur->getEmail() }}</td>
                        @if ($utilisateur->getResponsable())
                        <td class="mdl-data-table__cell--non-numeric">{{ $utilisateur->getResponsable()->getNomComplet() }}</td>
                        @else
                        <td class="mdl-data-table__cell--non-numeric">Aucun responsable</td>
                        @endif
                        <td>{{ count($utilisateur->getRendusSoumis()) }}</td>
                        <td>
                            <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('utilisateurs.edit', $utilisateur->getId()) }}">
                                Modifier l'utilisateur
                                <i class="material-icons">edit</i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mdl-card__actions mdl-card--border mdl-textfield--align-right">
            <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" data-action="supprimer" data-target="#utilisateurs-list">
                Supprimer tous les utilisateurs sélectionnés
                <i class="material-icons">delete</i>
            </a>
            <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('utilisateurs.create') }}">
                Ajouter un utilisateur
                <i class="material-icons">add</i>
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