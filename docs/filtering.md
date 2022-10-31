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
