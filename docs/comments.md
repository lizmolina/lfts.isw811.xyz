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


##
