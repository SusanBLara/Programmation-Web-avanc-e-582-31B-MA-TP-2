{{ include('layouts/header.php', {title: 'Accueil — Librairie pour enfants'}) }}

<div class="container home-page">
    <div class="home-copy">
        <h1>De petites pages, de grandes aventures.</h1>

        <p class="home-subtitle">
            Des histoires pour rêver, apprendre et grandir.
        </p>

        <p>
            Découvrez nos livres pour enfants et trouvez votre
            prochaine aventure à partager en famille.
        </p>

        <a class="btn" href="{{ base }}/books">
            Découvrir les livres
        </a>

        <a class="home-link" href="{{ base }}/categories">
            Explorer les catégories
        </a>
    </div>

    <img
        class="home-picture"
        src="{{ asset }}images/librairie-enfants.jpg"
        width="1200"
        height="1200"
        alt="Librairie pour enfants"
    >
</div>

{{ include('layouts/footer.php') }}
