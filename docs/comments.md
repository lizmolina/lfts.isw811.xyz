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

##
