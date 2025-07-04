@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Test API UPNVJ</div>
            <div class="card-body">
                <form id="apiForm">
                    <div class="mb-3">
                        <label>NIM/NIP/NIK</label>
                        <input type="text" class="form-control" name="identifier" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Uji API</button>
                </form>

                <div class="mt-4">
                    <h5>Hasil:</h5>
                    <pre id="result" style="background:#f5f5f5; padding:15px; border-radius:5px;"></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('apiForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const identifier = document.querySelector('input[name="identifier"]').value;
            const password = document.querySelector('input[name="password"]').value;

            fetch('/test-api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        identifier: identifier,
                        password: password
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('result').textContent = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    document.getElementById('result').textContent = 'Error: ' + error;
                });
        });
    </script>
@endsection
