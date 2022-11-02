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

## 

