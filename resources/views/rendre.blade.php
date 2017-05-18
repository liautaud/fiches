@extends('layouts.app')

@section('title', 'Rendre une fiche')

@section('content')
<form action="{{ route('rendre', $fiche->getId()) }}" method="post" enctype="multipart/form-data">
    <section class="mdl-card mdl-shadow--2dp" id="fiche-submit">
        <div class="mdl-card__title mdl-color--indigo">
            <h2 class="mdl-card__title-text mdl-color-text--white">
                @if ($peutSoumettre)
                Rendre la fiche {{ $fiche->getTitre() }}
                @elseif ($afficherNomPrenom)
                Consulter le rendu de la fiche {{ $fiche->getTitre() }} par {{ $dernierRendu->getUtilisateur()->getNomComplet() }}
                @else
                Consulter son rendu de la fiche {{ $fiche->getTitre() }}
                @endif
            </h2>
        </div>
        <div class="mdl-card__supporting-text">
            @foreach ($fiche->getExercices() as $exercice)
            <h4>
                # Exercice {{ $fiche->getTitre() }}.{{ $exercice->getNumero() }}
                @if ($peutSoumettre)
                <file-uploader 
                    name="fichiers[{{ $exercice->getNumero() }}]"
                    :numero="{{ $exercice->getNumero() }}"
                    @if ($dernierRendu && $dernierRendu->getReponse($exercice->getNumero()) && $dernierRendu->getReponse($exercice->getNumero())->getChemin())
                    :begin-empty="false"
                    @else
                    :begin-empty="true"
                    @endif></file-uploader>
                @elseif ($dernierRendu && $dernierRendu->getReponse($exercice->getNumero()) && $dernierRendu->getReponse($exercice->getNumero())->getChemin())
                <small>
                    <a href="{{ route('piece-jointe', $dernierRendu->getReponse($exercice->getNumero())->getId()) }}">Voir la pièce jointe</a>
                </small>
                @endif
            </h4>
            @if ($exercice->getType() == 'texte')
            <markdown-editor
                initial="{{ json_encode(($dernierRendu) ? $dernierRendu->getReponse($exercice->getNumero())->getContenu() : '') }}"
                @if (!$peutSoumettre) disabled @endif
                name="reponses[{{ $exercice->getNumero() }}]"
                :numero="{{ $exercice->getNumero() }}"></markdown-editor>
            @else
            <code-editor
                initial="{{ json_encode(($dernierRendu) ? $dernierRendu->getReponse($exercice->getNumero())->getContenu() : '') }}"
                lang="{{ $exercice->getType() }}"
                @if (!$peutSoumettre) disabled @endif
                name="reponses[{{ $exercice->getNumero() }}]"
                :numero="{{ $exercice->getNumero() }}"></code-editor>
            @endif
            @endforeach
        </div>
        <div class="mdl-card__actions mdl-card--border mdl-textfield--align-right">
            {{ csrf_field() }}

            <!--<a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" onclick="bus.$emit('fake-content')">
                Aucun contenu
                <i class="material-icons">flag</i>
            </a>-->
            @if ($fiche->getCheminGitComplementaires())
            <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('complementaires', $fiche->getId()) }}" target="_blank">
                Télécharger les compléments
                <i class="material-icons">file_download</i>
            </a>
            @endif
            <a class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect" href="{{ route('telecharger', $fiche->getId()) }}" target="_blank">
                Télécharger la fiche
                <i class="material-icons">file_download</i>
            </a>
            @if ($peutSoumettre)
            <button type="submit" name="sauvegarde" class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect">
                Sauvegarder les réponses
                <i class="material-icons">save</i>
            </button>
            <button type="submit" name="validation" class="mdl-button mdl-button--colored mdl-button--raised mdl-js-button mdl-js-ripple-effect">
                Envoyer les réponses
                <i class="material-icons">send</i>
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

@push('scripts')
<script src="https://unpkg.com/vue/dist/vue.min.js"></script>
<script src="https://unpkg.com/marked/marked.min.js"></script>
<script src="https://unpkg.com/ace-builds/src-min-noconflict/ace.js"></script>
<script src="{{ asset('js/rendre.js') }}"></script>
@endpush