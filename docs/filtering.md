[<-Go back](/README.md)

# Filtering

## Advanced Eloquent Query Constraints

Para este episodio se sigue trabajando la  función `filtro` del modelo `Post.php`, con el proposito de poder filtrar publicaciones según su categoria.

Se agrega código a la función de `scopeFilter` en el modelo `Post.php`, que  va a filtrar publicaciones por categoria.

    $query->when($filters['category'] ?? false, fn($query, $category) =>
                $query->whereHas('category', fn ($query) =>
                    $query->where('slug', $category)
                )
            );
Quedando la función completa de está forma.

```php
 public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('body', 'like', '%' . $search . '%'));
        $query->when($filters['category'] ?? false, fn($query, $category) =>
            $query->whereHas('category', fn ($query) =>
                $query->where('slug', $category)
            )
        );
    }
```

Se elimina la siguiente función que busca publicaciones por Categorias, en la carpetas de  rutas, el archivo `web.php`

```php
Route::get('categories/{category:slug}', function (Category $category) {

    return view('posts', [
        'posts' => $category->posts,
        'currentCategory' => $category,
        'categories' => Category::all()
    ]);
})->name('category');
```

## Extract a Category Dropdown Blade Component

¿Has notado que cada ruta necesita pasar una colección de categorías a la vista de publicaciones? Si echa un vistazo, esa variable solo se menciona como parte del menú desplegable de la categoría principal. Para este capitulo se va a crear un vista y componente dedicado solamente al funcionamiento de `Categoria`, logrando un código mas automatizado y reutilizable.

Creamos nuevo componeten llamado `CategoryDropdown.php`

    php artisan make:component CategoryDropdown

Modificamos la función `render` y se importa la clase `Category`

```php
 public function render()
    {
        return view('components.category-dropdown', [
            'categories' => Category::all(),
            'currentCategory' => Category::firstWhere('slug', request('category'))
        ]);
    }
```

Creamos una vista para el menu desplegable de Categorias, llamada `category-dropdown.blade.php` y movemos el código que se encontraba en `_header.blade.php` del menu desplegable.

```html
<x-dropdown>
    <x-slot name="trigger">
        <button class="py-2 pl-3 pr-9 text-sm font-semibold w-full lg:w-32 text-left flex lg:inline-flex">
            {{ isset($currentCategory) ? ucwords($currentCategory->name) : 'Categories' }}

            <x-icon name="down-arrow" class="absolute pointer-events-none" style="right: 12px;"/>
        </button>
    </x-slot>

    <x-dropdown-item href="/" :active="request()->routeIs('home')">All</x-dropdown-item>

    @foreach ($categories as $category)
        <x-dropdown-item
            href="/?category={{ $category->slug }}"
            :active='request()->is("categories/{$category->slug}")'
        >{{ ucwords($category->name) }}</x-dropdown-item>
    @endforeach
</x-dropdown>
```

Modificamos los `return` de cada una de las funciones en `PostController.php`

```php
public function index()
    {
        return view('posts.index', [
            'posts' => Post::latest()->filter(request(['search', 'category', 'author']))->get()

        ]);
    }

    public function show(Post $post)
    {
        return view('posts.show', [
            'post' => $post
        ]);
    }
```

Se crea una nueva carpeta en vistas, llamada `posts`, donde vamos a mover las vistas `posts.blade.php`, `post.blade.php` y `_posts-header.blade.php` y las renombramos

    `posts.blade.php` ---------- index.blade.php
    `post.blade.php` ----------- show.blade.php
    `_posts-header.blade.php`--- _header.blade.php

Recordar cambiar rutas en la vistas, en caso de ser necesario, por ejemplo:

    @include('posts._header') ---- en index.blade.php

    En show.blade.php
    <h5 class="font-bold">
    <a href="/?author={{ $post->author->username }}">{{ $post->author->name }}</a>
    </h5>
        
## Author Filtering

A continuación, agreguemos soporte para filtrar publicaciones según su autor respectivo. Con esto ahora podremos ordenar fácilmente todas las publicaciones por categoría, autor, texto de búsqueda o todo lo anterior. Para poder lograrlo hacermos una serie de modificaciones y agregamos código. 

Primero agregamos las directrices de busqueda de autor correspondientes en el modelo `Post.php`, la función `scopeFilter`. 

    $query->when($filters['author'] ?? false, fn($query, $author) =>
                $query->whereHas('author', fn ($query) =>
                    $query->where('username', $author)
                )
            );

Quedando de la siguiente forma 

```php
public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('body', 'like', '%' . $search . '%')
        );

        $query->when($filters['category'] ?? false, fn($query, $category) =>
            $query->whereHas('category', fn ($query) =>
                $query->where('slug', $category)
            )
        );

        $query->when($filters['author'] ?? false, fn($query, $author) =>
            $query->whereHas('author', fn ($query) =>
                $query->where('username', $author)
            )
        );
    }
```

Luego modificamos la ruta en las vistas html que nos muestra el `nombre del autor`. Como `post-featured-card.blade.php`, `post-card.blade.php` y `show.blade.php`

```html
<h5 class="font-bold">
    <a href="/?author={{ $post->author->username }}">{{ $post->author->name }}</a>
</h5>
```

Y eliminamos la routa en `web.php`, acerca de autores. 

```php
Route::get('authors/{author:username}', function (User $author) {
    return view('posts', [
        'posts' => $author->posts,
        'categories' => Category::all()
    ]);
});
```

## Merge Category and Search Queries

A continuación, debemos actualizar tanto el menú desplegable de categorías como la entrada de búsqueda para incluir todos los parámetros de cadena de consulta existentes y relevantes. En este momento, si estamos navegando en una determinada categoría, tan pronto como realicemos una búsqueda, esa categoría actual volverá a "Todos". Para arreglar esto, hacemos lo siguiente: 

Agregamos el codigo a la vista `category-dropdown.blade.php`, antes del `foreach` de categorias.

```html
<x-dropdown-item href="/?{{ http_build_query(request()->except('category', 'page')) }}"
        :active="request()->routeIs('home')">All
</x-dropdown-item>
```

Que al final se muestra así, 

    <x-dropdown>
        <x-slot name="trigger">
            <button class="py-2 pl-3 pr-9 text-sm font-semibold w-full lg:w-32 text-left flex lg:inline-flex">
                {{ isset($currentCategory) ? ucwords($currentCategory->name) : 'Categories' }}

                <x-icon name="down-arrow" class="absolute pointer-events-none" style="right: 12px;"/>
            </button>
        </x-slot>

        <x-dropdown-item href="/?{{ http_build_query(request()->except('category', 'page')) }}"
            :active="request()->routeIs('home')">All
        </x-dropdown-item>

        @foreach ($categories as $category)
            <x-dropdown-item
            href="/?category={{ $category->slug }}&{{ http_build_query(request()->except('category', 'page')) }}"
                :active='request()->is("categories/{$category->slug}")'
            >{{ ucwords($category->name) }}</x-dropdown-item>
        @endforeach
    </x-dropdown>


Luego a la vista `_header.blade.php` modificamos el formulario de busqueda[search], con las siguientes lineas. 

```php
 <form method="GET" action="/">
    @if (request('category'))
         <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
```
