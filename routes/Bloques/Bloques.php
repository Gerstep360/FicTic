<?php  
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloqueController;
Route::middleware(['auth'])
    ->prefix('admin')
    ->group(function () {

        // CATÁLOGOS: Bloques
        Route::resource('bloques', BloqueController::class)->names([
            'index'   => 'bloques.index',
            'create'  => 'bloques.create',
            'store'   => 'bloques.store',
            'show'    => 'bloques.show',
            'edit'    => 'bloques.edit',
            'update'  => 'bloques.update',
            'destroy' => 'bloques.destroy',
        ]);
    });