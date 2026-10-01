<?php
namespace App\Http\Controllers;
use App\Models\{Archivo, Carpeta};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};
use Illuminate\Support\Str;

class DriveController extends Controller
{
    /** Listado normal: SIEMPRE filtra eliminado = 0 */
    public function index(?Carpeta $carpeta = null)
    {
        $uid = Auth::id();
        if ($carpeta) { $this->propio($carpeta); abort_if($carpeta->eliminado, 404); }
        $id = $carpeta?->id_carpeta;

        $carpetas = Carpeta::where('id_usuario', $uid)->where('id_carpeta_padre', $id)
            ->where('eliminado', 0)->orderBy('nombre')->get();
        $archivos = Archivo::with(['comparticiones.destino', 'enlaces'])
            ->where('id_usuario', $uid)->where('id_carpeta', $id)
            ->where('eliminado', 0)->orderBy('nombre_original')->get();

        $ruta = []; $c = $carpeta;
        while ($c) { array_unshift($ruta, $c); $c = $c->padre; }

        return view('drive', compact('carpeta', 'carpetas', 'archivos', 'ruta'));
    }

    public function crearCarpeta(Request $r)
    {
        $d = $r->validate(['nombre' => 'required|max:150', 'id_carpeta_padre' => 'nullable|integer']);
        $padre = $d['id_carpeta_padre'] ?? null;
        if ($padre) { $p = Carpeta::findOrFail($padre); $this->propio($p); $this->vivo($p); }
        Carpeta::create(['id_usuario' => Auth::id(), 'nombre' => $d['nombre'],
            'id_carpeta_padre' => $padre ?: null]);
        return back()->with('ok', 'Carpeta creada');
    }

    public function renombrarCarpeta(Request $r, Carpeta $carpeta)
    {
        $this->propio($carpeta);
        $this->vivo($carpeta);
        $carpeta->update($r->validate(['nombre' => 'required|max:150']));
        return back()->with('ok', 'Carpeta renombrada');
    }

    /** Eliminación lógica EN CASCADA: subcarpetas y archivos pasan a la papelera con la misma fecha. */
    public function eliminarCarpeta(Carpeta $carpeta)
    {
        $this->propio($carpeta);
        $this->vivo($carpeta);
        $ids = $carpeta->descendientesIds();
        $ahora = now();
        $datos = ['eliminado' => 1, 'fecha_eliminacion' => $ahora];
        Carpeta::whereIn('id_carpeta', $ids)->where('eliminado', 0)->update($datos);
        Archivo::whereIn('id_carpeta', $ids)->where('eliminado', 0)->update($datos);
        return redirect()->route('drive', $carpeta->id_carpeta_padre)->with('ok', 'Carpeta enviada a la papelera');
    }

    public function subir(Request $r)
    {
        $r->validate(['archivos' => 'required|array', 'archivos.*' => 'file|max:51200', 'id_carpeta' => 'nullable|integer']);
        $uid = Auth::id();
        if ($r->id_carpeta) { $c = Carpeta::findOrFail($r->id_carpeta); $this->propio($c); $this->vivo($c); }
        foreach ($r->file('archivos') as $f) {
            $key = "usuarios/$uid/" . Str::uuid() . '.' . $f->getClientOriginalExtension();
            Storage::disk('s3')->put($key, fopen($f->getRealPath(), 'r'));
            Archivo::create([
                'id_usuario' => $uid, 'id_carpeta' => $r->id_carpeta ?: null,
                'nombre_original' => $f->getClientOriginalName(), 's3_key' => $key,
                's3_bucket' => config('filesystems.disks.s3.bucket'),
                'tamano_bytes' => $f->getSize(), 'mime_type' => $f->getClientMimeType() ?: 'application/octet-stream',
            ]);
        }
        return back()->with('ok', 'Archivo(s) subido(s)');
    }

    public function descargar(Archivo $archivo)
    {
        abort_if($archivo->eliminado, 404);
        abort_unless($archivo->permisoPara(Auth::id()), 403); // propietario o compartido
        return Storage::disk('s3')->download($archivo->s3_key, $archivo->nombre_original);
    }

    /** Solo propietario o permiso 'escritura': reemplaza el contenido del objeto en S3. */
    public function reemplazar(Request $r, Archivo $archivo)
    {
        $r->validate(['archivo' => 'required|file|max:51200']);
        $this->vivo($archivo);
        abort_unless(in_array($archivo->permisoPara(Auth::id()), ['propietario', 'escritura']), 403);
        $f = $r->file('archivo');
        Storage::disk('s3')->put($archivo->s3_key, fopen($f->getRealPath(), 'r'));
        $archivo->update(['tamano_bytes' => $f->getSize(), 'mime_type' => $f->getClientMimeType()]);
        return back()->with('ok', 'Archivo actualizado');
    }

    public function mover(Request $r, Archivo $archivo)
    {
        $this->propio($archivo);
        $this->vivo($archivo);
        $r->validate(['id_carpeta' => 'nullable|integer']);
        $dest = $r->input('id_carpeta') ?: null;
        if ($dest) { $c = Carpeta::findOrFail($dest); $this->propio($c); abort_if($c->eliminado, 404); }
        $archivo->update(['id_carpeta' => $dest]);
        return back()->with('ok', 'Archivo movido');
    }

    public function eliminarArchivo(Archivo $archivo)
    {
        $this->propio($archivo);
        $this->vivo($archivo);
        $archivo->update(['eliminado' => 1, 'fecha_eliminacion' => now()]);
        return back()->with('ok', 'Archivo enviado a la papelera');
    }
}
