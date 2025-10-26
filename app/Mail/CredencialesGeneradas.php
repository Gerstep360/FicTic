<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredencialesGeneradas extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombre;
    public string $email;
    public string $passwordPlano;
    public ?string $urlLogin;

    public function __construct(string $nombre, string $email, string $passwordPlano, ?string $urlLogin = null)
    {
        $this->nombre       = $nombre;
        $this->email        = $email;
        $this->passwordPlano= $passwordPlano;
        $this->urlLogin     = $urlLogin;
    }

    public function build()
    {
        return $this->subject('Tus credenciales de acceso - FICCT')
            ->view('emails.credenciales_generadas')   // Crea esta vista Blade sencilla
            ->with([
                'nombre'        => $this->nombre,
                'email'         => $this->email,
                'passwordPlano' => $this->passwordPlano,
                'urlLogin'      => $this->urlLogin ?? config('app.url'),
            ]);
    }
}
