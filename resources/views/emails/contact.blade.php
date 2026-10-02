<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensaje del CMS</title>
</head>
<body>
    <h1>Nuevo mensaje de contacto</h1>
    <p><strong>Nombre:</strong> {{ $senderName }}</p>
    <p><strong>Correo:</strong> {{ $senderEmail }}</p>
    <h2>Mensaje</h2>
    <p>{{ $contactMessage }}</p>
    <p style="color:#666;font-size:13px">El remitente del sobre SMTP es el CMS. Responda usando Reply-To.</p>
</body>
</html>
