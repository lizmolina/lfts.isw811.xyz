[<-Go back](/README.md)

# Integrate The Desings

## Convert the HTML and CSS to Blade

Se comienza a construir el diseño real del blog para esta serie. Como se discutió en el episodio cuatro, ya escribí el código HTML y CSS base. Eso significa que solo necesitamos descargarlo de GitHub y comenzar a migrarlo a nuestra aplicación Laravel. Como parte de esto, prepararemos el archivo de diseño y extraeremos algunos componentes de Blade.

El codigo html y css, además de las imagenes a utilizar para el proyecto Laravel se descarga a esta dirección.

    https://github.com/laracasts/Laravel-From-Scratch-HTML-CSS

Una vez descargado, se copia la carpeta de `images` en el folder `public` de nuestro proyecto Laravel, luego utilizamos el codigo de archivo `ìndex.html` en las diferentes vistas del proyecto, y se crean nuevas vistas.

En la vista `layout.blade.php`, se modifica de la siguiente forma:

```html
<!doctype html>

<title>Laravel From Scratch Blog</title>
<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<body style="font-family: Open Sans, sans-serif">
    <section class="px-6 py-8">
        <nav class="md:flex md:justify-between md:items-center">
            <div>
                <a href="/">
                    <img src="/images/logo.svg" alt="Laracasts Logo" width="165" height="16">
                </a>
            </div>

            <div class="mt-8 md:mt-0">
                <a href="/" class="text-xs font-bold uppercase">Home Page</a>

                <a href="#" class="bg-blue-500 ml-3 rounded-full text-xs font-semibold text-white uppercase py-3 px-5">
                    Subscribe for Updates
                </a>
            </div>
        </nav>

        {{$slot}}


        <footer class="bg-gray-100 border border-black border-opacity-5 rounded-xl text-center py-16 px-10 mt-16">
            <img src="/images/lary-newsletter-icon.svg" alt="" class="mx-auto -mb-6" style="width: 145px;">
            <h5 class="text-3xl">Stay in touch with the latest posts</h5>
            <p class="text-sm mt-3">Promise to keep the inbox clean. No bugs.</p>

            <div class="mt-10">
                <div class="relative inline-block mx-auto lg:bg-gray-200 rounded-full">

                    <form method="POST" action="#" class="lg:flex text-sm">
                        <div class="lg:py-3 lg:px-5 flex items-center">
                            <label for="email" class="hidden lg:inline-block">
                                <img src="/images/mailbox-icon.svg" alt="mailbox letter">
                            </label>

                            <input id="email" type="text" placeholder="Your email address"
                                   class="lg:bg-transparent py-2 lg:py-0 pl-4 focus-within:outline-none">
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
</body>
```

La vista `posts.blade.php`, se modifica así:

```html
<x-layout>

    @include('_posts-header')


    <main class="max-w-6xl mx-auto mt-6 lg:mt-20 space-y-6">

        <x-post-featured-card/>

        <div class="lg:grid lg:grid-cols-2">
            <x-post-card/>
            <x-post-card/>

        </div>

        <div class="lg:grid lg:grid-cols-3">
            <x-post-card/>
            <x-post-card/>
            <x-post-card/>


        </div>
    </main>
```

Se crea la vista `post-card.blade.php`

```html
<article
                class="transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl">
                <div class="py-6 px-5">
                    <div>
                        <img src="/images/illustration-3.png" alt="Blog Post illustration" class="rounded-xl">
                    </div>

                    <div class="mt-8 flex flex-col justify-between">
                        <header>
                            <div class="space-x-2">
                                <a href="#"
                                   class="px-3 py-1 border border-blue-300 rounded-full text-blue-300 text-xs uppercase font-semibold"
                                   style="font-size: 10px">Techniques</a>
                                <a href="#"
                                   class="px-3 py-1 border border-red-300 rounded-full text-red-300 text-xs uppercase font-semibold"
                                   style="font-size: 10px">Updates</a>
                            </div>

                            <div class="mt-4">
                                <h1 class="text-3xl">
                                    This is a big title and it will look great on two or even three lines. Wooohoo!
                                </h1>

                                <span class="mt-2 block text-gray-400 text-xs">
                                    Published <time>1 day ago</time>
                                </span>
                            </div>
                        </header>

                        <div class="text-sm mt-4">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                                ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>

                            <p class="mt-4">
                                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                            </p>
                        </div>

                        <footer class="flex justify-between items-center mt-8">
                            <div class="flex items-center text-sm">
                                <img src="/images/lary-avatar.svg" alt="Lary avatar">
                                <div class="ml-3">
                                    <h5 class="font-bold">Lary Laracore</h5>
                                    <h6>Mascot at Laracasts</h6>
                                </div>
                            </div>

                            <div>
                                <a href="#"
                                   class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8"
                                >Read More</a>
                            </div>
                        </footer>
                    </div>
                </div>
            </article>
```

Se crea otra vista con el nombre de `post-featured-card.blade`

```html
<article
            class="transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl">
            <div class="py-6 px-5 lg:flex">
                <div class="flex-1 lg:mr-8">
                    <img src="/images/illustration-1.png" alt="Blog Post illustration" class="rounded-xl">
                </div>

                <div class="flex-1 flex flex-col justify-between">
                    <header class="mt-8 lg:mt-0">
                        <div class="space-x-2">
                            <a href="#"
                               class="px-3 py-1 border border-blue-300 rounded-full text-blue-300 text-xs uppercase font-semibold"
                               style="font-size: 10px">Techniques</a>

                            <a href="#"
                               class="px-3 py-1 border border-red-300 rounded-full text-red-300 text-xs uppercase font-semibold"
                               style="font-size: 10px">Updates</a>
                        </div>

                        <div class="mt-4">
                            <h1 class="text-3xl">
                                This is a big title and it will look great on two or even three lines. Wooohoo!
                            </h1>

                            <span class="mt-2 block text-gray-400 text-xs">
                                    Published <time>1 day ago</time>
                                </span>
                        </div>
                    </header>

                    <div class="text-sm mt-2">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                            ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                            ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>

                        <p class="mt-4">
                            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                        </p>
                    </div>

                    <footer class="flex justify-between items-center mt-8">
                        <div class="flex items-center text-sm">
                            <img src="/images/lary-avatar.svg" alt="Lary avatar">
                            <div class="ml-3">
                                <h5 class="font-bold">Lary Laracore</h5>
                                <h6>Mascot at Laracasts</h6>
                            </div>
                        </div>

                        <div class="hidden lg:block">
                            <a href="#"
                               class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8"
                            >Read More</a>
                        </div>
                    </footer>
                </div>
            </div>
        </article>
```

Y la ultima vista para el `header`, con el nombre de `_posts-header.blade.php`

    <header class="max-w-xl mx-auto mt-20 text-center">
        <h1 class="text-4xl">
            Latest <span class="text-blue-500">Laravel From Scratch</span> News
        </h1>

        <h2 class="inline-flex mt-2">By Lary Laracore <img src="/images/lary-head.svg"
                                                        alt="Head of Lary the mascot"></h2>

        <p class="text-sm mt-14">
            Another year. Another update. We're refreshing the popular Laravel series with new content.
            I'm going to keep you guys up to speed with what's going on!
        </p>

        <div class="space-y-2 lg:space-y-0 lg:space-x-4 mt-8">
            
            <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">
                <select class="flex-1 appearance-none bg-transparent py-2 pl-3 pr-9 text-sm font-semibold">
                    <option value="category" disabled selected>Category
                    </option>
                    <option value="personal">Personal</option>
                    <option value="business">Business</option>
                </select>

                <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
                    height="22" viewBox="0 0 22 22">
                    <g fill="none" fill-rule="evenodd">
                        <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                        </path>
                        <path fill="#222"
                            d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                    </g>
                </svg>
            </div>

        
            <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">
                <select class="flex-1 appearance-none bg-transparent py-2 pl-3 pr-9 text-sm font-semibold">
                    <option value="category" disabled selected>Other Filters
                    </option>
                    <option value="foo">Foo
                    </option>
                    <option value="bar">Bar
                    </option>
                </select>

                <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
                    height="22" viewBox="0 0 22 22">
                    <g fill="none" fill-rule="evenodd">
                        <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                        </path>
                        <path fill="#222"
                            d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                    </g>
                </svg>
            </div>

            <!-- Search -->
            <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl px-3 py-2">
                <form method="GET" action="#">
                    <input type="text" name="search" placeholder="Find something"
                        class="bg-transparent placeholder-black font-semibold text-sm">
                </form>
            </div>
        </div>
    </header>

## Blade Components and CSS Grids

Este capitulo, se aprende a utilizar los CSS grids, cambiamos algunos elementos desde la barra de herramientas del navegador y se modificará código de las vistas.

Se modifica código de las vistas para colocar nuestras direcciones de rutas y atributos del modelo, para mostrar la información de acuerdo a las propiedades establecidas en la base de datos.

`posts.blade.php`

```html
<x-layout>

    @include('_posts-header')

    <main class="max-w-6xl mx-auto mt-6 lg:mt-20 space-y-6">

        @if ($posts->count())
        <x-post-featured-card :post="$posts[0]"/>

         @if ($posts->count() > 1)

        <div class="lg:grid lg:grid-cols-6">
            @foreach ( $posts->skip(1) as $post)
            <x-post-card
            :post="$post"
            class="{{ $loop->iteration < 3 ? 'col-span-3' : 'col-span-2' }}"
            />
            @endforeach
        </div>
        @endif

        @else

        <p class="text-center"> No posts yet. Please check back later. </p>

        @endif

    </main>
</x-layout>
```

`post-card.blade.php`

```php

@props(['post'])

<article
        {{$attributes->merge(['class' => 'transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl'])}}>
                <div class="py-6 px-5">
                    <div>
                        <img src="/images/illustration-3.png" alt="Blog Post illustration" class="rounded-xl">
                    </div>

                    <div class="mt-8 flex flex-col justify-between">
                        <header>
                            <div class="space-x-2">
                                <a href="/categories/{{$post->category->slug}}"
                                    class="px-3 py-1 border border-red-300 rounded-full text-red-300 text-xs uppercase font-semibold"
                                    style="font-size: 10px"> {{$post->category->name}}</a>

                            </div>

                            <div class="mt-4">
                                <h1 class="text-3xl">
                                    <a href="/posts/{{$post->slug}}">
                                        {{$post->title}}
                                    </a>
                                </h1>

                                <span class="mt-2 block text-gray-400 text-xs">
                                    Published <time>{{$post->created_at->diffForHumans()}}</time>
                                </span>
                            </div>
                        </header>

                        <div class="text-sm mt-4">
                            <p>
                                {{$post->excerpt}}
                            </p>


                        </div>

                        <footer class="flex justify-between items-center mt-8">
                            <div class="flex items-center text-sm">
                                <img src="/images/lary-avatar.svg" alt="Lary avatar">
                                <div class="ml-3">
                                    <h5 class="font-bold">{{$post->author->name}}</h5>
                                    <h6>Mascot at Laracasts</h6>
                                </div>
                            </div>

                            <div>
                                <a href="/posts/{{$post->slug}}"
                                   class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8"
                                >Read More</a>
                            </div>
                        </footer>
                    </div>
                </div>
            </article>
```

`post-featured-card.blade.php`

```php
@props(['post'])

<article
            class="transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl">
            <div class="py-6 px-5 lg:flex">
                <div class="flex-1 lg:mr-8">
                    {{--TODO--}}
                    <img src="/images/illustration-1.png" alt="Blog Post illustration" class="rounded-xl">
                </div>

                <div class="flex-1 flex flex-col justify-between">
                    <header class="mt-8 lg:mt-0">
                        <div class="space-x-2">


                            <a href="/categories/{{$post->category->slug}}"
                               class="px-3 py-1 border border-red-300 rounded-full text-red-300 text-xs uppercase font-semibold"
                               style="font-size: 10px"> {{$post->category->name}}</a>
                        </div>

                        <div class="mt-4">
                            <h1 class="text-3xl">
                                <a href="/posts/{{$post->slug}}">
                                    {{$post->title}}
                                </a>
                            </h1>

                            <span class="mt-2 block text-gray-400 text-xs">
                                    Published <time>{{$post->created_at->diffForHumans()}}</time>
                                </span>
                        </div>
                    </header>

                    <div class="text-sm mt-2">
                        <p>
                            {{$post->excerpt}}
                        </p>


                    </div>

                    <footer class="flex justify-between items-center mt-8">
                        <div class="flex items-center text-sm">
                            <img src="/images/lary-avatar.svg" alt="Lary avatar">
                            <div class="ml-3">
                                <h5 class="font-bold">{{$post->author->name}}</h5>
                                <h6>Mascot at Laracasts</h6>
                            </div>
                        </div>

                        <div class="hidden lg:block">
                            <a href="/posts/{{$post->slug}}"
                               class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8"
                            >Read More</a>
                        </div>
                    </footer>
                </div>
            </div>
        </article>
```

## Convert the Blog Post Page

Despues de crear y modificar la pagina de inicio, se comienza a dar estilo y formato a las vistas de Post, para esto agregaremos mas vistas y modificares las vista `post.blade.php`

Para la vista `post.blade.php`, se modificara el código que teniamos ya en ella, de la siguiente forma. (Agregamos estilo css y html ---- Además, se agregan la rutas y atributos de los modelos del proyecto)

```html
<x-layout>

    <section class="px-6 py-8">

        <main class="max-w-6xl mx-auto mt-10 lg:mt-20 space-y-6">
            <article class="max-w-4xl mx-auto lg:grid lg:grid-cols-12 gap-x-10">
                <div class="col-span-4 lg:text-center lg:pt-14 mb-10">
                    <img src="/images/illustration-1.png" alt="" class="rounded-xl">

                    <p class="mt-4 block text-gray-400 text-xs">
                        Published <time>{{$post->created_at->diffForHumans()}}</time>
                    </p>

                    <div class="flex items-center lg:justify-center text-sm mt-4">
                        <img src="/images/lary-avatar.svg" alt="Lary avatar">
                        <div class="ml-3 text-left">
                            <h5 class="font-bold">{{$post->author->name}}</h5>
                            <h6>Mascot at Laracasts</h6>
                        </div>
                    </div>
                </div>

                <div class="col-span-8">
                    <div class="hidden lg:flex justify-between mb-6">
                        <a href="/"
                            class="transition-colors duration-300 relative inline-flex items-center text-lg hover:text-blue-500">
                            <svg width="22" height="22" viewBox="0 0 22 22" class="mr-2">
                                <g fill="none" fill-rule="evenodd">
                                    <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                                    </path>
                                    <path class="fill-current"
                                        d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z">
                                    </path>
                                </g>
                            </svg>

                            Back to Posts
                        </a>

                        <div class="space-x-2">
                            <x-category-button :category="$post->category" />

                        </div>
                    </div>

                    <h1 class="font-bold text-3xl lg:text-4xl mb-10">

                            {{$post->title}}

                    </h1>

                    <div class="space-y-4 lg:text-lg leading-loose">
                        {{$post->body}}
                    </div>
                </div>
            </article>
        </main>
    </section>

</x-layout>
```

Creamos una nueva vista, llamada `category-button.blade.php`, en la cual agregaremos el codigo de `Category` para tener todo debidamente ordenado e implementar las buenas practicas de la programación.

```php
@props(['category'])

<a href="/categories/{{ $category->slug}}"
    class="px-3 py-1 border border-red-300 rounded-full text-red-300 text-xs uppercase font-semibold"
    style="font-size: 10px"
    > {{ $category->name}}</a>
```

## A Small JavaScript Dropdown Detour

A continuación, se deberá hacer que el menú desplegable "Categorías" en la página de inicio funcione como se esperaba. Para esto se  necesitará JavaScript para que funcione, así, que usaremos la biblioteca Alpine.js.

Para utilizar la biblioteca `Alpine.js`, ingresamos al enlace `https://github.com/alpinejs/alpine/tree/v2.8.2` y copiamos la linea:

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

La cual se debe colocar en la vista `layout.blade.php`.

Luego modificamos la vista `_posts-header.blade.php`, donde se encuentra el select de `Categoria`, ahi ingresará el nuevo código para crear el menu despleglable de Categoria.

```html
<div class="space-y-2 lg:space-y-0 lg:space-x-4 mt-8">
        <!--  Category -->
        <div class="relative  lg:inline-flex bg-gray-100 rounded-xl">

            <div x-data="{show: false}" @click.away="show = false">

                <button @click="  show = ! show "
                    class="py-2 pl-3 pr-9 text-sm font-semibold w-32 text-left lg:inline-flex">

                    {{ isset($currentCategory) ? ucwords($currentCategory->name): 'Categories' }}

                    <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
                        height="22" viewBox="0 0 22 22">
                        <g fill="none" fill-rule="evenodd">
                            <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                            </path>
                            <path fill="#222"
                                d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                        </g>
                    </svg>


                </button>
                <div x-show="show" class="py-2 absolute bg-gray-100  mt-2 rounded-xl w-full z-50" style="display: none">
                    <a href="/"
                        class="block text-left px-3 text-sm leading-6 hover:bg-blue-500
                        hover:bg-blue-500 hover:text-white focus:text-white"> All</a>


                    @foreach ($categories as $category)
                        <a href="/categories/{{$category->slug}}"
                            class="block text-left px-3 text-sm leading-6 hover:bg-blue-500
                            hover:bg-blue-500 hover:text-white focus:text-white
                            {{ isset($currentCategory) && $currentCategory->is($category) ? 'bg-blue-500 text-white': ''}}
                            ">{{ucwords($category->name)}}</a>
                    @endforeach
                </div>
            </div>


        </div>
```

Y por último, modificamos las rutas, para que cargue correctamente, se modifica la route `web.php`

```php
Route::get('categories/{category:slug}', function (Category $category) {

    return view('posts', [
        'posts' => $category->posts,
        'currentCategory'=> $category,
        'categories'=> Category ::all()
    ]);
});
```

Se agrega la ultima linea `'categories'=> Category ::all()`, a cada unas de las rutas.

## How to Extract a Dropdown Blade Component

Despues de haber creado con éxito la funcionalidad básica para un menú desplegable, lo vamos hacer que sea reutilizable. Para esto vamos a extraer el componente Blade x-dropdown. Esto tendrá el efecto secundario de aislar todo el código específico de Alpine en ese único archivo de componente.

Se crean varias vistas para poder separar el código, de acuerdo, a su componentes y funcionalidades, así poder reutizarlo.

Creamos los componentes para las vistas, `dropdown-blade.php`, `dropdown-item.blade.php`, `icon.blade.php` y modificamos `_posts-header.blade.php`

Para `dropdown-blade.php` el código, quedaria de la siguiente forma

 ```php
 @props(['trigger'])

<div class="relative  lg:inline-flex bg-gray-100 rounded-xl">

    <div x-data="{ show: false }" @click.away="show = false">
        {{-- Trigger --}}

        <div @click="show = ! show ">
            {{ $trigger }}

        </div>

        {{-- Links --}}
        <div x-show="show" class="py-2 absolute bg-gray-100  mt-2 rounded-xl w-full z-50" style="display: none">
            {{ $slot }}
        </div>
    </div>
```

Para `dropdown-item.blade.php` el código seria:

```php
@props(['active' => false])

@php
    $classes = 'block text-left px-3 text-sm leading-6 hover:bg-blue-500
hover:bg-blue-500 hover:text-white focus:text-white';
    
    if ($active) {
        $classes .= 'bg-blue-500 text-white';
    }
    
@endphp

<a {{ $attributes(['class' => $classes]) }}>
    {{ $slot }}</a>
```

Para `icon.blade.php` el código seria:

```php
@props(['name'])

@if ($name === 'down-arrow')
    <svg {{ $attributes(['class' => 'transform -rotate-90']) }} width="22" height="22" viewBox="0 0 22 22">
        <g fill="none" fill-rule="evenodd">
            <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
            </path>
            <path fill="#222" d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
        </g>
    </svg>
@endif

```

Modificamos `_posts-header.blade.php`

```php
<!--  Category -->

        <x-dropdown>

            <x-slot name="trigger">

                <button class="py-2 pl-3 pr-9 text-sm font-semibold w-32 text-left lg:inline-flex">

                    {{ isset($currentCategory) ? ucwords($currentCategory->name) : 'Categories' }}

                    <x-icon name="down-arrow" class="absolute pointer-events-none" style="right: 12px;" />

                </button>
            </x-slot>

            <x-dropdown-item href="/" :active="request()->routeIs('home')">All</x-dropdown-item>

            @foreach ($categories as $category)
                {{-- {{ isset($currentCategory) && $currentCategory->is($category) ? 'bg-blue-500 text-white' : '' }} --}}

                <x-dropdown-item href="/categories/{{ $category->slug }}" :active='request()->is("categories/{$category->slug}")'>
                    {{ ucwords($category->name) }}</x-dropdown-item>
            @endforeach

        </x-dropdown>
```

Y por último modificamos las rutas, en `web.php`

```php
Route::get('/', function () {

    return view('posts', [
        'posts' => Post::latest()->with(['category', 'author'])->get(),
        'categories' => Category::all()
    ]);
})->name('home');


Route::get('categories/{category:slug}', function (Category $category) {

    return view('posts', [
        'posts' => $category->posts,
        'currentCategory' => $category,
        'categories' => Category::all()
    ]);
})->name('category');
```

## Quick Tweaks and Clean-Up

En este capitulo se hace una limpieza rápida, se refresca la base de datos, para esto haremos lo siguiente.

Modificamos las dos últimas lineas de la función en `factories` `PostFactorory.php`, quedando de la siguiente forma:

```php
public function definition()
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'excerpt' => '<p>' . implode('</p><p>', $this->faker->paragraphs(2)) . '</p>',
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(6)) . '</p>',
        ];
    }
```

Se modifica `CategoryFactory.php`, igual que a la función anterior

```php
public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'slug' => $this->faker->unique()->slug
        ];
    }
```

Se refresca la base de datos con el comando

    php artisan migrate:fresh 

Y se ingresa `php artisan tinker`, para crear nuevos `Post`

    App\Models\Post::factory(30)->create();

Luego modificamos las vistas donde de encuentre `excerpt` y `body`, con comodines para que no muestre el código html, de la siguiente forma.

```php
 <div class="text-sm mt-2 space-y-4">{!! $post->excerpt !!}</div>

 <div class="space-y-4 lg:text-lg leading-loose">{!! $post->body !!}</div>

```
Para las vistas `post-card.blade.php`, `post-featured-card.blade.php` y `post.blade.php`
