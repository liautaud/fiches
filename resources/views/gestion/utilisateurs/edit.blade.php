@extends('layouts.app')

@if ($ajout)
    @section('title', 'Ajout d\'un utilisateur')
@else
    @section('title', 'Modification d\'un utilisateur')
@endif

@section('content')
@if ($ajout)
<form action="{{ route('utilisateurs.store') }}" method="post" class="wide-form">
@else
<form action="{{ route('utilisateurs.update', $courant->getId()) }}" method="post" class="wide-form">
@endif
    <section class="mdl-card mdl-shadow--2dp" id="fiche-submit">
        <div class="mdl-card__title mdl-color--indigo">
            @if ($ajout)
            <h2 class="mdl-card__title-text mdl-color-text--white">Ajout d'un utilisateur</h2>
            @else
            <h2 class="mdl-card__title-text mdl-color-text--white">Modification de l'utilisateur {{ $courant->getNomComplet() }}</h2>
            @endif
        </div>
        <div class="mdl-card__supporting-text">
            <p>Lorsque l'utilisateur sera ajouté, un e-mail lui sera envoyé avec un lien lui permettant de terminer son inscription en choisissant son mot de passe.</p>
            <h4>Informations sur l'utilisateur</h4>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="nom" name="nom" value="{{ $courant->getNom() }}">
                <label class="mdl-textfield__label" for="nom">Nom de l'utilisateur</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="prenom" name="prenom" value="{{ $courant->getPrenom() }}">
                <label class="mdl-textfield__label" for="prenom">Prénom de l'utilisateur</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="email" name="email" value="{{ $courant->getEmail() }}">
                <label class="mdl-textfield__label" for="email">Adresse e-mail de l'utilisateur</label>
            </div>
            <div class="mdl-textfield mdl-select">
                <select name="statut" id="statut">
                    <option value="0"{{ ($courant->getStatut() == 0) ? ' selected' : '' }}>Elève</option>
                    <option value="1"{{ ($courant->getStatut() == 1) ? ' selected' : '' }}>Chargé de groupe</option>
                    @if ($utilisateur->isAdministrateur())
                    <option value="2"{{ ($courant->getStatut() == 2) ? ' selected' : '' }}>Administrateur</option>
                    @endif
                </select>
            </div>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="integer" id="promotion" name="promotion" value="{{ $courant->getPromotion() }}">
                <label class="mdl-textfield__label" for="promotion">Promotion de l'utilisateur</label>
            </div>

            @if ($utilisateur->isAdministrateur())
            <h4 style="margin-top: 50px;">Responsable de l'utilisateur</h4>
            <div class="mdl-textfield mdl-select">
                <select name="responsable" id="responsable">
                    <option value="0">Aucun responsable</option>
                    @foreach ($responsables as $responsable)
                    <option
                            value="{{ $responsable->getId() }}"
                            {{ ($courant->getResponsable() && $courant->getResponsable()->getId() == $responsable->getId()) ? ' selected' : '' }}>
                        {{ $responsable->getNomComplet() }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="responsable" value="{{ auth()->user()->getId() }}" />
            @endif
        </div>
        <div class="mdl-card__actions mdl-card--border mdl-textfield--align-right">
            {{ csrf_field() }}

            @if ($ajout)
                <button type="submit" class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect">
                    Ajouter l'utilisateur
                    <i class="material-icons">add</i>
                </button>
            @else
                {{ method_field('PUT') }}
                <button type="submit" class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect">
                    Enregistrer l'utilisateur
                    <i class="material-icons">save</i>
                </button>
            @endif
        </div>
        <div class="mdl-card__menu">
            <a class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect mdl-color-text--white" href="{{ route('index') }}">
                <i class="material-icons">clear</i>
            </a>
        </div>
    </section>
</form>
@endsection