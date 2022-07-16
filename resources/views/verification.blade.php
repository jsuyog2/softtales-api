<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<style>
    html,
    body {
        height: 100%;
    }

    .container {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .message{
        padding: 20px;
        border: 2px solid black;
        background-color: rgba(	173,255,47,0.5);
    }

</style>

<body>
    <div class="container">
        <div class="message">
            <h1>{{ $message }}</h1>
        </div>
      </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
