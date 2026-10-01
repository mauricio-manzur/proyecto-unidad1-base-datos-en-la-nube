@extends('layout')
@section('contenido')
<h4>Papelera</h4>
<ul class="list-group">
@forelse($carpetas as $c)
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span>📁 {{ $c->nombre }} <small class="text-muted">eliminada {{ $c->fecha_eliminacion }}</small></span>
    <div class="d-flex gap-2">
      <form method="post" action="{{ route('papelera.carpeta.restaurar', $c) }}">@csrf<button class="btn btn-sm btn-success">Restaurar</button></form>
      <form method="post" action="{{ route('papelera.carpeta.borrar', $c) }}" onsubmit="return confirm('Eliminación definitiva')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Eliminar definitivamente</button></form>
    </div></li>
@empty @endforelse
@forelse($archivos as $a)
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span>📄 {{ $a->nombre_original }} <small class="text-muted">eliminado {{ $a->fecha_eliminacion }}</small></span>
    <div class="d-flex gap-2">
      <form method="post" action="{{ route('papelera.archivo.restaurar', $a) }}">@csrf<button class="btn btn-sm btn-success">Restaurar</button></form>
      <form method="post" action="{{ route('papelera.archivo.borrar', $a) }}" onsubmit="return confirm('Eliminación definitiva')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Eliminar definitivamente</button></form>
    </div></li>
@empty @endforelse
@if($carpetas->isEmpty() && $archivos->isEmpty())<li class="list-group-item text-muted">La papelera está vacía</li>@endif
</ul>
@endsection
