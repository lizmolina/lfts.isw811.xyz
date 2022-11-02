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

## Validate and Store Post Thumbnails

En este video, aprenderá cómo cargar imágenes en su disco local utilizando una entrada de archivo estándar y la clase UploadedFile de Laravel. ¡Es tan fácil!

El controlador de publicación `PostController` se agrega el siguiente atributo para la versión reducida de la imagen. 


    'thumbnail' => 'required|image',

    $attributes['thumbnail'] = request()->file('thumbnail')->store('thumbnails');

Para quedar en función store(),  del siguiente modo. 

```php
 public function store()
    {
        $attributes = request()->validate([
            'title' => 'required',
            'thumbnail' => 'required|image',
            'slug' => ['required', Rule::unique('posts', 'slug')],
            'excerpt' => 'required',
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        $attributes['user_id'] = auth()->id();
        $attributes['thumbnail'] = request()->file('thumbnail')->store('thumbnails');

        Post::create($attributes);

        return redirect('/');
    }
```

Se actualiza la vista `create.blade.php`

```html
<x-layout>

    <section class="py-8 max-w-md mx-auto">
        <h1 class="text-lg font-bold mb-4">
            Publish New Post
        </h1>

        <x-panel>
            <form method="POST" action="/admin/posts" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="title"
                    >
                        Title
                    </label>
                    <input class="border border-gray-400 p-2 w-full"
                           type="text"
                           name="title"
                           id="title"
                           value="{{ old('title') }}"
                           required
                    >
                    @error('title')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="slug"
                    >
                        Slug
                    </label>
                    <input class="border border-gray-400 p-2 w-full"
                           type="text"
                           name="slug"
                           id="slug"
                           value="{{ old('slug') }}"
                           required
                    >
                    @error('slug')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="thumbnail"
                    >
                        Thumbnail
                    </label>

                    <input class="border border-gray-400 p-2 w-full"
                           type="file"
                           name="thumbnail"
                           id="thumbnail"
                           required
                    >

                    @error('thumbnail')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>


                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="excerpt"
                    >
                        Excerpt
                    </label>
                    <textarea class="border border-gray-400 p-2 w-full"
                           name="excerpt"
                           id="excerpt"
                           required
                    >{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="body"
                    >
                        Body
                    </label>
                    <textarea class="border border-gray-400 p-2 w-full"
                           name="body"
                           id="body"
                           required
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                           for="category_id"
                    >
                        Category
                    </label>
                    <select name="category_id" id="category_id">
                        @foreach (\App\Models\Category::all() as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}
                            >{{ ucwords($category->name) }}</option>
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

Se actualiza la ruta de las vistas, para mostrar la nueva imagen minuatura, en las siguientes vistas del component.

    show.blade.php
    post-featured-card.blade.php
    post-card.blade.php

```html
  <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="" class="rounded-xl">
```

Se agrega el nuevo atributo a las migraciones, en especifico `create_posts_table`

    $table->string('thumbnail')->nullable();


Y se refresca las migraciones en la base de datos, dentro de la maquina virtual vagrant. 

    php artisan migrate:fresh --seed


En la carpeta config, modificamos el archivo filesystems, haciendo lo público

    'default' => env('FILESYSTEM_DRIVER', 'public'),

## Extract Form-Specific Blade Components

 Aprenderemos cómo limpiar el HTML de un formulario extrayendo una serie de "piezas" reutilizables que se pueden usar para construir cada sección. Por supuesto, usaremos componentes Blade para permitir esto

Vamos a crear una vista para cada componente del formulario, asi gestionar un código limpio y reutilizable. 

Primero vamos a crear una carpeta llamada form, en el folder de component, de las vistas del proyecto. 
Dentro del nuevo folder creado, comenzaremos agregar las vistas. 

Primero creamos -->`button.blade.php`

```html
<x-form.field> botón ---guardar --crear
    <button type="submit"
            class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600"
    >
        {{ $slot }}
    </button>
</x-form.field>
```

---> `error.blade.php` --mensaje 

```html
@props(['name'])

@error($name)
    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
@enderror
```

---> `field.blade.php` 

```html
<div class="mt-6">
    {{ $slot }}
</div>
```

---> `input.blade.php`

```html 
@props(['name', 'type' => 'text'])

<x-form.field>
    <x-form.label name="{{ $name }}"/>

    <input class="border border-gray-400 p-2 w-full"
           type="{{ $type }}"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name) }}"
           required
    >

    <x-form.error name="{{ $name }}"/>
</x-form.field>
```

---> `label.blade.php`

```html
@props(['name'])

<label class="block mb-2 uppercase font-bold text-xs text-gray-700"
       for="{{ $name }}"
>
    {{ ucwords($name) }}
</label>
```

---> `textarea.blade.php`

```html
@props(['name'])

<x-form.field>
    <x-form.label name="{{ $name }}" />

    <textarea
        class="border border-gray-400 p-2 w-full"
        name="{{ $name }}"
        id="{{ $name }}"
        required
    >{{ old($name) }}</textarea>

    <x-form.error name="{{ $name }}" />
</x-form.field>
```

Se modifica la vista `_add-comment-form.blade.php`, se encuentra en la carpeta posts, en vistas. 

```html
 <x-form.button>Submit</x-form.button>
 ```

 Y por último se modifica la vista `create.blade.php`, que se encuentra en la carpeta posts, agregando las directivas de las vistas del los componentes para el formulario de publicaciones. 

 ```html
 <x-layout>

    <section class="py-8 max-w-md mx-auto">
        <h1 class="text-lg font-bold mb-4">
            Publish New Post
        </h1>

        <x-panel>
            <form method="POST" action="/admin/posts" enctype="multipart/form-data">
                @csrf

                <x-form.input name="title" />
                <x-form.input name="slug" />
                <x-form.input name="thumbnail" type="file" />
                <x-form.textarea name="excerpt" />
                <x-form.textarea name="body" />
                <x-form.field>
                    <x-form.label name="category" />

                    <select name="category_id" id="category_id">
                        @foreach (\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ ucwords($category->name) }}</option>
                        @endforeach
                    </select>
                    <x-form.error name="category" />
                </x-form.field>
                <x-form.button>Publish</x-form.button>
            </form>
        </x-panel>
    </section>
</x-layout>
```

## Extend the Admin Layout

En este episodio, agregaremos un menú desplegable de cuenta al área de navegación y luego ampliaremos el diseño de nuestra sección de configuración/administración para permitir una barra lateral.

Para esto haremos una serie de modificaciones, en las vista creadas anteriormente. 

Modificamos la vista `input.blade`

```html
@props(['name'])

<x-form.field>
    <x-form.label name="{{ $name }}" />

    <input class="border border-gray-200 p-2 w-full rounded" name="{{ $name }}" id="{{ $name }}"
        value="{{ old($name) }}" required {{ $attributes }}>

    <x-form.error name="{{ $name }}" />
</x-form.field>
```

Modificamos la vista `dropdown.blade`

```html
props(['trigger'])


<div x-data="{ show: false }" @click.away="show = false" class="relative">
    {{-- Trigger --}}
    <div @click="show = ! show">
        {{ $trigger }}
    </div>
    {{-- Links --}}
    <div x-show="show" class="py-2 absolute bg-gray-100 mt-2 rounded-xl w-full z-50 overflow-auto max-h-52" style="display: none">
        {{ $slot }}
    </div>
</div>
```

Modificamos la vista `texarea.blade`

```html
@props(['name'])

<x-form.field>
    <x-form.label name="{{ $name }}" />

    <textarea
    class="border border-gray-200 p-2 w-full rounded"
        name="{{ $name }}"
        id="{{ $name }}"
        required
        {{ $attributes }}
    >{{ old($name) }}</textarea>

    <x-form.error name="{{ $name }}" />
</x-form.field>
```

Modificamos la vista `layout.blade`

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
                <x-dropdown>
                    <x-slot name="trigger">
                        <button class="text-xs font-bold uppercase">Welcome, {{ auth()->user()->name }}!</button>
                    </x-slot>

                    <x-dropdown-item href="/admin/dashboard">Dashboard</x-dropdown-item>
                    <x-dropdown-item href="/admin/posts/create" :active="request()->is('admin/posts/create')">New Post</x-dropdown-item>
                    <x-dropdown-item href="#" x-data="{}" @click.prevent="document.querySelector('#logout-form').submit()">Log Out</x-dropdown-item>

                    <form id="logout-form" method="POST" action="/logout" class="hidden">
                        @csrf
                    </form>
                </x-dropdown>
                @else
                <a href="/register" class="text-xs font-bold uppercase {{ request()->is('register') ? 'text-blue-500' : '' }}">Register</a>
                <a href="/login" class="ml-6 text-xs font-bold uppercase {{ request()->is('login') ? 'text-blue-500' : '' }}">Log In</a>
                @endauth

                <a href="#newsletter" class="bg-blue-500 ml-3 rounded-full text-xs font-semibold text-white uppercase py-3 px-5">
                    Subscribe for Updates
                </a>
            </div>
        </nav>

        {{ $slot }}

        <footer id="newsletter" class="bg-gray-100 border border-black border-opacity-5 rounded-xl text-center py-16 px-10 mt-16">
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
                                class="transition-colors duration-300 bg-blue-500 hover:bg-blue-600 mt-4 lg:mt-0 lg:ml-3 rounded-full text-xs font-semibold text-white uppercase py-3 px-8"
                        >
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

Creamos la vista `setting.blade`

```html 
@props(['heading'])

<section class="py-8 max-w-4xl mx-auto">
    <h1 class="text-lg font-bold mb-8 pb-2 border-b">
        {{ $heading }}
    </h1>

    <div class="flex">
        <aside class="w-48">
            <h4 class="font-semibold mb-4">Links</h4>

            <ul>
                <li>
                    <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'text-blue-500' : '' }}">Dashboard</a>
                </li>

                <li>
                    <a href="/admin/posts/create" class="{{ request()->is('admin/posts/create') ? 'text-blue-500' : '' }}">New Post</a>
                </li>
            </ul>
        </aside>

        <main class="flex-1">
            <x-panel>
                {{ $slot }}
            </x-panel>
        </main>
    </div>
</section>
```
Modificamos la vista `create.blade` de la carpeta posts

```html
<x-layout>

    <x-setting heading="Publish New Post">
        <form method="POST" action="/admin/posts" enctype="multipart/form-data">
            @csrf

            <x-form.input name="title" />
            <x-form.input name="slug" />
            <x-form.input name="thumbnail" type="file" />
            <x-form.textarea name="excerpt" />
            <x-form.textarea name="body" />

            <x-form.field>
                <x-form.label name="category" />

                <select name="category_id" id="category_id">
                    @foreach (\App\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ ucwords($category->name) }}</option>
                    @endforeach
                </select>

                <x-form.error name="category" />
            </x-form.field>
            <x-form.button>Publish</x-form.button>
        </form>
    </x-setting>
</x-layout>


```

## Create a Form to Edit and Delete Posts

Falta una característica evidente que debemos implementar: cualquier publicación puede editarse o eliminarse. Trabajaremos para permitir eso en este episodio.

En este espisodio aprederemos a rellenar formularios de edición y actualizar la reglas de validación. Para esto haremos lo siguiente:

Crearemos un nuevo controllador llamado `AdminPostController`, que funcionara para el registro de nuevos publicaciones del administrador. 

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Validation\Rule;

class AdminPostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index', [
            'posts' => Post::paginate(50)
        ]);
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store()
    {
        $attributes = request()->validate([
            'title' => 'required',
            'thumbnail' => 'required|image',
            'slug' => ['required', Rule::unique('posts', 'slug')],
            'excerpt' => 'required',
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        $attributes['user_id'] = auth()->id();
        $attributes['thumbnail'] = request()->file('thumbnail')->store('thumbnails');

        Post::create($attributes);

        return redirect('/');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', ['post' => $post]);
    }

    public function update(Post $post)
    {
        $attributes = request()->validate([
            'title' => 'required',
            'thumbnail' => 'image',
            'slug' => ['required', Rule::unique('posts', 'slug')->ignore($post->id)],
            'excerpt' => 'required',
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        if (isset($attributes['thumbnail'])) {
            $attributes['thumbnail'] = request()->file('thumbnail')->store('thumbnails');
        }

        $post->update($attributes);

        return back()->with('success', 'Post Updated!');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success', 'Post Deleted!');
    }
}

```

Eliminamos la función de create() y store(), del controlador `PostController`

En vistas creamos una nueva carpeta para admin, con un folder de post, en los cuales se crearan tres vistas. 

----> La primera se llamara `create.blade`

```html
<x-layout>
    <x-setting heading="Publish New Post">
        <form method="POST" action="/admin/posts" enctype="multipart/form-data">
            @csrf

            <x-form.input name="title" required />
            <x-form.input name="slug" required />
            <x-form.input name="thumbnail" type="file" required />
            <x-form.textarea name="excerpt" required />
            <x-form.textarea name="body" required />

            <x-form.field>
                <x-form.label name="category"/>

                <select name="category_id" id="category_id" required>
                    @foreach (\App\Models\Category::all() as $category)
                        <option
                            value="{{ $category->id }}"
                            {{ old('category_id') == $category->id ? 'selected' : '' }}
                        >{{ ucwords($category->name) }}</option>
                    @endforeach
                </select>

                <x-form.error name="category"/>
            </x-form.field>

            <x-form.button>Publish</x-form.button>
        </form>
    </x-setting>
</x-layout>
```
---> Otra con el nombre `edit.blade` 

```html
<x-layout>
    <x-setting :heading="'Edit Post: ' . $post->title">
        <form method="POST" action="/admin/posts/{{ $post->id }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <x-form.input name="title" :value="old('title', $post->title)" required />
            <x-form.input name="slug" :value="old('slug', $post->slug)" required />

            <div class="flex mt-6">
                <div class="flex-1">
                    <x-form.input name="thumbnail" type="file" :value="old('thumbnail', $post->thumbnail)" />
                </div>

                <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="" class="rounded-xl ml-6" width="100">
            </div>

            <x-form.textarea name="excerpt" required>{{ old('excerpt', $post->excerpt) }}</x-form.textarea>
            <x-form.textarea name="body" required>{{ old('body', $post->body) }}</x-form.textarea>

            <x-form.field>
                <x-form.label name="category"/>

                <select name="category_id" id="category_id" required>
                    @foreach (\App\Models\Category::all() as $category)
                        <option
                            value="{{ $category->id }}"
                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}
                        >{{ ucwords($category->name) }}</option>
                    @endforeach
                </select>

                <x-form.error name="category"/>
            </x-form.field>

            <x-form.button>Update</x-form.button>
        </form>
    </x-setting>
</x-layout>
```

--->  Luego la última llamada `index.blade` estas funcionaran solamente para que el administrador cree nuevas publicaciones. 

```html
<x-layout>
    <x-setting heading="Manage Posts">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($posts as $post)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a href="/posts/{{ $post->slug }}">
                                                        {{ $post->title }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="/admin/posts/{{ $post->id }}/edit" class="text-blue-500 hover:text-blue-600">Edit</a>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form method="POST" action="/admin/posts/{{ $post->id }}">
                                                @csrf
                                                @method('DELETE')

                                                <button class="text-xs text-gray-400">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-setting>
</x-layout>
```

Recuerda cambiar las rutas que hacen llamados a las vista anteriores. 

Agregamos la rutas en `web.php`

```php
Route::post('admin/posts', [AdminPostController::class, 'store'])->middleware('admin');
Route::get('admin/posts/create', [AdminPostController::class, 'create'])->middleware('admin');
Route::get('admin/posts', [AdminPostController::class, 'index'])->middleware('admin');
Route::get('admin/posts/{post}/edit', [AdminPostController::class, 'edit'])->middleware('admin');
Route::patch('admin/posts/{post}', [AdminPostController::class, 'update'])->middleware('admin');
Route::delete('admin/posts/{post}', [AdminPostController::class, 'destroy'])->middleware('admin');

```

## Group and Store Validation Logic

Notará que nuestra lógica de validación del controlador ahora se ha duplicado en su mayoría. En esta lección, discutiremos los pros y los contras de mantener esa duplicación, antes de aprender a normalizarla y extraerla en un método reutilizable.

Aprenderemos a normalizar reglas de validación y quitar la duplicación. 

Para esto trabajaremos con el controlador `AdminPostController`, modificaremos funciones y agregaremos nuevas. 

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Validation\Rule;

class AdminPostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index', [
            'posts' => Post::paginate(50)
        ]);
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store()
    {
        Post::create(array_merge($this->validatePost(), [
            'user_id' => request()->user()->id,
            'thumbnail' => request()->file('thumbnail')->store('thumbnails')
        ]));

        return redirect('/');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', ['post' => $post]);
    }

    public function update(Post $post)
    {
        $attributes = $this->validatePost($post);

        if ($attributes['thumbnail'] ?? false) {
            $attributes['thumbnail'] = request()->file('thumbnail')->store('thumbnails');
        }

        $post->update($attributes);

        return back()->with('success', 'Post Updated!');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success', 'Post Deleted!');
    }

    protected function validatePost(?Post $post = null): array
    {
        $post ??= new Post();

        return request()->validate([
            'title' => 'required',
            'thumbnail' => $post->exists ? ['image'] : ['required', 'image'],
            'slug' => ['required', Rule::unique('posts', 'slug')->ignore($post)],
            'excerpt' => 'required',
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')]
        ]);
    }
}
```

## All About Authorization

¿Se dio cuenta de que cualquier persona que haya iniciado sesión verá enlaces específicos del administrador dentro de la barra de navegación desplegable? ¿Cómo podríamos arreglar eso? Parece que tendremos que modificar un poco nuestra autorización.

Aprenderemos puertas, directivas de autorización de cuchillas, grupos de rutas  y 
recursos de ruta

En este episodio eliminaremos el Middleware `MustBeAdministrator, recuerde eliminar las instancias colocadas anteriormente. 

```php
 protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => MustBeAdministrator::class, ----Esta
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
```

Modificamos la clase `AppServiceProvider`, de la carpeta Providers

```php
<?php

namespace App\Providers;

use App\Services\Newsletter;
use App\Services\MailchimpNewsletter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use MailchimpMarketing\ApiClient;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
        /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind(Newsletter::class, function () {
            $client = (new ApiClient)->setConfig([
                'apiKey' => config('services.mailchimp.key'),
                'server' => 'us21'
            ]);

            return new MailchimpNewsletter($client);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();

        Gate::define('admin', function (User $user) {
            return $user->username === 'emolina';
        });

        Blade::if('admin', function () {

            return optional(request()->user())->can('admin');

            //return request()->user()?->can('admin');
        });
    }



}
```

Modificamos la rutas en `web.php`, las agrupamos para que solo las pueda ver el administrador

```php

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostCommentsController;


Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('posts/{post:slug}', [PostController::class, 'show']);
Route::post('posts/{post:slug}/comments', [PostCommentsController::class, 'store']);

Route::post('newsletter', NewsletterController::class);

Route::get('register', [RegisterController::class, 'create'])->middleware('guest');
Route::post('register', [RegisterController::class, 'store'])->middleware('guest');

Route::get('login', [SessionsController::class, 'create'])->middleware('guest');
Route::post('login', [SessionsController::class, 'store'])->middleware('guest');

Route::post('logout', [SessionsController::class, 'destroy'])->middleware('auth');

// Admin Section
Route::middleware('can:admin')->group(function () {
    Route::resource('admin/posts', AdminPostController::class)->except('show');
});

```

