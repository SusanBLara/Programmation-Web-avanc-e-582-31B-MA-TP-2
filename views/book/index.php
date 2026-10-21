{{ include('layouts/header.php', {'title': 'Livres'}) }}

<h1>Liste des livres</h1>

<a href="{{ base }}/book/create" class="btn">
    Ajouter un livre
</a>

<div class="catalogue-frame">
    <table class="books-table">
        <thead>
            <tr>
                <th class="book-title-column">Titre</th>
                <th>Auteur(s)</th>
                <th>Catégorie</th>
                <th class="book-description-column">Description</th>
                <th>Pages</th>
                <th>Prix</th>
                <th>Édition</th>
                <th>Âge</th>
            </tr>
        </thead>

        <tbody>
            {% for book in books %}
                <tr>
                    <td>
                        <a href="{{ base }}/book/show?id={{ book.id }}">
                            {{ book.title }}
                        </a>
                    </td>

                    <td>{{ book.author_names }}</td>
                    <td>{{ book.category_name }}</td>
                    <td>{{ book.description }}</td>
                    <td>{{ book.page_count }}</td>
                    <td class="book-price">{{ book.price }} $</td>
                    <td>{{ book.edition }}</td>
                    <td class="book-age">{{ book.age_group }}</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</div>

{{ include('layouts/footer.php') }}