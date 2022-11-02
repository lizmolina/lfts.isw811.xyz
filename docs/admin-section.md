[<-Go back](/README.md)

# Admin Section

## Limit Access to Only Admins

Por último, trabajemos en la sección de administración para publicar nuevas publicaciones. Antes de comenzar a construir el formulario, primero averigüemos cómo agregar la capa de autorización necesaria para garantizar que solo los administradores puedan acceder a esta sección del sitio. Este episodio definiremos el administrador.

Agregamos las siguientes funciones al controlador de publicación `PostController`, para crear nuevo posts

```php
public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
        $attributes = request()->validate([
            'title' => 'required',
            'slug' => ['required', Rule::unique('posts', 'slug')],
            'excerpt' => 'required',
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        $attributes['user_id'] = auth()->id();

        Post::create($attributes);

        return redirect('/');
    }
```

Creamos un Middleware nuevo, el máquina vagrant, dentro del carpeta del proyecto, con el comando 

    php artisan make:middleware MustBeAdministrator


Y agregamos el siguente código 

```php
public function handle(Request $request, Closure $next)
    {
        if (auth()->user()?->username !== 'JeffreyWay') {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
```

En el Kernel, agregamos la nueva directiva de MustBeAdministrator

```php 

use App\Http\Middleware\MustBeAdministrator;
'admin' => MustBeAdministrator::class, // definir como administrador
```

Quedando de la siguiente forma

```php

use App\Http\Middleware\MustBeAdministrator;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
@@ -54,6 +55,7 @@ class Kernel extends HttpKernel
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => MustBeAdministrator::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

```

Modificamos la siguiente linea en el controlador de Registro `RegisterController`

```php
 auth()->login(User::create($attributes));
```

## Create the Publish Post Form

Bien, ahora que hemos intentado por primera vez agregar alguna autorización de ruta, terminemos de crear el formulario "Publicar publicación". En este episodio crearemos la vista para mostrar el formulario de publicar el post. 

Para esto creamos la vista `create.blade`, en la carpeta de posts, perteneciente a vistas. 

```html
<x-layout>
    <section class="px-6 py-8">
        <x-panel class="max-w-sm mx-auto">
            <form method="POST" action="/admin/posts">
                @csrf

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="title">
                        Title
                    </label>

                    <input class="border border-gray-400 p-2 w-full" type="text" name="title" id="title"
                        value="{{ old('title') }}" required>

                    @error('title')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="slug">
                        Slug
                    </label>

                    <input class="border border-gray-400 p-2 w-full" type="text" name="slug" id="slug"
                        value="{{ old('slug') }}" required>

                    @error('slug')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>


                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="excerpt">
                        Excerpt
                    </label>

                    <textarea class="border border-gray-400 p-2 w-full" name="excerpt" id="excerpt" required>{{ old('excerpt') }}</textarea>

                    @error('excerpt')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="body">
                        Body
                    </label>

                    <textarea class="border border-gray-400 p-2 w-full" name="body" id="body" required>{{ old('body') }}</textarea>

                    @error('body')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="category_id">
                        Category
                    </label>

                    <select name="category_id" id="category_id">
                        @foreach (\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ ucwords($category->name) }}</option>
                        @endforeach
                    </select>

                    @error('category')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <x-submit-button>Publish</x-submit-button>
            </form>
        </x-panel>
    </section>
</x-layout>
```

Y agregamos las rutas para acceder al formulario creado, en `web.php`

```php
Route::get('admin/posts/create', [PostController::class, 'create'])->middleware('admin');
Route::post('admin/posts', [PostController::class, 'store'])->middleware('admin');
```
##



