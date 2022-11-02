[<-Go back](/README.md)

# Newsletters and APIs

## Mailchimp API Tinkering

Empezaremos a conocer API de Mailchimp. Aprenderemos cómo instalar el SDK oficial de PHP y luego revisaremos los conceptos básicos sobre cómo realizar algunas llamadas API iniciales.

En este episodio conocemos la funcionalidad de Mailchimp, para crear campañas publicitarias, con nuestros contactos, entre las actividades que desarrollamos es 

Crear cuenta -----https://us21.admin.mailchimp.com/signup/setup/

Generamos la API ---- https://us21.admin.mailchimp.com/account/api/

Instalamos los paquetes Mailchimp en nuestro proyecto, usando composer, (dentro la maquina vagrant cd /vagrant/sites/lfts.isw811.xyz)

```php
composer require mailchimp/marketing
```

Agregamos a `services.php`, pertenece a la carpeta config (del proyecto)

```php
 'mailchimp' => [
        'key' => env('MAILCHIMP_KEY'),
        'lists' => [
            'subscribers' => env('MAILCHIMP_LIST_SUBSCRIBERS')
        ]
    ]
```

Y al archivo `.env`


    MAILCHIMP_KEY= 
    MAILCHIMP_LIST_SUBSCRIBERS=

    
## Make the Newsletter Form Work

Ahora sabemos cómo agregar una dirección de correo electrónico a una lista de Mailchimp, actualicemos el formulario del boletín.

A continuación haremos modificaciones en la vista de `layout.blade.php`, para implementar la nueva funcionalidad de subscripcción. 

```html
<!doctype html>

<title>Laravel From Scratch Blog</title>
<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

<style>
    html {
        scroll-behavior: smooth;
    }
    .clamp {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .clamp.one-line {
        -webkit-line-clamp: 1;
    }
</style>

<body style="font-family: Open Sans, sans-serif">
    <section class="px-6 py-8">
        <nav class="md:flex md:justify-between md:items-center">
            <div>
                <a href="/">
                    <img src="/images/logo.svg" alt="Laracasts Logo" width="165" height="16">
                </a>
            </div>

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

                <a href="#newsletter"
                    class="bg-blue-500 ml-3 rounded-full text-xs font-semibold text-white uppercase py-3 px-5">
                    Subscribe for Updates
                </a>
            </div>
        </nav>

        {{ $slot }}


        <footer id="newsletter"   class="bg-gray-100 border border-black border-opacity-5 rounded-xl text-center py-16 px-10 mt-16">
            <img src="/images/lary-newsletter-icon.svg" alt="" class="mx-auto -mb-6" style="width: 145px;">
            <h5 class="text-3xl">Stay in touch with the latest posts</h5>
            <p class="text-sm mt-3">Promise to keep the inbox clean. No bugs.</p>

            <div class="mt-10">
                <div class="relative inline-block mx-auto lg:bg-gray-200 rounded-full">

                    <form method="POST" action="/newsletter" class="lg:flex text-sm">
                        @csrf
                        <div class="lg:py-3 lg:px-5 flex items-center">
                            <label for="email" class="hidden lg:inline-block">
                                <img src="/images/mailbox-icon.svg" alt="mailbox letter">
                            </label>

                            <div>
                                <input id="email"
                                       name="email"
                                       type="text"
                                       placeholder="Your email address"
                                       class="lg:bg-transparent py-2 lg:py-0 pl-4 focus-within:outline-none">

                                @error('email')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit"
                            class="transition-colors duration-300 bg-blue-500 hover:bg-blue-600 mt-4 lg:mt-0 lg:ml-3 rounded-full text-xs font-semibold text-white uppercase py-3 px-8">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </footer>
    </section>
    <x-flash />
</body>
```

##  Extract a Newsletter Service

Este episodio vamos a crear la propia clase para Newsletter, además, de su controlador correspondiente y agregar la ruta de enlace, para subscripción del boletín. (Objetivo, implementar buenas prácticas de programación. )

Creamos una clase con el nombre de `Newsletter.php` en un folder creado en `app`, llamado `Services`. 

```php
<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;

class Newsletter
{
    public function subscribe(string $email, string $list = null)
    {
        $list ??= config('services.mailchimp.lists.subscribers');

        return $this->client()->lists->addListMember($list, [
            'email_address' => $email,
            'status' => 'subscribed'
        ]);
    }

    protected function client()
    {
        return (new ApiClient())->setConfig([
            'apiKey' => config('services.mailchimp.key'),
            'server' => 'us6'
        ]);
    }
}
```

Luego creamos el controlador para `Newsletter.php`, en la maquina vagrant.

    php artisan make:controller NewsletterController

Y agregamos el código anteriormente desarrollado en rutas, a continuación

```php
<?php

namespace App\Http\Controllers;

use App\Services\Newsletter;
use Exception;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    public function __invoke(Newsletter $newsletter)
    {
        request()->validate(['email' => 'required|email']);

        try {
            $newsletter->subscribe(request('email'));
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'email' => 'This email could not be added to our newsletter list.'
            ]);
        }

        return redirect('/')
            ->with('success', 'You are now signed up for our newsletter!');
    }
}
```

Ahora implementamos la ruta de enlace, recuerde importar las referencias a la clase o controlador, en rutas `web.php`

```php
Route::post('newsletter', NewsletterController::class);
```

## Toy Chests and Contracts

En este episodio se modifica los Services, se crea  un archivo nuevo llamado `MailchimpNewsletter.php`

```php
<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;

class MailchimpNewsletter implements Newsletter
{
    public function __construct(protected ApiClient $client)
    {
        //
    }

    public function subscribe(string $email, string $list = null)
    {
        $list ??= config('services.mailchimp.lists.subscribers');

        return $this->client->lists->addListMember($list, [
            'email_address' => $email,
            'status' => 'subscribed'
        ]);
    }
}
```

Luego se modifica `Newsletter.php` 

```php
<?php

namespace App\Services;

interface Newsletter
{

public function subscribe(string $email, string $list = null);

}
```

