<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Richiesta di registrazione su {{Config::get('authentication::app_name')}}</h2>
<div>
    <strong>La richiesta di registrazione è stata inoltrata con successo. Un moderatore validerà i dati da te inseriti</strong>
    <br/>
    <strong>Riepilogo dati: </strong>
    <ul>
        <li>Username: {{$body['email']}}</li>
        <li>Password: {{$body['password']}}</li>
    </ul>
</div>
</body>
</html>