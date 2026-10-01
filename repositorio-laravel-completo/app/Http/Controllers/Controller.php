<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function propio($modelo): void
    {
        abort_unless($modelo->id_usuario == Auth::id(), 403);
    }

    /** Aborta con 404 si el registro está en la papelera. */
    protected function vivo($modelo): void
    {
        abort_if($modelo->eliminado, 404);
    }
}
