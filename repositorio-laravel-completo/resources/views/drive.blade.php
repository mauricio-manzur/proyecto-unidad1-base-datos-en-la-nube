@extends('layout')
@section('contenido')
<nav class="mb-3"><a href="{{ route('drive') }}">Raíz</a>
@foreach($ruta as $c) / <a href="{{ route('drive', $c) }}">{{ $c->nombre }}</a>@endforeach</nav>

<div class="row g-3 mb-4">
  <form class="col-md-5 d-flex gap-2" method="post" action="{{ route('carpetas.crear') }}">@csrf
    <input type="hidden" name="id_carpeta_padre" value="{{ $carpeta?->id_carpeta }}">
    <input class="form-control" name="nombre" placeholder="Nueva carpeta" required>
    <button class="btn btn-secondary">Crear</button>
  </form>
  <form class="col-md-7 d-flex gap-2" method="post" action="{{ route('archivos.subir') }}" enctype="multipart/form-data">@csrf
    <input type="hidden" name="id_carpeta" value="{{ $carpeta?->id_carpeta }}">
    <input class="form-control" type="file" name="archivos[]" multiple required>
    <button class="btn btn-primary">Subir</button>
  </form>
</div>

<h5>Carpetas</h5>
<ul class="list-group mb-4">
@forelse($carpetas as $c)
  <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
    <a href="{{ route('drive', $c) }}">📁 {{ $c->nombre }}</a>
    <div class="d-flex gap-2">
      <form method="post" action="{{ route('carpetas.renombrar', $c) }}" class="d-flex gap-1">@csrf @method('PUT')
        <input class="form-control form-control-sm" name="nombre" value="{{ $c->nombre }}">
        <button class="btn btn-sm btn-outline-secondary">Renombrar</button></form>
      <form method="post" action="{{ route('carpetas.eliminar', $c) }}" onsubmit="return confirm('Se enviará la carpeta y su contenido a la papelera')">@csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
    </div>
  </li>
@empty<li class="list-group-item text-muted">Sin subcarpetas</li>@endforelse
</ul>

<h5>Archivos</h5>
@forelse($archivos as $a)
@php $enlace = $a->enlaces->where('activo', 1)->first(fn($e) => !$e->fecha_expiracion || $e->fecha_expiracion->isFuture()); @endphp
<div class="card mb-2"><div class="card-body">
  <div class="d-flex justify-content-between">
    <div>📄 <strong>{{ $a->nombre_original }}</strong> <small class="text-muted">{{ number_format($a->tamano_bytes/1024, 1) }} KB</small></div>
    <div class="d-flex gap-2">
      <a class="btn btn-sm btn-outline-primary" href="{{ route('archivos.descargar', $a) }}">Descargar</a>
      <form method="post" action="{{ route('archivos.eliminar', $a) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
    </div>
  </div>
  <details class="mt-2"><summary>Compartir / enlace público / mover</summary>
    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <strong>Compartir con usuario</strong>
        <form method="post" action="{{ route('compartir', $a) }}" class="d-flex gap-1 my-1">@csrf
          <input class="form-control form-control-sm" type="email" name="correo" placeholder="correo@ejemplo.com" required>
          <select class="form-select form-select-sm" name="permiso" style="width:auto"><option value="lectura">Lectura</option><option value="escritura">Escritura</option></select>
          <button class="btn btn-sm btn-success">Compartir</button></form>
        @foreach($a->comparticiones as $s)
          <form method="post" action="{{ route('compartir.revocar', $s) }}" class="d-flex justify-content-between small">@csrf @method('DELETE')
            <span>{{ $s->destino->correo }} ({{ $s->permiso }})</span><button class="btn btn-link btn-sm text-danger p-0">Revocar</button></form>
        @endforeach
      </div>
      <div class="col-md-6">
        <strong>Enlace público</strong>
        @if($enlace)
          <input class="form-control form-control-sm my-1" readonly value="{{ route('publico', $enlace->token) }}" onclick="this.select()">
          <form method="post" action="{{ route('enlace.revocar', $enlace) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Desactivar enlace</button></form>
        @else
          <form method="post" action="{{ route('enlace.crear', $a) }}" class="my-1">@csrf<button class="btn btn-sm btn-outline-success">Generar enlace</button></form>
        @endif
        <form method="post" action="{{ route('archivos.mover', $a) }}" class="d-flex gap-1 mt-2">@csrf @method('PATCH')
          <input class="form-control form-control-sm" name="id_carpeta" placeholder="ID de carpeta destino (vacío = raíz)">
          <button class="btn btn-sm btn-outline-secondary">Mover</button></form>
      </div>
    </div>
  </details>
</div></div>
@empty<p class="text-muted">Sin archivos en esta carpeta</p>@endforelse
@endsection
