<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Подтверждение авторизации</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h3>Запрос авторизации</h3>
            <p>Приложение <strong>{{ $client->name }}</strong> запрашивает доступ к вашему аккаунту.</p>
            
            <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                @csrf
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <button type="submit" class="btn btn-success">Разрешить</button>
            </form>
            
            <form method="POST" action="{{ route('passport.authorizations.deny') }}" class="mt-2">
                @csrf
                @method('DELETE')
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <button type="submit" class="btn btn-danger">Запретить</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
