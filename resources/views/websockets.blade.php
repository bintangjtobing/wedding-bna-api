<!DOCTYPE html>
<html>

<head>
    <title>WebSockets Dashboard</title>
</head>

<body>
    <h1>WebSockets Dashboard</h1>
    <p>Buka console developer untuk melihat koneksi WebSocket.</p>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        window.Echo.channel('messages')
            .listen('.new-message', (e) => {
                console.log('New message received:', e);
            });
    </script>
</body>

</html>