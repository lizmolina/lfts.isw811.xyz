[<-Go back](/README.md)

# Blade

## Blade: The Absolute Basics

Apreder de Blade, un motor de plantillas para las vista de Laravel.Se modifica las vistas, principalmente cambiando ´php <?= ´ por ´{{ }}´. Por ejemplo=

´´´php
{{$post->title}}

   <div>
        {!! $post->body !!}
    </div>
´´´
  ----

´´´php
@foreach ($posts as $post)
    <article>
        <h1>
        <a href="/posts/{{$post->slug}}">
{{$post->title }}

        </a>
    </h1>


        <div>
            {{$post->excerpt}}
        </div>

    </article>

@endforeach
´´´

## Blade Layouts Two Ways

Cuando agregamos una nueva hoja de estilo, debemos actualizar cada vista. Así, que se enseñará la forma de crear diseños para reducir la duplicación en los archivos de diseño. Se agrega un nueva vista llamada ´layout.blade.php´ en folder llamado ´component´ en la carpeta de ´views´, con el siguiente contenido.

´´´php
<!DOCTYPE html>

<Title>My Blog</Title>
<link rel="stylesheet" href="/app.css">

<body>
    {{$slot}}

</body>
´´´

## A Few Tweaks and Consideration

Se elimina la restricción de ruta y se crea un nuevo metodo en el modelos ´Post´ llamado ´findOrFail´ que servirá para cancelar automaticamente cualquier publicación que no coincida con el slug dado.

´´´php
 public static function findOrFail($slug)
    {

        $post= static::find($slug);
        if(! $post){
            throw new ModelNotFoundException();
        }
        return $post;
    }

´´´
---

´´´php
Route::get('posts/{post}', function ($slug) {

    return view('post', [
        'post' => Post::findOrFail($slug)
    ]);
});
´´´
