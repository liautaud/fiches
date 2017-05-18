@extends('layouts.app')

@section('title', 'Récapitulatif')

@section('content')
    <section id="fiches-graph"></section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.16.1/vis.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.16.1/vis.min.js"></script>
<script src="{{ asset('js/graph.js') }}"></script>
<script>
    var isAdmin = {!! json_encode($utilisateur->isAdministrateur()) !!};
    var graphData = {!! json_encode($graphe) !!};

    window.onload = function () {
        var graph = new Graph(document.getElementById('fiches-graph'), graphData, isAdmin);
        graph.draw();
    };
</script>
@endpush