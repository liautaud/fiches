@extends('layouts.app')

@if ($ajout)
    @section('title', 'Ajout d\'une fiche')
@else
    @section('title', 'Modification d\'une fiche')
@endif

@section('content')
@if ($ajout)
<form action="{{ route('fiches.store') }}" method="post" class="wide-form">
@else
<form action="{{ route('fiches.update', $fiche->getId()) }}" method="post" class="wide-form">
@endif
    <section class="mdl-card mdl-shadow--2dp" id="fiche-submit">
        <div class="mdl-card__title mdl-color--indigo">
            @if ($ajout)
            <h2 class="mdl-card__title-text mdl-color-text--white">Ajout d'une fiche</h2>
            @else
            <h2 class="mdl-card__title-text mdl-color-text--white">Modification de la fiche {{ $fiche->getTitre() }}</h2>
            @endif
        </div>
        <div class="mdl-card__supporting-text">
            <h4>Informations sur la fiche</h4>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="titre" name="titre" value="{{ $fiche->getTitre() }}">
                <label class="mdl-textfield__label" for="titre">Titre de la fiche</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="langage" name="langage" value="{{ ($fiche->getLangage()) ? $fiche->getLangage()->getTitre() : '' }}">
                <label class="mdl-textfield__label" for="langage">Langage de la fiche</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="chemin" name="chemin" value="{{ $fiche->getCheminGit() }}">
                <label class="mdl-textfield__label" for="chemin">Chemin de la fiche sur le serveur Git</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="chemin-complementaires" name="chemin-complementaires" value="{{ $fiche->getCheminGitComplementaires() }}">
                <label class="mdl-textfield__label" for="chemin-complementaires">Chemin des fichiers complémentaires sur le serveur Git</label>
            </div>
            <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" style="margin-top: 20px;" for="mini-projet">
                <input type="checkbox" id="mini-projet" class="mdl-checkbox__input" name="mini-projet"{{ ($fiche->isMiniProjet()) ? ' checked' : '' }} />
                <span class="mdl-checkbox__label">La fiche est un mini-projet</span>
            </label>

            <h4 style="margin-top: 60px;">Exercices de la fiche</h4>
            <p>Pour ajouter un exercice, il suffit de commencer à saisir le type d'exercice dans le champ ci-dessous <i>(par exemple Texte, HTML, ...)</i>.<br />
            Les exercices seront numérotés automatiquement selon l'ordre dans lequel ils apparaissent dans le champ.</p>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="exercices" name="exercices">
            </div>

            <h4>Dépendances de la fiche</h4>
            <p>Pour ajouter des dépendances, il suffit de commencer à saisir le titre de la fiche dans le champ ci-dessous.</p>
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input" type="text" id="dependances" name="dependances">
            </div>
        </div>
        <div class="mdl-card__actions mdl-card--border mdl-textfield--align-right">
            {{ csrf_field() }}

            @if ($ajout)
            <button type="submit" class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect">
                Ajouter la fiche
                <i class="material-icons">add</i>
            </button>
            @else
            {{ method_field('PUT') }}
            <button type="submit" class="mdl-button mdl-button--primary mdl-js-button mdl-js-ripple-effect">
                Enregistrer la fiche
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

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/bootstrap-tagsinput@0.7.1/src/bootstrap-tagsinput-typeahead.css" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/0.11.1/typeahead.bundle.min.js"></script>
<script src="https://unpkg.com/bootstrap-tagsinput@0.7.1/src/bootstrap-tagsinput.js"></script>
<script>
    var EXERCICES_PREFILL = {!! json_encode($exercices) !!};
    var DEPENDANCES_PREFILL = {!! json_encode($dependances) !!};
</script>
<script>
    $(function () {
        // Gestion du titre et du langage de la fiche
        $('#titre').keyup(function () {
            var titre = $(this).val().toUpperCase();
            var langage = titre.match(/[a-zA-Z]*/)[0];
            var langageInput = $('#langage');

            $(this).val(titre);
            langageInput.val(langage);

            if (langage != '') {
                langageInput.parent().addClass('is-dirty');
            } else {
                langageInput.parent().removeClass('is-dirty');
            }
        });

        // Gestion des exercices de la fiche
        var availableModes =
                ['abap', 'abc', 'actionscript', 'ada', 'apache_conf', 'applescript', 'asciidoc', 'assembly_x86', 'autohotkey',
                 'batchfile', 'c9search', 'c_cpp', 'cirru', 'clojure', 'cobol', 'coffee', 'coldfusion', 'csharp', 'css', 'curly',
                 'd', 'dart', 'diff', 'django', 'dockerfile', 'dot', 'drools', 'eiffel', 'ejs', 'elixir', 'elm', 'erlang', 'forth',
                 'fortran', 'ftl', 'gcode', 'gherkin', 'gitignore', 'glsl', 'gobstones', 'golang', 'groovy', 'haml', 'handlebars',
                 'haskell', 'haskell_cabal', 'haxe', 'html', 'html_elixir', 'html_ruby', 'ini', 'io', 'jack', 'jade', 'java', 'javascript',
                 'json', 'jsoniq', 'jsp', 'jsx', 'julia', 'kotlin', 'latex', 'lean', 'less', 'liquid', 'lisp', 'live_script', 'livescript',
                 'logiql', 'lsl', 'lua', 'luapage', 'lucene', 'makefile', 'markdown', 'mask', 'matlab', 'mavens_mate_log', 'maze', 'mel',
                 'mips_assembler', 'mipsassembler', 'mushcode', 'mysql', 'nix', 'nsis', 'objectivec', 'ocaml', 'pascal', 'perl', 'pgsql',
                 'php', 'plain_text', 'powershell', 'praat', 'prolog', 'properties', 'protobuf', 'python', 'r', 'razor', 'rdoc', 'rhtml',
                 'rst', 'ruby', 'rust', 'sass', 'scad', 'scala', 'scheme', 'scss', 'sh', 'sjs', 'smarty', 'snippets', 'soy_template', 'space',
                 'sql', 'sqlserver', 'stylus', 'svg', 'swift', 'swig', 'tcl', 'tex', 'text', 'textile', 'toml', 'tsx', 'twig', 'typescript',
                 'vala', 'vbscript', 'velocity', 'verilog', 'vhdl', 'wollok', 'xml', 'xquery', 'yaml'];

        var exercicesTypes = availableModes.map(function (language) {
           return { 'value': language, 'text': 'Code ' + language.toUpperCase() };
        });

        exercicesTypes.push({ "value": 'texte' , "text": "Texte" });

        var exercicesSuggestions = new Bloodhound({
            local: exercicesTypes,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text')
        });

        $('#exercices').tagsinput({
            itemValue: 'value',
            itemText: 'text',
            allowDuplicates: true,
            typeaheadjs: {
                name: 'typesExercice',
                displayKey: 'text',
                source: exercicesSuggestions.ttAdapter()
            }
        });

        EXERCICES_PREFILL.map(function (exercice) {
            $('#exercices').tagsinput('add', exercice);
        });

        // Gestion des dépendances
        var fichesSuggestions = new Bloodhound({
            prefetch: {
                url: '{{ route('fiches-suggestions') }}',
                cache: false
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text')
        });

        $('#dependances').tagsinput({
            itemValue: 'value',
            itemText: 'text',
            typeaheadjs: {
                name: 'fiches',
                displayKey: 'text',
                source: fichesSuggestions.ttAdapter()
            }
        });

        DEPENDANCES_PREFILL.map(function (dependance) {
            $('#dependances').tagsinput('add', dependance);
        });
    });
</script>
@endpush