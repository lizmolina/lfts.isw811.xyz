[<-Go back](/README.md)

# Forms and Authentication

## Build a Register User Page

En este episodio comenzaremos con el manejo de formularios y la autenticación de usuarios. Para comenzar, crearemos una ruta que muestre un formulario de registro para registrarse en nuestro sitio.

Se iniciará con la creación de un controler para el modulo `Registro`, en la maquina `vagrant`, ruta `cd \vagrant\sites\lfts.isw811.xyz`

    php artisan make:controller RegisterController 

Al cual se le agrega el siguiente código, que contiene la función de crear el nuevo registro de usuario.

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;

class RegisterController extends Controller
{
    public function create()
    {
        return view('register.create');
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => 'required|max:255',
            'username' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:7|max:255',
        ]);

        User::create($attributes);

        return redirect('/');
    }
}
```

Luego creamos una carpeta en la sección de vistas, llamada `register` y ella se agregará el nuevo html la vista de creación de registro, con el nombre de `create.blade.php`

```html
<x-layout>
    <section class="px-6 py-8">
        <main class="max-w-lg mx-auto mt-10 bg-gray-100 border border-gray-200 p-6 rounded-xl">
            <h1 class="text-center font-bold text-xl">Register!</h1>

            <form method="POST" action="/register" class="mt-10">
                @csrf

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="name">
                        Name
                    </label>

                    <input class="border border-gray-400 p-2 w-full" type="text" name="name" id="name"
                        required>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="username">
                        Username
                    </label>

                    <input class="border border-gray-400 p-2 w-full" type="text" name="username" id="username"
                        required>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="email">
                        Email
                    </label>

                    <input class="border border-gray-400 p-2 w-full" type="email" name="email" id="email"
                        required>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="password">
                        Password
                    </label>

                    <input class="border border-gray-400 p-2 w-full" type="password" name="password" id="password"
                        required>
                </div>

                <div class="mb-6">
                    <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500">
                        Submit
                    </button>
                </div>
            </form>
        </main>
    </section>
</x-layout>
```

Luego eliminamos el modelo de `User.php`

```php
protected $fillable = [
        'name',
        'email',
        'password',
    ];

```

Y agregamos

```php
protected $guarded = [];
```

Por último se incorporan las rutas para registro en las routes `web.php`. Recuerde importar las dependencias.

```php
Route::get('register', [RegisterController::class, 'create'])->middleware('guest');

Route::post('register', [RegisterController::class, 'store'])->middleware('guest');
```

## Automatic Password Hashing With Mutators

Aprovecharemos los mutadores de Eloquent para asegurarnos de que las contraseñas siempre se codifican antes de que se conserve. En este capitulo de encriptan la contraseñas.

Para eso agregaremos la función `setPasswordAttribute` al modelo `User.php`

```php
 public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
```

## Failed Validation and Old Input Data

A continuación, debemos proporcionar comentarios al usuario cada vez que falle el verificador de validación. En estos casos, podemos buscar la directiva @error Blade para representar fácilmente el mensaje de validación correspondiente de un atributo (si corresponde). También discutiremos cómo obtener datos de entrada antiguos ().

Para esto modificamos los atributos en el controlador de `Registro`, `RegisterControler.php`, que deseamos que posean requisitos de validación al registrar. En nuestro caso modificamos en `username` y `email`.

    'username' => 'required|min:3|max:255|unique:users,username',
    'email' => 'required|email|max:255|unique:users,email',

Luego agregamos la directiva `@error Blade` en la vista html de `create.blade.php` para registrar el nuevo usuario y así poder mostrar el mensaje de error. Recuerda agregar a cada uno de los atributos a ingresar, por ejemplo, `name`, `username`, `email`, `password`

```html
@error('name')
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
@enderror
```

Como obtener datos de entreda antiguos.

    value="{{ old('name') }}"

## Show a Success Flash Message

A continuación se mostrará al usuario un comentario despues de registrarse exitosamente a nuestro sitio. Con la ayuda de un Mensaje Flash.

Primero creamos una nueva vista en la carpeta `components` perteneciente a vistas, con el nombre de `flash.blade.php`

```html
@if (session()->has('success'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 4000)"
         x-show="show"
         class="fixed bg-blue-500 text-white py-2 px-4 rounded-xl bottom-3 right-3 text-sm"
    >
        <p>{{ session('success') }}</p>
    </div>
@endif
```

Luego agregamos la directiva de la nueva vista creada a la vista de `layout.blade.php`

    </section>
        <x-flash /> --------esta linea
    </body>

Para finalizar agregamos el `mensaje flash` en la función `store` de `RegisterController.php`, al final de `return` que mostrará al usuario registrado un mensaje de exito.

```php
 return redirect('/')->with('success', 'Your account has been created.');
```

## Login and Logout

En este capitulo preparamos el ambiente para hacer el `login` y el `logout`, creamos una controler llamado `SessionsController.php`, con el comando

    php artisan make:controller SessionsController

Y se agrega el código, para iniciar y salir la sesión.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth()->attempt($attributes)) {
            session()->regenerate();

            return redirect('/')->with('success', 'Welcome Back!');
        }

        throw ValidationException::withMessages([
            'email' => 'Your provided credentials could not be verified.'
        ]);
    }

    public function destroy()
    {
        auth()->logout();

        return redirect('/')->with('success', 'Goodbye!');
    }
}

```

En la vista layout.blade se agrega el código html para mostrar la etiquetas de `login` y `logout`, además  <div class="mt-8 md:mt-0 flex items-center">
    @auth
<span class="text-xs font-bold uppercase">Welcome, {{ auth()->user()->name }}!</span>

    <form method="POST" action="/logout" class="text-xs font-semibold text-blue-500 ml-6">
    @csrf

<button type="submit">Log Out</button>
</form>
@else
 <a href="/register" class="text-xs font-bold uppercase">Register</a>
 <a href="/login" class="ml-6 text-xs font-bold uppercase">Log In</a>
@endauth  redireccionar las rutas.

```html
  <div class="mt-8 md:mt-0 flex items-center">
                @auth
                    <span class="text-xs font-bold uppercase">Welcome, {{ auth()->user()->name }}!</span>

                    <form method="POST" action="/logout" class="text-xs font-semibold text-blue-500 ml-6">
                        @csrf

                        <button type="submit">Log Out</button>
                    </form>
                @else
                    <a href="/register" class="text-xs font-bold uppercase">Register</a>
                    <a href="/login" class="ml-6 text-xs font-bold uppercase">Log In</a>
                @endauth
```

Se modifica el `RouteServiceProvider.php` en `Providers`

    public const HOME = '/';

Se agrega la ruta para el `logout` en `web.php`

```php
Route::post('logout', [SessionsController::class, 'destroy'])->middleware('auth');
```

## Build the Log In Page

En este episodio se crea la vista para inicio sesión y enlace para la rutas, además se crean las rutas para el login. 


Se crea una carpeta en `views` para las sesiones, llamada `sessions` y dentro se crea la vista para el `login` con el nombre de `create.blade.php`, en ella se agrega el código 

```html
<x-layout>
    <section class="px-6 py-8">
        <main class="max-w-lg mx-auto mt-10 bg-gray-100 border border-gray-200 p-6 rounded-xl">
            <h1 class="text-center font-bold text-xl">Log In!</h1>

            <form method="POST" action="/login" class="mt-10">
                @csrf

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="email"
                    >
                        Email
                    </label>

                    <input class="border border-gray-400 p-2 w-full"
                           type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           required
                    >

                    @error('email')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="password"
                    >
                        Password
                    </label>

                    <input class="border border-gray-400 p-2 w-full"
                           type="password"
                           name="password"
                           id="password"
                           required
                    >

                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <button type="submit"
                            class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500"
                    >
                        Log In
                    </button>
                </div>
            </form>
        </main>
    </section>
</x-layout>
```

Luego se agregan las rutas de login en `web.php`

```php
Route::get('login', [SessionsController::class, 'create'])->middleware('guest');
Route::post('login', [SessionsController::class, 'store'])->middleware('guest');
```

## Laravel Breeze Quick Peek

Con el sistema de autenticación listo se revisa el código, se hacen modificaciones para mejorar y se crea un demo para probar los paquetes de autenticación `Laravel Breeze`

Se modifica la función `store` en el controlador `SessionsController.php`

```php
public function store()
    {
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (! auth()->attempt($attributes)) {
            throw ValidationException::withMessages([
                'email' => 'Your provided credentials could not be verified.'
            ]);
        }

        session()->regenerate();

        return redirect('/')->with('success', 'Welcome Back!');
    }
```


