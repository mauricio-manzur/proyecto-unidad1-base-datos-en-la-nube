<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Repositorio de archivos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4"><div class="container">
  <a class="navbar-brand" href="{{ route('drive') }}">📁 Mi repositorio</a>
  @auth
  <div class="d-flex gap-3 align-items-center">
    <a class="text-white" href="{{ route('drive') }}">Mis archivos</a>
    <a class="text-white" href="{{ route('compartidos') }}">Compartidos conmigo</a>
    <a class="text-white" href="{{ route('papelera') }}">🗑 Papelera</a>
    <span class="text-secondary">{{ auth()->user()->nombre }}</span>
    <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-light">Salir</button></form>
  </div>
  @endauth
</div></nav>
<div class="container pb-5">
  @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  @yield('contenido')
</div></body></html>
