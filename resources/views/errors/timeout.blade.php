<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiempo de espera agotado</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        h1 {
            color: #333;
            font-size: 28px;
            margin: 0 0 20px 0;
            font-weight: 600;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 30px 0;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .tips {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
            text-align: left;
        }

        .tips h2 {
            color: #333;
            font-size: 18px;
            margin: 0 0 15px 0;
        }

        .tips ul {
            color: #666;
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">⏱️</div>
        <h1>Tiempo de espera agotado</h1>
        <p>{{ $message ?? 'La operación está tardando más de lo esperado. Por favor, recarga la página e intenta nuevamente.' }}
        </p>
        <button class="btn" onclick="window.location.reload()">Recargar página</button>

        <div class="tips">
            <h2>💡 Sugerencias:</h2>
            <ul>
                <li>Verifica tu conexión a internet</li>
                <li>Si el problema persiste, espera unos minutos e intenta nuevamente</li>
                <li>Contacta al administrador si el problema continúa</li>
            </ul>
        </div>
    </div>
</body>

</html>
