@extends('layout')
@section('contenido')
<h4>Compartidos conmigo</h4>
<ul class="list-group">
@forelse($items as $i)
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span>📄 {{ $i->archivo->nombre_original }} <small class="text-muted">de {{ $i->archivo->propietario->nombre }} · {{ $i->permiso }}</small></span>
    <div class="d-flex gap-2 align-items-center">
      @if($i->permiso === 'escritura')
      <form method="post" action="{{ route('archivos.reemplazar', $i->archivo) }}" enctype="multipart/form-data" class="d-flex gap-1">@csrf
        <input class="form-control form-control-sm" type="file" name="archivo" required><button class="btn btn-sm btn-outline-secondary">Reemplazar</button></form>
      @endif
      <a class="btn btn-sm btn-outline-primary" href="{{ route('archivos.descargar', $i->archivo) }}">Descargar</a>
    </div></li>
@empty<li class="list-group-item text-muted">Nadie ha compartido archivos contigo</li>@endforelse
</ul>
@endsection
