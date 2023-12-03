<!DOCTYPE html>
<html class="h-full">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Foxhound</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Hedvig+Letters+Serif:opsz@12..24&family=Inconsolata&display=swap" rel="stylesheet">

    {{
        \Illuminate\Support\Facades\Vite::useHotFile(public_path('vendor/foxhound/hot'))
            ->useBuildDirectory('vendor/foxhound/build')
            ->withEntryPoints(['resources/css/app.css', 'resources/js/app.ts'])
    }}
</head>
<body class="antialiased h-full bg-gray-50 dark:bg-slate-900">
	<div id="app" class="min-h-full flex flex-col"></div>
</body>
</html>
