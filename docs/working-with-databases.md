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

## Route Model Binding

La función de vinculación del modelo de ruta de Laravel nos permite vincular un comodín de ruta a una instancia de modelo Eloquent, se crea una funcion en el modelos ´Post´, con el siguiente codigo para que retorne el ´slug´ y se modifica la funcion de rutas, en  ´web.php´. 

´´´php
  public function getRouteKeyName()
    {
        return 'slug';
    }
´´´

´´´php 
Route::get('posts/{post:slug}', function (Post $post) {

    return view('post', [
        'post' => $post
    ]);
});
´´´
## Your First Eloquent Relationship

La siguiente tarea es asignar una categoría a cada publicación. Para permitir esto, necesitaremos crear un nuevo modelo Eloquent y una migración para representar una Categoría. 

## Show All Posts Associated With a Category

Con la categoría lista en la  aplicación, se crea una nueva ruta que obtenga y cargue todas las publicaciones asociadas con la categoría dada.

## Clockwork, and the N+1 Problem

Se introduce un problema de rendimiento que se conoce como el problema N+1. Debido a que las relaciones de carga diferida de Laravel, esto significa que potencialmente puede caer en una trampa en la que se ejecuta una consulta SQL adicional para cada elemento dentro de un bucle. Cincuenta elementos... cincuenta consultas SQL. A continuación,  veremos cómo depurar estas consultas, tanto manualmente como con la extensión Clockwork. 

## Database Seeding Saves Time

En este punto se asocia  una publicación de blog con un autor o usuario en particular. Pero, en el proceso de agregar esto, nuevamente nos encontramos con el problema de necesitar ingresar manualmente a nuestra base de datos. Se revisa la inicialización a la base de datos. 

## Turbo Boost With Factories

Se integran fábricas de modelos para generar sin problemas cualquier número de registros de bases de datos. 

## View All Posts By An Author

 Crear una nueva ruta que represente todas las publicaciones de blog escritas por un autor en particular. 

 ## Eager Load Relationships on an Existing Model

 Aprender qué relaciones deben cargarse de forma predeterminada en un modelo. También hablaremos de los pros y los contras de este enfoque. 

