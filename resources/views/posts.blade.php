<!DOCTYPE html>

<Title>My Blog</Title>
<link rel="stylesheet" href="/css/app.css">
<body> 
    <?php foreach ($posts as $post) : ?>
    <article>
        <h1>
            <a href="/posts/<?= $post->slug; ?>">
                <?= $post->title; ?>
            
            </a>


        </h1>
        <div>
            <?= $post->excerpt; ?>
    

        </div>
       
    </article>
   <?php endforeach; ?> 
       
</body>