<!DOCTYPE html>
<html>

<head>
    <title>Ações SEED</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css'
        integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
</head>

<div>

    <div class="container border border-dark rounded mt-1 pb-2">
        <h1>SEED</h1>
        @include('navbar')

        <button type="button" class="btn btn-secondary" disabled>Ultimo GET Professor:
            {{$professorMaisRecente->data}}</button>
        <button type="button" class="btn btn-secondary" disabled>Ultimo GET Aluno: {{$alunoMaisRecente->data}}</button>
        <hr>
        <form action="{{ route('search.professor') }}" method="POST">
            @csrf
            <label for="username">Professor Username:</label>
            <input type="text" name="username" id="username" required>
            <button type="submit">Buscar</button>
        </form>
        <form action="{{ route('search.aluno') }}" method="POST">
            @csrf
            <label for="username">Aluno Username:</label>
            <input type="text" name="username" id="username" required>
            <button type="submit">Buscar</button>
        </form>


        @isset($users)
            <div class="mt-4">
                {!! $users !!}
            </div>
        @endisset

        @isset($html)
            <div class="mt-4">
                {!! $html !!}
            </div>
        @endisset
    </div>


    </body>

</html>