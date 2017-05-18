@extends('layouts.app')

@section('title', 'Compte-rendu')

@section('content')
    <section class="mdl-card mdl-shadow--2dp" id="fiche-submit">
        <div class="mdl-card__title mdl-color--indigo">
            <h2 class="mdl-card__title-text mdl-color-text--white">Compte-rendu</h2>
        </div>
        <div class="mdl-card__supporting-text">
            <h4>Statistiques globales</h4>
            <div style="display: flex; justify-content: space-between;">
                <canvas id="evolution-chart" style="max-width: 500px;"></canvas>
                <canvas id="participation-chart" style="max-width: 500px;"></canvas>
            </div>

            <h4>Statistiques par groupes</h4>
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                <thead>
                    <tr>
                        <th class="mdl-data-table__cell--non-numeric">Chargé de groupe</th>
                        <th>Nombre d'élèves du groupe</th>
                        <th>Nombre de fiches validées</th>
                        <th>Nombre moyen de fiches validées</th>
                        <th>Nombre médian de fiches validées</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupes as $groupe)
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric">{{ $groupe['responsable'] }}</td>
                        <td>{{ $groupe['eleves'] }}</td>
                        <td>{{ $groupe['fiches'] }}</td>
                        <td>{{ $groupe['moyenne'] }}</td>
                        <td>{{ $groupe['mediane'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h4>Statistiques par utilisateur</h4>
            <p>Pour chaque langage, on précise le nombre de fiches rendues et le nombre de fiches validées entre parenthèses.</p>
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                <thead>
                    <tr>
                        <th class="mdl-data-table__cell--non-numeric">Utilisateur</th>
                        <th class="mdl-data-table__cell--non-numeric">Responsable</th>
                        @foreach ($langages as $langage)
                        <th>{{ $langage->getTitre() }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($utilisateurs as $utilisateur)
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric">{{ $utilisateur->getNomComplet() }}</td>
                        @if ($utilisateur->getResponsable())
                        <td class="mdl-data-table__cell--non-numeric">{{ $utilisateur->getResponsable()->getNomComplet() }}</td>
                        @else
                        <td class="mdl-data-table__cell--non-numeric">Aucun responsable</td>
                        @endif
                        @foreach ($langages as $langage)
                        <td>{{ count($utilisateur->getRendusForLangage($langage)) }} ({{ count($utilisateur->getRendusValidesForLangage($langage)) }})</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mdl-card__menu">
            <a class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect mdl-color-text--white" href="{{ route('index') }}">
                <i class="material-icons">clear</i>
            </a>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://unpkg.com/chart.js/dist/Chart.min.js"></script>
<script>
    function getRandomColor(opacity) {
        opacity = opacity || 1;

        var color = 'rgba(';
        for (var i = 0; i < 3; i++) {
            color += Math.floor(Math.random() * 255) + ', ';
        }
        color += opacity + ')';

        return color;
    }

    var evolutionContext = document.getElementById('evolution-chart');
    var evolutionData = {
        labels: ['2016'],
        datasets: [
            {
                label: 'Nombre total de fiches validées',
                data: [{!! json_encode($totalValides) !!}],
                backgroundColor: [
                    getRandomColor(.7)
                ]
            }
        ]
    };
    var evolutionChart = new Chart(evolutionContext, {
        type: 'bar',
        data: evolutionData
    });

    var participationContext = document.getElementById('participation-chart');
    var participationData = {
        labels: [],
        datasets: [{ data: [], backgroundColor: [] }]
    };

    @foreach ($groupes as $groupe)
    participationData.labels.push({!! json_encode($groupe['responsable']) !!});
    participationData.datasets[0].data.push({!! json_encode($groupe['fiches']) !!});
    participationData.datasets[0].backgroundColor.push(getRandomColor(.7));
    @endforeach

    var participationChart = new Chart(participationContext, {
        type: 'pie',
        data: participationData
    });
</script>
@endpush