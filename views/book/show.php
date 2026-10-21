{{ include('layouts/header.php', {'title': 'Livre'}) }}

<div class="container">
    <h1>Détails du livre</h1>

    <p><strong>Titre : </strong>{{ book.title }}</p>

    <p><strong>Description : </strong>{{ book.description }}</p>

    <p><strong>Nombre de pages : </strong>{{ book.page_count }}</p>

    <p><strong>Prix : </strong>{{ book.price }} $</p>

    <p><strong>Édition : </strong>{{ book.edition }}</p>

    <p><strong>Âge : </strong>{{ book.age_group }}</p>

    <p><strong>Catégorie : </strong>{{ categoryName }}</p>

    <p>
        <strong>Auteur(s) : </strong>
        {% for author in authors %}
            {{ author.name }}
            {% if not loop.last %}<br>{% endif %}
        {% endfor %}
    </p>

    <a href="{{ base }}/book/edit?id={{ book.id }}" class="btn">
        Modifier
    </a>

    <form action="{{ base }}/book/delete" method="post">
        <input type="hidden" name="id" value="{{ book.id }}">
        <input type="submit" value="Supprimer" class="btn red">
    </form>
</div>

{{ include('layouts/footer.php') }}
