[<-Go back](/README.md)

# Pagination 

## Laughably Simple Pagination

Actualmente estamos obteniendo todas las publicaciones de la base de datos y mostrándolas como una cuadrícula en la página de inicio. Pero, ¿qué sucede más adelante cuando tienes, digamos, quinientas publicaciones de blog? Eso es un poco demasiado costoso de renderizar. Para solucionar vamos a utilizar la paginación. 

Primero modificamos  la función `index` para agregar la paginación, en el controlador `PostController.php`

```php
public function index()
    {
        return view('posts.index', [
            'posts' => Post::latest()->filter(
                request(['search', 'category', 'author'])
            )->paginate(6)->withQueryString()
        ]);
    }
```

Corremos el siguiente comando para publicar el modulo de `pagination`. Recuerde que estos comandos se corren en la máquina `vagrant` en la ruta `cd /vagrant/sites/lfts.isw811.xyz`

    php artisan vendor:publish

E ingresamos el número 17, que se refiere a `laravel-pagination`. 

También si deseamos mas publicaciones para ver el funcionamiento de la implementación de paginación podemos ingresar el comando, al misma ruta alterior 

    php artisan tinker
    App\Models\Post::factory(10)->create(['category_id' => 2 ]);
