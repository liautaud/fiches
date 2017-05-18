@extends('layouts.app')

@section('title', 'Gestion des fiches')

@section('content')
<section class="mdl-card mdl-shadow--2dp" id="fiche-submit">
    <div class="mdl-card__title mdl-color--indigo">
        <h2 class="mdl-card__title-text mdl-color-text--white">Gestion des fiches</h2>
        <select data-filter-by="langage" data-target="#fiches-list">
            <option value="0">Filtrer par langage</option>
            @foreach($langages as $langage)
            <option value="{{ $langage->getId() }}">{{ $langage->getTitre() }}</option>
            @endforeach
        </select>
    </div>
    <div class="mdl-card__supporting-text paddingless" id="fiches-list">
        <table class="mdl-data-table mdl-js-data-table mdl-data-table--selectable inline-table">
            <thead>
            <tr>
                <th class="mdl-data-table__cell--non-numeric">Titre de la fiche</th>
                <th>Nombre d'exercices</th>
                <th>Nombre de rendus</th>
                <th>Nombre de rendus validés</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($fiches as $fiche)
            <tr data-supprimer-url="{{ route('fiches.destroy', $fiche->getId()) }}" data-supprimer-method="DELETE" data-langage="{{ $fiche->getLangage()->getId() }}">
                <td class="mdl-data-table__cell--non-numeric">{{ $fiche->getTitre() }}</td>
                <td>{{ count($fiche->getExercices()) }}</td>
                <td>{{ count($fiche->getRendus()) }}</td>
                <td>{{ count($fiche->getRendusValides()) }}</td>
                <td>
                    <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('fiches.edit', $fiche->getId()) }}">
                        Modifier la fiche
                        <i class="material-icons">edit</i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mdl-card__actions mdl-card--border mdl-textfield--align-right">
        <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" data-action="supprimer" data-target="#fiches-list">
            Supprimer toutes les fiches sélectionnées
            <i class="material-icons">delete</i>
        </a>
        <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('fiches.create') }}">
            Ajouter une fiche
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
<script src="{{ asset('js/filterable.js') }}"></script>
@endpush