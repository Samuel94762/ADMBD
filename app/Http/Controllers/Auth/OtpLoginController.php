<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\OtpCodeMail;
use App\Models\User;
use App\Models\CodigoOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class OtpLoginController extends Controller
{
    // Paso 1: Login inicial con email y password
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        // Validar bloqueo
        if ($user && $user->bloqueado_hasta && now()->lt($user->bloqueado_hasta)) {
            return back()->withErrors(['email' => 'Usuario bloqueado. Intenta después de 5 min.']);
        }

        // Credenciales incorrectas
        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($user) {
                $user->intentos_fallidos++;
                if ($user->intentos_fallidos >= 3) {
                    $user->bloqueado_hasta = now()->addMinutes(5);
                    $user->intentos_fallidos = 0;
                }
                $user->save();
            }
            return back()->withErrors(['email' => 'Credenciales incorrectas']);
        }

        // Resetear intentos fallidos
        $user->update(['intentos_fallidos' => 0]);

        // Generar OTP
        $codigo = rand(100000, 999999);
        CodigoOtp::create([
            'user_id' => $user->id,
            'codigo' => $codigo,
            'expira_en' => now()->addSeconds(60),
            'utilizado' => false
        ]);

        // Enviar OTP por correo (ahora con plantilla Markdown)
        Mail::to($user->email)->send(new OtpCodeMail($user, $codigo, 60));

        // Guardar en sesión
        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.form');
    }

    // Mostrar formulario OTP
    public function showOtpForm()
    {
        $userId = session('otp_user_id');

    if (!$userId) {
        return redirect()->route('login')->withErrors([
            'email' => 'Primero inicia sesión para generar un código OTP.',
        ]);
    }

    // Último OTP sin usar para el usuario
    $otp = \App\Models\CodigoOtp::where('user_id', $userId)
        ->where('utilizado', false)
        ->latest() // por created_at
        ->first();

    if (!$otp) {
        return redirect()->route('login')->withErrors([
            'email' => 'No se encontró un OTP activo. Inicia sesión nuevamente para generar uno nuevo.',
        ]);
    }

    // Segundos restantes (si es negativo, forzamos a 0)
    $remaining = now()->diffInSeconds($otp->expira_en, false);
    if ($remaining < 0) {
        $remaining = 0;
    }

    return view('auth.otp', [
        'remainingSeconds' => $remaining,
    ]);
    }

    // Validar OTP
    public function validateOtp(Request $request)
    {
        $request->validate(['codigo' => 'required|digits:6']);

        $user_id = session('otp_user_id');
        $otp = CodigoOtp::where('user_id', $user_id)
            ->where('codigo', $request->codigo)
            ->where('utilizado', false)
            ->where('expira_en', '>', now())
            ->first();

        if (!$otp) {
            return back()->withErrors(['codigo' => 'Código inválido o expirado']);
        }

        // Marcar OTP usado
        $otp->update(['utilizado' => true]);

        // Autenticar
        Auth::loginUsingId($user_id);

        // Limpiar sesión temporal
        session()->forget('otp_user_id');

        return redirect()->route('dashboard');
    }
    public function resendOtp(Request $request)
{
    $userId = session('otp_user_id');

    if (!$userId) {
        return redirect()->route('login')->withErrors([
            'email' => 'Primero inicia sesión para generar un código OTP.',
        ]);
    }

    $user = \App\Models\User::find($userId);

    if (!$user) {
        return redirect()->route('login')->withErrors([
            'email' => 'No se encontró el usuario.',
        ]);
    }

    // Generar un nuevo código OTP
    $codigo = rand(100000, 999999);
    $expiraEn = now()->addSeconds(60);

    \App\Models\CodigoOtp::create([
        'user_id'   => $user->id,
        'codigo'    => $codigo,
        'expira_en' => $expiraEn,
        'utilizado' => false,
    ]);

    // Enviar correo con el nuevo OTP
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new OtpCodeMail($user, $codigo, 60));

    return redirect()->route('otp.form')->with('status', 'Se ha enviado un nuevo OTP a tu correo.');
}

}
