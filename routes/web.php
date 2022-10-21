<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route :: get('posts/{post}', function($slug){
    $path= __DIR__ . "/../resources/posts/{$slug}.html";

    if( ! file_exists($path)){
        return redirect('/'); 
        

    }
    $post= file_get_contents($path);

    return view('post', [
        'post' => $post
    ]);

})->where('post','[A-z_\-]+');

Route::get('/', function () {
    return view('posts');
});




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
