@extends('layout')
@section('contenido')
<div class="card mx-auto" style="max-width:420px"><div class="card-body">
<h4>Registro</h4>
<form method="post" action="{{ route('registro') }}" class="vstack gap-3">@csrf
  <input class="form-control" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required>
  <input class="form-control" type="email" name="correo" placeholder="Correo" value="{{ old('correo') }}" required>
  <input class="form-control" type="password" name="password" placeholder="Contraseña (mín. 8)" required>
  <input class="form-control" type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
  <button class="btn btn-success">Registrarme</button>
  <a href="{{ route('login') }}">Ya tengo cuenta</a>
</form></div></div>
@endsection
