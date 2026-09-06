<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="referrer" content="no-referrer">
    <title>Menghubungkan ke CBT</title>
</head>
<body>
    <p>Menghubungkan ke CBT…</p>
    <form id="sso-redirect" method="POST" action="{{ $targetUrl }}">
        <input type="hidden" name="token" value="{{ $token }}">
    </form>
    <script>document.getElementById('sso-redirect').submit();</script>
</body>
</html>
