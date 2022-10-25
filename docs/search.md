[<-Go back](/README.md)

# Search

## Search (The Messy Way)

En esta nueva sección, se implementará  la funcionalidad de búsqueda para nuestro blog. El codigo se  hará funcionable pero no reutilizable. 

Para esto utilizaremos el codigo que se encuentra en la vista `_posts-header.blade.php`, solo agregaremos el atributo `value`. 

```php
 <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl px-3 py-2">
        <form method="GET" action="#">
            <input typ
            e="text" name="search" placeholder="Find something"
                class="bg-transparent placeholder-black font-semibold text-sm"
                value= "{{ request('search')}}">
        </form>
    </div>
```

Y se modifica la ruta en `web.php`

```php
Route::get('/', function () {

    $posts = Post:: latest();

    if(request('search')){
        $posts
        ->where('title', 'like', '%' . request('search') . '%')
        ->orWhere('body', 'like', '%' . request('search') . '%');
    }

    return view('posts', [
        'posts' => $posts->get(),
        'categories' => Category::all()
    ]);
})->name('home');
```
## 
