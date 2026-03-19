<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 | {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: dark light; }
        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 20% 20%, rgba(99,102,241,0.18), transparent 36%),
                radial-gradient(circle at 80% 15%, rgba(236,72,153,0.13), transparent 34%),
                linear-gradient(to right, rgba(99,102,241,0.12) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(236,72,153,0.10) 1px, transparent 1px),
                #0a0a0a;
            background-size: auto, auto, 48px 48px, 48px 48px, auto;
            color: #f5f5f5;
        }
        .card {
            width: min(680px, calc(100vw - 2rem));
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 2rem;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
        }
        .kicker { font-size: 12px; opacity: .7; letter-spacing: .08em; text-transform: uppercase; }
        h1 { margin: .4rem 0 0; font-size: clamp(2rem, 5vw, 3.4rem); line-height: 1.05; }
        p { margin-top: .8rem; opacity: .84; }
        a {
            display: inline-block;
            margin-top: 1rem;
            border: 1px solid rgba(255,255,255,0.18);
            padding: .55rem .85rem;
            border-radius: 12px;
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="kicker">500 Server Error</div>
        <h1>Something broke on our side.</h1>
        <p>Try refreshing the page in a moment, or head back home.</p>
        <a href="{{ url('/') }}">Go home</a>
    </main>
</body>
</html>

