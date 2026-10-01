<?php
namespace App\Http\Controllers;
use App\Models\{Archivo, Carpeta};
use Illuminate\Support\Facades\{Auth, DB, Storage};

class PapeleraController extends Controller
{
    /** Vista inversa: eliminado = 1. Solo muestra la "raíz" de cada eliminación. */
    public function index()
    {
        $uid = Auth::id();
        $carpetas = Carpeta::with('padre')->where('id_usuario', $uid)->where('eliminado', 1)->get()
            ->filter(fn($c) => !$c->padre || !$c->padre->eliminado || $c->padre->fecha_eliminacion != $c->fecha_eliminacion);
        $archivos = Archivo::with('carpeta')->where('id_usuario', $uid)->where('eliminado', 1)->get()
            ->filter(fn($a) => !$a->carpeta || !$a->carpeta->eliminado || $a->carpeta->fecha_eliminacion != $a->fecha_eliminacion);
        return view('papelera', compact('carpetas', 'archivos'));
    }

    public function restaurarCarpeta(Carpeta $carpeta)
    {
        $this->propio($carpeta);
        abort_unless($carpeta->eliminado, 400);
        $f = $carpeta->fecha_eliminacion;
        $ids = $carpeta->descendientesIds();
        $datos = ['eliminado' => 0, 'fecha_eliminacion' => null];
        // Solo restaura lo que se eliminó junto con la carpeta (misma fecha)
        Carpeta::whereIn('id_carpeta', $ids)->where('fecha_eliminacion', $f)->update($datos);
        Archivo::whereIn('id_carpeta', $ids)->where('fecha_eliminacion', $f)->update($datos);
        if ($carpeta->id_carpeta_padre && Carpeta::find($carpeta->id_carpeta_padre)?->eliminado) {
            Carpeta::where('id_carpeta', $carpeta->id_carpeta)->update(['id_carpeta_padre' => null]);
        }
        return back()->with('ok', 'Carpeta restaurada');
    }

    public function restaurarArchivo(Archivo $archivo)
    {
        $this->propio($archivo);
        abort_unless($archivo->eliminado, 400);
        $datos = ['eliminado' => 0, 'fecha_eliminacion' => null];
        if ($archivo->id_carpeta && Carpeta::find($archivo->id_carpeta)?->eliminado) $datos['id_carpeta'] = null;
        $archivo->update($datos);
        return back()->with('ok', 'Archivo restaurado');
    }

    public function borrarArchivo(Archivo $archivo)
    {
        $this->propio($archivo);
        abort_unless($archivo->eliminado, 400);
        Storage::disk('s3')->delete($archivo->s3_key); // borra el objeto real
        $archivo->delete();                            // y el registro (cascade a compartidos/enlaces)
        return back()->with('ok', 'Archivo eliminado definitivamente');
    }

    public function borrarCarpeta(Carpeta $carpeta)
    {
        $this->propio($carpeta);
        abort_unless($carpeta->eliminado, 400);
        $ids = $carpeta->descendientesIds();
        DB::transaction(function () use ($ids) {
            // Cualquier archivo vivo que haya quedado dentro se rescata a la raíz (no se borra de S3).
            Archivo::whereIn('id_carpeta', $ids)->where('eliminado', 0)->update(['id_carpeta' => null]);
            foreach (Archivo::whereIn('id_carpeta', $ids)->where('eliminado', 1)->get() as $a) {
                Storage::disk('s3')->delete($a->s3_key);
                $a->delete();
            }
            foreach (array_reverse($ids) as $id) Carpeta::where('id_carpeta', $id)->delete(); // hijos primero (FK RESTRICT)
        });
        return back()->with('ok', 'Carpeta eliminada definitivamente');
    }
}
