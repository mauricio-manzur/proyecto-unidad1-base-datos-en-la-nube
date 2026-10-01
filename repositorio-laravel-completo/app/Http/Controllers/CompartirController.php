<?php
namespace App\Http\Controllers;
use App\Models\{Archivo, Compartido, Enlace, Usuario};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};
use Illuminate\Support\Str;

class CompartirController extends Controller
{
    public function compartidosConmigo()
    {
        $items = Compartido::with('archivo.propietario')->where('id_usuario_destino', Auth::id())
            ->whereHas('archivo', fn($q) => $q->where('eliminado', 0))->get();
        return view('compartidos', compact('items'));
    }

    public function compartir(Request $r, Archivo $archivo)
    {
        $this->propio($archivo);
        $this->vivo($archivo);
        $d = $r->validate(['correo' => 'required|email|exists:usuarios,correo', 'permiso' => 'required|in:lectura,escritura']);
        $dest = Usuario::where('correo', $d['correo'])->first();
        if ($dest->id_usuario == Auth::id()) return back()->withErrors(['correo' => 'No puedes compartir contigo mismo']);
        Compartido::updateOrCreate(
            ['id_archivo' => $archivo->id_archivo, 'id_usuario_destino' => $dest->id_usuario],
            ['permiso' => $d['permiso']]
        );
        return back()->with('ok', 'Archivo compartido');
    }

    public function revocar(Compartido $comparticion)
    {
        $this->propio($comparticion->archivo);
        $comparticion->delete();
        return back()->with('ok', 'Compartición revocada');
    }

    public function crearEnlace(Archivo $archivo)
    {
        $this->propio($archivo);
        $this->vivo($archivo);
        if (Enlace::where('id_archivo', $archivo->id_archivo)->where('activo', 1)->exists()) {
            return back()->with('ok', 'Ya existe un enlace público activo');
        }
        Enlace::create(['id_archivo' => $archivo->id_archivo, 'token' => Str::random(48), 'activo' => 1]);
        return back()->with('ok', 'Enlace público generado');
    }

    public function revocarEnlace(Enlace $enlace)
    {
        $this->propio($enlace->archivo);
        $enlace->update(['activo' => 0, 'revocado_en' => now()]);
        return back()->with('ok', 'Enlace desactivado');
    }

    /** Acceso SIN sesión */
    public function publico(string $token)
    {
        $e = Enlace::with('archivo')->where('token', $token)->where('activo', 1)
            ->where(fn($q) => $q->whereNull('fecha_expiracion')->orWhere('fecha_expiracion', '>', now()))->firstOrFail();
        abort_if($e->archivo->eliminado, 404);
        return Storage::disk('s3')->download($e->archivo->s3_key, $e->archivo->nombre_original);
    }
}
