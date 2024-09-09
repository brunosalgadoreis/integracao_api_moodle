<!DOCTYPE html>
<html>

<head>
    <title>Ações SEED</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <div class="container border border-dark rounded mt-1 pb-2">
        <h1>Aluno</h1>
        @include('navbar')
        <form action="{{ route('aluno.action') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="date">Data (dd-mm-aaaa):</label>
                <input type="text" id="date" name="date" class="form-control" required pattern="\d{2}-\d{2}-\d{4}"
                    placeholder="dd-mm-aaaa">
            </div>

            <!-- <div id="additional-inputs" style="display: none;">
                <div class="form-group">
                    <label for="date">Data Fim (dd-mm-aaaa):</label>
                    <input type="text" id="extra-input" name="extra_input" class="form-control" required pattern="\d{2}-\d{2}-\d{4}"
                        placeholder="dd-mm-aaaa">
                </div>
            </div> -->

            <div class="form-group">
                <label for="action">Ação:</label>
                <select id="action" name="action" class="form-control" required>
                    <option value="" selected="selected">Escolha uma ação</option>
                    <option value="get">Get Aluno Data</option>
                    <option value="get2" disabled>Get Aluno De/Até</option>
                    <option value="import" disabled>Import Aluno Data</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <div class="d-flex justify-content-center mt-2">
            <div id="loading-spinner" class="spinner-border" role="status" style="display: none;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        @isset($response)
            <div class="mt-4">
                {!! $response !!}
            </div>
        @endisset

        @isset($html)
            <div class="mt-4">
                {!! $html !!}
            </div>
        @endisset
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form");
            const spinner = document.getElementById("loading-spinner");

            form.addEventListener("submit", function () {
                spinner.style.display = "block";
            });

            window.addEventListener("load", function () {
                spinner.style.display = "none";
            });
        });
    </script>
    
    <!-- <script>
        document.getElementById('action').addEventListener('change', function () {
            var additionalInputs = document.getElementById('additional-inputs');
            if (this.value === 'get2') {
                additionalInputs.style.display = 'block';
            } else {
                additionalInputs.style.display = 'none';
            }
        });

    </script> -->
</body>

</html>