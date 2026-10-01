<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, DriveController, PapeleraController, CompartirController};

Route::get('/', fn() => redirect()->route('drive'));
Route::get('/p/{token}', [CompartirController::class, 'publico'])->name('publico');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'formRegistro'])->name('registro');
    Route::post('/registro', [AuthController::class, 'registrar']);
    Route::get('/login', [AuthController::class, 'formLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/drive/{carpeta?}', [DriveController::class, 'index'])->name('drive');
    Route::post('/carpetas', [DriveController::class, 'crearCarpeta'])->name('carpetas.crear');
    Route::put('/carpetas/{carpeta}', [DriveController::class, 'renombrarCarpeta'])->name('carpetas.renombrar');
    Route::delete('/carpetas/{carpeta}', [DriveController::class, 'eliminarCarpeta'])->name('carpetas.eliminar');

    Route::post('/archivos', [DriveController::class, 'subir'])->name('archivos.subir');
    Route::get('/archivos/{archivo}/descargar', [DriveController::class, 'descargar'])->name('archivos.descargar');
    Route::post('/archivos/{archivo}/reemplazar', [DriveController::class, 'reemplazar'])->name('archivos.reemplazar');
    Route::patch('/archivos/{archivo}/mover', [DriveController::class, 'mover'])->name('archivos.mover');
    Route::delete('/archivos/{archivo}', [DriveController::class, 'eliminarArchivo'])->name('archivos.eliminar');

    Route::get('/papelera', [PapeleraController::class, 'index'])->name('papelera');
    Route::post('/papelera/carpetas/{carpeta}/restaurar', [PapeleraController::class, 'restaurarCarpeta'])->name('papelera.carpeta.restaurar');
    Route::post('/papelera/archivos/{archivo}/restaurar', [PapeleraController::class, 'restaurarArchivo'])->name('papelera.archivo.restaurar');
    Route::delete('/papelera/carpetas/{carpeta}', [PapeleraController::class, 'borrarCarpeta'])->name('papelera.carpeta.borrar');
    Route::delete('/papelera/archivos/{archivo}', [PapeleraController::class, 'borrarArchivo'])->name('papelera.archivo.borrar');

    Route::get('/compartidos', [CompartirController::class, 'compartidosConmigo'])->name('compartidos');
    Route::post('/archivos/{archivo}/compartir', [CompartirController::class, 'compartir'])->name('compartir');
    Route::delete('/comparticiones/{comparticion}', [CompartirController::class, 'revocar'])->name('compartir.revocar');
    Route::post('/archivos/{archivo}/enlace', [CompartirController::class, 'crearEnlace'])->name('enlace.crear');
    Route::delete('/enlaces/{enlace}', [CompartirController::class, 'revocarEnlace'])->name('enlace.revocar');
});
