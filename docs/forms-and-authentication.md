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

##
