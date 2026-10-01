<?php
namespace App\Http\Controllers;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class AuthController extends Controller
{
    public function formRegistro() { return view('auth.registro'); }
    public function formLogin() { return view('auth.login'); }

    public function registrar(Request $r)
    {
        $d = $r->validate([
            'nombre' => 'required|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo',
            'password' => 'required|min:8|confirmed',
        ]);
        $u = Usuario::create([
            'nombre' => $d['nombre'], 'correo' => $d['correo'],
            'password_hash' => Hash::make($d['password']), // bcrypt
        ]);
        Auth::login($u);
        $r->session()->regenerate();
        return redirect()->route('drive');
    }

    public function login(Request $r)
    {
        $c = $r->validate(['correo' => 'required|email', 'password' => 'required']);
        if (Auth::attempt($c)) {
            $r->session()->regenerate();
            return redirect()->intended(route('drive'));
        }
        return back()->withErrors(['correo' => 'Credenciales incorrectas'])->onlyInput('correo');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
