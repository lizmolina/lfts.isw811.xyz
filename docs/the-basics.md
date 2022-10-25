[<-Go back](/README.md)

# The Basics

## How a Route Loads a View

A continuación cómo una ruta "escucha" un URI y luego carga una vista (o HTML) en respuesta. (Pasando parametros o recibiendo datos tipo json.)

``` php
Route::get('/hello', function () {
    return view('welcome');
});

```

``` php
Route::get('/', function () {
    return 'hello world';
});

```

``` php
Route::get('/', function () {
    return ['foo' => 'bar'];
});

```

## Include CSS and JavaScript

A continuación, veamos cómo incluir algunos activos genéricos de CSS y JavaScript. (Incluyendo css y javascript)

Creamos dos archivos en la carpeta public, uno `app.css` y el segundo `app.js`.

``` css
body{
    background:navy;
    color: white;

}
```

```javascript
alert('I am here');
```

Se modifica la vista `welcome.blade.php`, se agrega las referencias de los archivos anteriormente creados.

``` html
<!DOCTYPE html>

<Title>My Blog</Title>
<link rel="stylesheet" href="/app.css">
<script src= "/app.js"></script>

<body>
    <h1>Hello World</h1>   
</body>
```

## Make a Route and Link to it

Se crea una lista de publiciones del Blog, para luego vincularlas individualmente a una ruta que contiene las publicaciones completas. (Crear una ruta y vincularla)

Agregamos una nueva ruta en la carpeta routes `web.php`, y modificamos la anterior.

``` php
Route::get('/', function () {
    return view('posts');
});
```

``` php
Route :: get('post', function(){
    return view('post');

});
```

Se modifica el nombre de la vista `welcome.blade.php` por `posts.blade.php` y se modifica su contenido

``` html
<!DOCTYPE html>

<Title>My Blog</Title>
<link rel="stylesheet" href="/app.css">

<body>
    <article>
    <h1> <a href="/post">  My First Post </a></h1>
     <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit?
    </p>
    </article>

    <article>
    <h1> <a href="/post">  My Second Post </a></h1>
    <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit?
    </p>
    </article>

    <article>
    <h1> <a href="/post">  My Third Post </a></h1>
    <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit?
    </p>
    </article>
       
</body>

```

Se crea una nueva  vista  en la carpeta `views` con el nombre `post.blade.php` con el siguiente contenido

``` html
<body>
    <article>
    <h1> <a href="/post">  My First Post </a></h1>
     <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit?
    </p>
    </article>

    <a href="/">Go Back</a>

</body>

```

## Store Blog Posts as HTML Files

A continuación se va a almacenar cada publicación de blog dentro de su propio archivo HTML. Ademas, en nuestro archivo de rutas, podemos usar un comodín de ruta para determinar qué publicación debe recuperarse y pasarse a la vista. (Almacenar como archivos html).

Se crea un folder en la carpeta `resources` llamado `posts`, el se crean 3 archivos `.html` llamados

`my-first-post.html`
`my-second-post.html`
`my-third-post.html`

Con los siguientes contenidos, y el título segun corresponda

```html
<h1> <a href="/post">  My First Post </a></h1>     
<p>
   Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit?
</p>
```

Luego seguimos con el archivo `web.php`, de la carpeta de `routes`, en cual modicamos la siguiente ruta

```php
Route :: get('posts/{post}', function($slug){
    $path= __DIR__ . "/../resources/posts/{$slug}.html";

    if( ! file_exists($path)){
        return redirect('/'); 
        

    }
    $post= file_get_contents($path);

    return view('post', [
        'post' => $post
    ]);

});
```

Y por último se modifica la ruta de la vista `posts.blade.php`, para cada posts.

```html
<article>
    <h1> <a href="/posts/my-first-post">  My First Post </a></h1>
    
    <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit?
    </p>
 </article>
 ```

## Route Wildcard Constraints

A continuación se agrega una restricción a la ruta para garantizar que el `slug` de la publicación del blog se componga exclusivamente de cualquier combinación de letras, números, guiones y guiones bajos. (`Rentricciones de comodin de ruta`).

Se modifica  el archivo de rutas `web.php`, en la última linea.

```php
})->where('post','[A-z_\-]+');
```

## Use Caching for Expensive Operations

 A continuación se va a almacenar en caché el HTML de cada publicación para mejorar el rendimiento(`Utilizar el almacenamiento en caché para operaciones costosas`).

Se modifica  el archivo de rutas `web.php`, pero en la siguiente linea en los que se enfoca tema
`$post = cache()->remember("posts.{$slug}", 1200, fn()=> file_get_contents($path));`.

```php
Route :: get('posts/{post}', function($slug){
    
    if( ! file_exists($path= __DIR__ . "/../resources/posts/{$slug}.html")){
        return redirect('/');  

    }
    $post = cache()->remember("posts.{$slug}", 1200, fn()=> file_get_contents($path));

   
    return view('post', [
        'post' => $post
    ]);

})->where('post','[A-z_\-]+');

```

## Use the Filesystem Class to Read a Directory

Se obtiene y lee todas las publicaciones dentro del directorio de `resources/posts`. Una vez que tenemos una matriz adecuada, podemos recorrerlos y mostrar cada uno en la página principal de descripción general del blog. (`Usar la clase del sistema de archivos para leer un directorio`)

Se modifica  el archivo de rutas `web.php`

```php
Route::get('/', function () {
   
    return view('posts', [
        'posts' => Post::all()   
    ]);
});

```

Se modifica el archivo de vistas `posts.blade.php`

```php
<body> 
    <?php foreach ($posts as $post) : ?>
    <article>
        <?= $post;?>
    
    </article>
   <?php endforeach; ?> 
       
</body>
```

Se modifica cada uno de las vistas html en la carpeta `posts` del folder `resources`, con su respectiva información según el titulo.

```html
---
title: My Fourth Post
excerpt: Lorem ipsum dolor sit amet consectetur adipisicing elit.
date: 2021-05-21
---

<h1> My Fourth Post </h1>  
<p>
   Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorum blanditiis excepturi doloribus, saepe consectetur veritatis ab ea repellendus facere, nemo illum minima, porro iusto? Nostrum illo vitae culpa repellat praesentium!

</p>
```

Se agrega un nuevo modelo llamado `Post.php` en la carpeta `Models`, con el siguiente contenido.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;

class Post{

    public static function all(){
        $files= File::files(resource_path("posts/"));

        return array_map(fn($file) => $file->getContents(), $files);
 
    }

    public static function find($slug)
    {
        if( ! file_exists($path= resource_path("posts/{$slug}.html"))){
            throw new ModelNotFoundException();

        }
    
        return cache()->remember("posts.{$slug}", 1200, fn() => file_get_contents($path));
    }

}
```

## Find a Composer Package for Post Metadata

Se descarga un paquete de ´composer´ que nos ayude a leer el formato de metadatos ´Yaml Front Matter´.
con el siguiente comando ´ composer require spatie/yaml-front-matter´ en la maquina virtual de vangrant de lfts@isw811.xyz.

Se modifica el modelo llamado `Post.php` en la carpeta `Models`, con el siguiente contenido.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post{

    public $title;
    public $excerpt;
    public $date;
    public $body;
    public $slug;

    public function __construct($title, $excerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date= $date;
        $this->body= $body;
        $this->slug= $slug;
    }

    public static function all(){
        
        return collect(File::files(resource_path("posts")))
        ->map(fn($file)=>YamlFrontMatter::parseFile($file))
        ->map(fn($document)=>  new Post(
            $document->title,
            $document->excerpt,
            $document->date,
            $document->body(),
            $document->slug

        ));
        
    }

    public static function find($slug)
    {
        return static::all()->firstWhere('slug', $slug);

    }
```

## Collection Sorting and Caching Refresher

Se ordena el post de acuerdo a la fecha de publicación, administrar el almacenamiento del cache. Se modifica la primera y ultima linea de la funcion `all()` en el  modelo `Post.php`, con el siguiente código.

```php
<?php
public static function all(){
        return cache()->rememberForever('posts.all', function () {
            return collect(File::files(resource_path("posts")))
        ->map(fn($file)=>YamlFrontMatter::parseFile($file))
        ->map(fn($document)=>  new Post(
            $document->title,
            $document->excerpt,
            $document->date,
            $document->body(),
            $document->slug

        ))
        ->sortByDesc('date');



        });

    }
```
