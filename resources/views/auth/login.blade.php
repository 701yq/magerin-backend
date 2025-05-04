<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { border: 1px solid #ddd; padding: 30px; border-radius: 10px; }
    </style>
</head>
<body>
    <form method="POST" action="/login">
        @csrf
        <h3>Login Admin Mager-In</h3>

        @if($errors->any())
            <p style="color: red;">{{ $errors->first('message') }}</p>
        @endif

        <label>Email</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Masuk</button>
    </form>
</body>
</html>
