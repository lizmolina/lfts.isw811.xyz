<!DOCTYPE html>

<Title>My Blog</Title>
<link rel="stylesheet" href="/css/app.css">
<body> 
    
        @foreach ($posts as $post)
    <article class="{{$loop->even ? 'foobar' : '' }}">
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
  
       
</body>