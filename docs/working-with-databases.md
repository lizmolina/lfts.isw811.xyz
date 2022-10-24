[<-Go back](/README.md)

# Working With Databases

## Environment Files and Database Connections

Se analiza los aspectos esenciales de los archivos de entorno y luego pasaremos a la conexión a una base de datos MySQL. Se instala la base de datos mysql, se crea una base de datos llamada ´laravel´, con el usuario ´vagrant´ y la contraseña ´secret´, se corren la migraciones con el comando ´ṕhp artisan migrate´ en la máquina virtual vagrant de ´lfts.isw811.xyz´

Comandos para crear la base de datos 

    sudo mysql -- Conectarse a la base de datos.
    show databases; --  Ver las bases de datos creadas. 
    create database laravel; --Crear la base de datos.
    create user vagrant identified by 'secret'; --Crear usuario y contraseña.
    grant all privileges on laravel.* to vagrant; --Dar todos los privilegios al usuario sobre una base de dato
    flush privileges; --Recargar privilegios. 
    mysql -u vagrant -p --Conectarme a la base de datos con las credenciales creadas. 
    quit -- Salir de la base de datos.

Se edita el archivo `.env`.

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=vagrant
    DB_PASSWORD= secret


## Migrations: The Absolute Basics

Después de preparar el entorno de mysql se comenzará aprende de las clases de migraciones.
Se prueban los comandos:

    ṕhp artisan migrate:rollback ---revierte la última migración en la base de datos
    php artisan migrate ---crea la migración de un tabla en laravel
    php artisan migrate:fresh --actualiza la migraciones en la base de datos


## Eloquent and the Active Record Pattern

Ahora veremos Eloquent, que es la implementación de Active Record de Laravel, esto nos  permite asignar un registro de tabla de base de datos a un objeto Aprederemos a usar la API inicial.  Utilizamos ´php artisan tinker´ para crear usuarios, modificar modelos, y acceder a la tablas.

    php artisan tinker --- creamos y seleccionamos usuarios en las bases de datos. 
    $user = new App\Model\User;
    $user ->name= 'Ana';
    $user ->email? 'ana@gmail.com';
    $user -> password = bcrypt('!password');
    $user ->save();
    $user
    $User :: find(1);

## Make a Post Model and Migration

En este punto, eliminamos el modelo ´Post´ creado anteriormente basado en archivos y ahora se va a cree el mismo modelo por medio de ´Eloquent´. Tambien preparar la tabla de publicaciones.

´´´php
php artisan
php artisan migrate -- correr las migraciones
php artisan make:migration create_posts_table --create tablas
php artisan make:model Post -- crear modelos
php artisan tinker --acceder a tinker -- insertar datos en las tablas, etc.
'''

## Eloquent Updates and HTML Escaping

Actualizar registros en la base de datos usando ´Eloquent´, usaremos comandos en ´php artisan tinker' como los siguientes ´use App\Models\Post, $post = new Post, $post->title= 'Eloquent is Amazing', $post->excerpt = 'Lorem ipsun dolar sit amet.', $post->body = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit?', $post->save(),
Post::all(), Post::count(), Post::find(1)´. 



## Ways to Mitigate Mass Assignment Vulnerabilities

Se analizará todo lo que se necesita saber sobre las vulnerabilidades de asignación masiva. Como verá, Laravel proporciona un par de formas de especificar qué atributos pueden o no asignarse en masa. Sin embargo, hay una tercera opción al final de este video que es igualmente válida. 

Con el siguiente comando se crean nuevos `posts` en la base de datos

    Post::create(['title' => 'My Fourth Post', 'excerpt' => 'excerpt of post', 'body'=> 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste provident doloribus est officiis reiciendis magni a perferendis ratione dolorum ipsa animi, culpa ullam amet dignissimos vero commodi autem molestias suscipit.']);

Y con este comando podemos actualizar registros en la base de datos

    $post-> update(['excerpt' => 'Changed'])


## Route Model Binding

La función de vinculación del modelo de ruta de Laravel nos permite vincular un comodín de ruta a una instancia de modelo Eloquent, se crea una funcion en el modelos ´Post´, con el siguiente codigo para que retorne el ´slug´ y se modifica la funcion de rutas, en  ´web.php´. 

```php
  public function getRouteKeyName()
    {
        return 'slug';
    }
```

```php 

Route::get('posts/{post:slug}', function (Post $post) {

    return view('post', [
        'post' => $post
    ]);
});
```
Se refresca la base de datos con 

    php artisan migrate:fresh 

Luego se vuelven a insertar cada uno de los `posts` con el siguiente comando, en la base de datos `mariadb`

    insert into posts (id  , slug , title, excerpt, body, created_at,  updated_at, published_at)  VALUES (3,'my-third-post ' ,  'My <strong>Third</strong>Post', 'Lorem ipsum dolor sit amet consectetur adipisicing elit.', '<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quo saepe non soluta nobis similique numquam ullam error nisi iusto eum, exercitationem ad accusamus quibusdam iure. Exercitationem excepturi ab officiis odit</p>', '2022-02-02 18:13:28', '2022-02-03 15:13:28', NULL);


## Your First Eloquent Relationship

La siguiente tarea es asignar una categoría a cada publicación. Para permitir esto, necesitaremos crear un nuevo modelo Eloquent y una migración para representar una Categoría. 

Se corren los siguientes comandos en la maquina `vagrant`, a la siguiente ruta `cd /vagrant/sites/lfts.isw811.xyz` para crear el modelo y tabla de  `Category`. 

    php artisan make:migration create_categories_table --se crea la tabla en la base de datos
    php artisan make:model Category -m --se crea el modelo
    php artisan migrate:fresh -- se refresca la base de datos

Luego insertamos los nuevos datos en la base de datos, por medio del comando `php artisan tinker`, primero ingresamos los datos de `Category` y luego de `Post`.

    use App\Models\Category;
    $c = new Category;           -----------------Con cada una de las categorias a insertar 
    $c->name= 'Personal';
    $c->slug = 'personal';
    $c->save();

    use App\Models\Post;
    Post::create(['title' => 'My Family Post', 
    'excerpt' => 'Excerpt for my post', 
    'body'=> 'Lorem ipsum dolor sit amet consectetur adipisicing elit.Iste provident doloribus est       officiis reiciendis magni a perferendis ratione dolorum ipsa animi, culpa ullam amet dignissimos vero commodi autem moles
    tias suscipit.', 
    'slug'=> 'my-family-post',
    'category_id'=> 1]);

Se modifica el modelo `Post`, agregando la siguiente funcion.

```php
public function category()
    {
        return $this->belongsTo(Category::class);
    }
```

Para las migraciones de `posts` y `categories`, quedan la siguiente forma 

```php
public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
    }
```

```php
 public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table-> foreignId('category_id');
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt');
            $table->text('body');
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
        });
    }
```

Y finalmente para cada una de las vistas `post.blade.php` y `posts.blade.php` se agrega el siguiente codigo debajo del titilo del post. 

```html
<p>
     <a href="#">{{$post->category->name}}</a> 
```

## Show All Posts Associated With a Category

Con la categoría lista en la  aplicación, se crea una nueva ruta que obtenga y cargue todas las publicaciones asociadas con la categoría dada.

Se inserta la nueva ruta en la carpeta de `routes`, en el archivo `web.php`. 

```php
Route::get('categories/{category:slug}', function (Category $category) {

    return view('posts', [
        'posts' => $category->posts
    ]);
});
```


Se mueve la siguiente función al modelo `Category`

```php
public function posts()
    {
        return $this->hasMany(Post::class);
    }
```

Se modifica la vista `posts.blade.php` y `post.blade.php`

```html
<p>
    <a href="/categories/{{$post->category->slug}}">{{$post->category->name}}</a>
</p>
```

## Clockwork, and the N+1 Problem

Se introduce un problema de rendimiento que se conoce como el problema N+1. Debido a que las relaciones de carga diferida de Laravel, esto significa que potencialmente puede caer en una trampa en la que se ejecuta una consulta SQL adicional para cada elemento dentro de un bucle. Cincuenta elementos... cincuenta consultas SQL. A continuación,  veremos cómo depurar estas consultas, tanto manualmente como con la extensión Clockwork. 

Depurar las consultas de forma manual

    \Illuminate\Support\Facades\DB::listen(function ($query)
        {
            logger($query->sql);
        }); 

    \Illuminate\Support\Facades\DB::listen(function ($query)
        {
            logger($query->sql, $query->bindings);

        });

Depurar las consultas de forma automatica, en la  maquina virtual `vagrant`, en la `cd /vagrant/sites/lfts.isw811.xyz` corremos el comando para instalar Clockwork, además, agregamos la extesión a nuestro respectivo navegador y por medio de las herramietas para desarrollador, podemos observar el funcionamiento de clockwork. 

    composer require itsgoingd/clockwork

Y modificamos las route, en `web.php`, por medio del navegador podemos observar su funcionamiento. 

```php
Route::get('/', function () {

    return view('posts', [
        'posts' => Post::with('category')->get()
    ]);
});
```

## Database Seeding Saves Time

En este punto se asocia  una publicación de blog con un autor o usuario en particular. Pero, en el proceso de agregar esto, nuevamente nos encontramos con el problema de necesitar ingresar manualmente a nuestra base de datos. Se revisa la inicialización a la base de datos. 

Agregamos el nombre del autor del `posts`, en `post.blade.php`. 

```html
<p>
    By <a href="#">Jeffrey Way</a> in <a href="/categories/{{$post->category->id}}">{{$post->category->name}}</a>
</p>
```

Para no estar insertando los datos manualmente, cada vez que reseteamos la base de datos, se usará los `Seeder` que funciona para iniciar las tablas con los datos. Para esto se ejecutara un serie de comandos y modificaciones en el código. 

    php artisan migrate:fresh
    php artisan db: seed --agregar registros a la base de datos automaticamente 
    php artisan migrate:fresh --seed --refresca e inicia la base de datos
    php artisan tinker  --- se chequea que los datos fueron insertados automaticamente. 

Se modifica el archivo `DatabaseSeeder.php`


```php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        Category:: truncate();
        Post::truncate();


        $user= User::factory()->create();

        $personal = Category:: create([

            'name' => 'Personal',
            'slug'=> 'personal'
        ]);

        $family= Category:: create([

            'name' => 'Family',
            'slug'=> 'family'
        ]);

        $work= Category:: create([

            'name' => 'Work',
            'slug'=> 'work'
        ]);

        Post::create([
            'user_id' => $user->id,
            'category_id' => $family->id,
            'title' => 'My Family Post',
            'slug'=> 'my-family-post',
            'excerpt' => '<p> Excerpt for my post </p> ',
            'body'=> '<p> Lorem ipsum dolor sit amet consectetur adipisicing elit.Iste provident doloribus est       officiis reiciendis magni a perferendis ratione dolorum ipsa animi, culpa ullam amet dignissimos vero commodi autem moles
            tias suscipit.</p>'
        ]);

        Post::create([
            'user_id' => $user->id,
            'category_id' => $work->id,
            'title' => 'My Work Post',
            'slug'=> 'my-work-post',
            'excerpt' => '<p> Excerpt for my post</p> ',
            'body'=> '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit.Iste provident doloribus est       officiis reiciendis magni a perferendis ratione dolorum ipsa animi, culpa ullam amet dignissimos vero commodi autem moles
            tias suscipit. </p>'
        ]);
    }

}
```
Se agrega la función al modelo `Post.php`

```php
public function user()
    {
        return $this->belongsTo(User::class);
    }
```
Y la función al modelo `User.php`

```php
 public function posts(){
        return $this->hasMany(Post::class);
    }
```

## Turbo Boost With Factories

Se integran fábricas de modelos para generar sin problemas cualquier número de registros de bases de datos. 

## View All Posts By An Author

 Crear una nueva ruta que represente todas las publicaciones de blog escritas por un autor en particular. 

 ## Eager Load Relationships on an Existing Model

 Aprender qué relaciones deben cargarse de forma predeterminada en un modelo. También hablaremos de los pros y los contras de este enfoque. 

