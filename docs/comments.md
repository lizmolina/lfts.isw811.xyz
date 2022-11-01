[<-Go back](/README.md)

# Comments

## Write the Markup for a Post Comment

Pasemos ahora a publicar comentarios. Comenzaremos con el marcado base para un comentario. Para esto creamos una nueva vista en `components` con el nombre `post-comment.blade.php`, y el siguiente contenido

```html
<article class="flex bg-gray-100 border border-gray-200 p-6 rounded-xl space-x-4">
    <div class="flex-shrink-0">
        <img src="https://i.pravatar.cc/60" alt="" width="60" height="60" class="rounded-xl">
    </div>

    <div>
        <header class="mb-4">
            <h3 class="font-bold">John Doe</h3>

            <p class="text-xs">
                Posted
                <time>8 months ago</time>
            </p>
        </header>

        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi viverra vehicula nisl
            eget blandit. Mauris hendrerit accumsan est id cursus. Ut sed elit at ligula tempor
            porta sed sed odio.
        </p>
    </div>
</article
```

Luego agregamos directiva en la vista `show.blade.php" para mostrar en pantalla los comentarios

```php
 <section class="col-span-8 col-start-5 mt-10 space-y-6">
                    <x-post-comment />
                    <x-post-comment />
                    <x-post-comment />
                    <x-post-comment />
</section>
```

## Table Consistency and Foreign Key Constraints

A continuación, vamos a construir la migración y la tabla correspondiente para nuestros comentarios. Esto nos dará la oportunidad de discutir más profundamente las restricciones de clave externa.

Para este episodio lo primero que hacemos es crear el modelo de `Coments`, asi como correr sus respectivas migraciones. De mismo modo, cuando utilizamos migraciones, se realiza de la maquina virtual de `vagrant`.

    php artisan make:model Comment -mfc

Despues, modificamos la migración creada nueva con el nombre `create_comments_table.php`, agregando nuevos atributos al `Schema`.

```php
  public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }
```

Y modificamos la migración `create_posts_table.php`, en el atributo `user`, del `Schema`

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

## Make the Comments Section Dynamic

Con la tabla de comentarios lista, cambiemos y construyamos los atributos necesarios para nuestra CommentFactory. Una vez completado, volveremos a nuestra página de publicación y haremos que la sección de comentarios, haciendo al mismo tiempo los cambios en la base de datos.

Comenzamos con agregar las funciones `post()` y `author()` al modelo `Comment.php`.

```php
 public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
```

Despues, agregamos la función `comment()` al modelo de `Post.php`.

```php
public function comments()
    {
        return $this->hasMany(Comment::class);
    }
```

Ahora, iniciamos a modificar y agregar el código a los `factories`, primero iniciares en definir el `Post` y `User` en `CommentFactory.php`

```php
public function definition()
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'body' => $this->faker->paragraph()
        ];
    }
```

Modificamos `CategoryFactory.php`, en los atributos de definición.

```php
  public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'slug' => $this->faker->unique()->slug()
        ];
    }
```

También, modificamos `PostFactory.php`, en `title` y `slug`, quedando de la siguiente forma

    'title' => $this->faker->sentence(),
    'slug' => $this->faker->slug(),

Y por último, `UserFactory.php`, modificamos al final, agregando `()`, de los siguientes atributos.

    'name' => $this->faker->name(),
    'username' => $this->faker->unique()->userName(),
    'email' => $this->faker->unique()->safeEmail(),

Luego se modifican las vistas, en este caso sería la vista de comentarios, `post-comment.blade.php`, las siguientes lineas

```html
        <img src="https://i.pravatar.cc/60?u={{ $comment->id }}" alt="" width="60" height="60" class="rounded-xl">
        
        <h3 class="font-bold">{{ $comment->author->username }}</h3>

        <time>{{ $comment->created_at }}</time>

         {{ $comment->body }}

```

Junto los vista del `post`, `show.blade.php`, directamente donde llamamos a la vista de comentarios.

```html
  <section class="col-span-8 col-start-5 mt-10 space-y-6">
        @foreach ($post->comments as $comment)
             <x-post-comment :comment="$comment" />
        @endforeach
  </section>
```

Luego para hacer modificaciones en la base datos, agregar nuevos comentarios, ejecutamos los siguientes comandos.

    php artisan tinker
    $post= App\Models\Post::latest()->first(); ---Ver el id del primer post
    App\Models\Comment::factory(10)->create(['post_id' => 18]);  --- crear comentarios al id especifico. 
    App\Models\Comment::factory(10)->create(); ---crear comentarios a post ramdon

## Design the Comment Form

En este episodio se crea un formulario para permitir que cualquier usuario autenticado pueda comentar en las publicaciones. Para esto haremos lo siguiente:

En la vista `show.blade.php`, se crea el formulario para comentar

```html
<x-panel>
                        <form method="POST" action="#">
                            @csrf

                            <header class="flex items-center">
                                <img src="https://i.pravatar.cc/60?u={{ auth()->id() }}"
                                     alt=""
                                     width="40"
                                     height="40"
                                     class="rounded-full">

                                <h2 class="ml-4">Want to participate?</h2>
                            </header>

                            <div class="mt-6">
                                <textarea
                                    name="body"
                                    class="w-full text-sm focus:outline-none focus:ring"
                                    rows="5"
                                    placeholder="Quick, thing of something to say!"
                                    required>
                                </textarea>


                            </div>

                            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                                <button type="submit"
                                 class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600">Post</button>
                            </div>
                        </form>

                    </x-panel>
```

Creamos una nueva vista en `components`, con el nombre `panel.blade.php`

```html
<div {{ $attributes(['class' => 'border border-gray-200 p-6 rounded-xl']) }}>
    {{ $slot }}
</div>

Y por último identamos el código de la vista `post-comment.blade.php` dentro del `panel.blade.php`, mostrando de así

```html
@props(['comment'])

<x-panel class="bg-gray-50">
    <article class="flex space-x-4">
        <div class="flex-shrink-0">
            <img src="https://i.pravatar.cc/60?u={{ $comment->user_id }}" alt="" width="60" height="60" class="rounded-xl">
        </div>

        <div>
            <header class="mb-4">
                <h3 class="font-bold">{{ $comment->author->username }}</h3>

                <p class="text-xs">
                    Posted
                    <time>{{ $comment->created_at }}</time>
                </p>
            </header>

            <p>
                {{ $comment->body }}
            </p>
        </div>
    </article>
</x-panel>
```

## Activate the Comment Form

Con el formulario de comentarios diseñado, agregamos la lógica para activarlo. Para este seguiremos una serie de pasos. 

Creamos un controlador para los comentarios de las publicaciones, `PostCommentsController.php`

    php artisan make:controller PostCommentsController

Agregamos la función de `validate()` y `create()` nuevos comentarios 

```php
 public function store(Post $post)
    {
        request()->validate([
            'body' => 'required'
        ]);

        $post->comments()->create([
            'user_id' => request()->user()->id,
            'body' => request('body')
        ]);

        return back();
    }
```

Modificamos la vista donde se encuentra el formulario de comentarios `show.blade.php`, para agregar la ruta y las directivas de logueo, para poder comentar. 

```html
@auth
                       
                    <x-panel>
                        <form method="POST" action="/posts/{{ $post->slug }}/comments">
                            @csrf

                            <header class="flex items-center">
                                <img src="https://i.pravatar.cc/60?u={{ auth()->id() }}" alt="" width="40"
                                    height="40" class="rounded-full">

                                <h2 class="ml-4">Want to participate?</h2>
                            </header>

                            <div class="mt-6">
                                <textarea name="body" class="w-full text-sm focus:outline-none focus:ring" rows="5"
                                    placeholder="Quick, thing of something to say!" required>
                                </textarea>


                            </div>

                            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                                <button type="submit"
                                    class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600">Post</button>
                            </div>
                        </form>

                    </x-panel>
                     
                    
                @else
                    <p class="font-semibold">
                        <a href="/register" class="hover:underline">Register</a> or
                        <a href="/login" class="hover:underline">log in</a> to leave a comment.
                    </p>
                @endauth
```

Le damos formato a la fecha de publicación en la vista `post-comment.blade.php`

    <time>{{ $comment->created_at->format('F j, Y, g:i a') }}</time>

Agregamos la ruta para crear nuevos comentarios en `web.php`

```php
Route::post('posts/{post:slug}/comments', [PostCommentsController::class, 'store']);
```

Se modifica el `AppServiceProvider.php` en Providers. Se desabilita temporalmente la protección de asignación  masiva. 

```php
public function boot()
    {
        Model::unguard();
    }
```

##  Some Light Chapter Clean Up

Extraeremos un par de componentes de Blade, crearemos una inclusión de PHP y luego reformatearemos manualmente los bits de nuestro código.

Aca se desarrolla un código limpio y reutilizable, para esto creamos las vistas para los diferentes componentes y los implementamos en la vista principal de la publicación. El funcionamiento sigue igual. 

Creamos un vista el formulario de comentarios en la carpeta de `posts`, con el nombre de `_add-comment-form.blade.php`

```html
@auth
    <x-panel>
        <form method="POST" action="/posts/{{ $post->slug }}/comments">
            @csrf

            <header class="flex items-center">
                <img src="https://i.pravatar.cc/60?u={{ auth()->id() }}"
                     alt=""
                     width="40"
                     height="40"
                     class="rounded-full">

                <h2 class="ml-4">Want to participate?</h2>
            </header>

            <div class="mt-6">
                <textarea
                    name="body"
                    class="w-full text-sm focus:outline-none focus:ring"
                    rows="5"
                    placeholder="Quick, thing of something to say!"
                    required></textarea>

                @error('body')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                <x-submit-button>Post</x-submit-button>
            </div>
        </form>
    </x-panel>
@else
    <p class="font-semibold">
        <a href="/register" class="hover:underline">Register</a> or
        <a href="/login" class="hover:underline">log in</a> to leave a comment.
    </p>
@endauth
```

Agregamos un nuevo componentes en la vistas para el botón, `submit-button.blade.php`

```html
<button type="submit"
        class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600"
>
    {{ $slot }}
</button>
```

Y en la vista de la publicación `show.blade.php`, llamamos la directivas de las vistas creadas anteriormente. 

```html
 <section class="col-span-8 col-start-5 mt-10 space-y-6">

    @include('posts._add-comment-form')             

       @foreach ($post->comments as $comment)
            <x-post-comment :comment="$comment" />
       @endforeach
 </section>
```



