@extends('layout')
@section('contenido')
<div class="card mx-auto" style="max-width:420px"><div class="card-body">
<h4>Iniciar sesión</h4>
<form method="post" action="{{ route('login') }}" class="vstack gap-3">@csrf
  <input class="form-control" type="email" name="correo" placeholder="Correo" value="{{ old('correo') }}" required>
  <input class="form-control" type="password" name="password" placeholder="Contraseña" required>
  <button class="btn btn-primary">Entrar</button>
  <a href="{{ route('registro') }}">Crear cuenta</a>
</form></div></div>
@endsection
