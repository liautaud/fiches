@extends('layouts.guest')

@section('title', 'Terminer son inscription')

@section('content')
<div class="mdl-layout mdl-js-layout">
    <form class="mdl-layout__content" method="post" action="#">
        <div class="mdl-card mdl-shadow--6dp">
            <div class="mdl-card__title mdl-color--indigo mdl-color-text--white">
                <h2 class="mdl-card__title-text">Inscription - PROJ1</h2>
            </div>
            <div class="mdl-card__supporting-text" style="border-bottom: 1px solid #eee; text-align: justify;">
                Afin de terminer votre inscription à l'interface de gestion des fiches de PROJ1, veuillez choisir un mot de passe pour le compte <i class="mdl-color-text--indigo">{{ $utilisateur->getEmail() }}</i>.
            </div>
            <div class="mdl-card__supporting-text">
                <div class="mdl-textfield mdl-js-textfield">
                    <input class="mdl-textfield__input" type="password" id="password" name="password" />
                    <label class="mdl-textfield__label" for="password">Mot de passe</label>
                </div>
                <div class="mdl-textfield mdl-js-textfield">
                    <input class="mdl-textfield__input" type="password" id="password-confirm" name="password-confirm" />
                    <label class="mdl-textfield__label" for="password-confirm">Confirmation du mot de passe</label>
                </div>
            </div>
            <div class="mdl-card__actions mdl-card--border">
                {{ csrf_field() }}
                <button class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" type="submit">Terminer son inscription</button>
            </div>
        </div>
    </form>
</div>

<p class="message">Pour toute suggestion ou pour signaler un bug, <br />merci d'envoyer un message à <i class="mdl-color-text--indigo">romain.liautaud@[...]</i>.</p>
@endsection
