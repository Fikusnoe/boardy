<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Авторизация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h3>Запрос авторизации</h3>
            <p>Приложение <strong>{{ $client->name ?? 'Без названия' }}</strong> запрашивает доступ к вашему аккаунту.</p>

            	<form method="POST" action="{{ route('passport.authorizations.approve') }}">
	                @csrf
                	<input type="hidden" name="state" value="{{ request('state') }}">
                	<input type="hidden" name="client_id" value="{{ request('client_id') }}">
                	<input type="hidden" name="redirect_uri" value="{{ request('redirect_uri') }}">
                	<input type="hidden" name="response_type" value="{{ request('response_type') }}">
                	<input type="hidden" name="code_challenge" value="{{ request('code_challenge') }}">
                	<input type="hidden" name="code_challenge_method" value="{{ request('code_challenge_method') }}">
                	<input type="hidden" name="scope" value="{{ request('scope') }}">

                	<input type="hidden" name="auth_token_request" value="{{ $authToken }}">

                	<button type="submit" class="btn btn-success">Разрешить</button>
            	</form>

            <br>

            <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="state" value="{{ request('state') }}">
                <input type="hidden" name="client_id" value="{{ $client->id ?? request('client_id') }}">
                <button type="submit" class="btn btn-danger">Запретить</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
