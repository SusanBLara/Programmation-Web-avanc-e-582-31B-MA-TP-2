{{ include('layouts/header.php', {'title': 'Erreur'}) }}

<div class="error-page">
    <h1 class="error-page__title">Erreur :(</h1>

    <h2 class="error-page__message">
        {{ msg }}
    </h2>

    <a href="{{ base }}/" class="error-page__button">
        Retour à l'accueil
    </a>
</div>

{{ include('layouts/footer.php') }}