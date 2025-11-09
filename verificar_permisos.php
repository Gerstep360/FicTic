<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\User;

echo "=== VERIFICACIÓN DE PERMISOS ===" . PHP_EOL . PHP_EOL;

// 1. Verificar permisos en Admin DTIC
$admin = Role::where('name', 'Admin DTIC')->first();
if ($admin) {
    echo "✅ Rol 'Admin DTIC' existe" . PHP_EOL;
    echo "Total permisos: " . $admin->permissions->count() . PHP_EOL;
    
    $tiene_reportes = $admin->permissions->where('name', 'ver_reportes')->count() > 0;
    $tiene_reprog = $admin->permissions->where('name', 'gestionar_reprogramaciones')->count() > 0;
    
    echo ($tiene_reportes ? "✅" : "❌") . " ver_reportes" . PHP_EOL;
    echo ($tiene_reprog ? "✅" : "❌") . " gestionar_reprogramaciones" . PHP_EOL;
} else {
    echo "❌ Rol 'Admin DTIC' NO existe" . PHP_EOL;
}

echo PHP_EOL;

// 2. Verificar usuario Admin
$user = User::find(1);
if ($user) {
    echo "Usuario ID 1: " . $user->name . PHP_EOL;
    echo "Email: " . $user->email . PHP_EOL;
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . PHP_EOL;
    echo PHP_EOL;
    
    echo "Puede ver_reportes: " . ($user->can('ver_reportes') ? "✅ SI" : "❌ NO") . PHP_EOL;
    echo "Puede gestionar_reprogramaciones: " . ($user->can('gestionar_reprogramaciones') ? "✅ SI" : "❌ NO") . PHP_EOL;
}

echo PHP_EOL . "=== FIN VERIFICACIÓN ===" . PHP_EOL;
