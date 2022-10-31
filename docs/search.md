[<-Go back](/README.md)

# Search

## Search (The Messy Way)

En esta nueva sección, se implementará  la funcionalidad de búsqueda para nuestro blog. El codigo se  hará funcionable pero no reutilizable. 

Para esto utilizaremos el codigo que se encuentra en la vista `_posts-header.blade.php`, solo agregaremos el atributo `value`. 

```php
 <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl px-3 py-2">
        <form method="GET" action="#">
            <input typ
            e="text" name="search" placeholder="Find something"
                class="bg-transparent placeholder-black font-semibold text-sm"
                value= "{{ request('search')}}">
        </form>
    </div>
```

Y se modifica la ruta en `web.php`

```php
Route::get('/', function () {

    $posts = Post:: latest();

    if(request('search')){
        $posts
        ->where('title', 'like', '%' . request('search') . '%')
        ->orWhere('body', 'like', '%' . request('search') . '%');
    }

    return view('posts', [
        'posts' => $posts->get(),
        'categories' => Category::all()
    ]);
})->name('home');
```


## Search (The Cleaner Way)

En este episodio se  refactoriza el código de la función de busqueda en algo más agradable a la vista (y reutilizable). Para esto se crea un nuevo controlador y se modifican las rutas y vistas creadas para el funcionamiento del filtro de busqueda. 

Se crea un nuevo controlador: 

    php artisan make:controller PostController

EL controlador `PostController`, se agrega el siguiente código:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return view('posts', [
            'posts' => Post::latest()->filter(request(['search']))->get(),
            'categories' => Category::all()
        ]);
    }

    public function show(Post $post)
    {
        return view('post', [
            'post' => $post
        ]);
    }
}
```
En la vista `_posts-header.blade.php` se agrega modifica el input del formulario, referente al filtro de busqueda: 

```html
<form method="GET" action="#">
            <input type="text"
            name="search"
            placeholder="Find something"
            class="bg-transparent placeholder-black font-semibold text-sm"
            value="{{ request('search') }}">
</form>
```

Se agrega la siguiente función en el modelo `Post.php`

```php
public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('body', 'like', '%' . $search . '%'));
    }
```

Y por último, se modifcan las rutas en `web.php`

```php
Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('posts/{post:slug}', [PostController::class, 'show']);
```
