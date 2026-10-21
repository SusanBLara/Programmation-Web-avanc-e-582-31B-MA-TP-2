{{ include('layouts/header.php', {'title': 'Catégories de livres'}) }}

<h1>Catégories de livres</h1>

<div class="catalogue-frame">
    <table class="books-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
            </tr>
        </thead>

        <tbody>
            {% for category in categories %}
                <tr>
                    <td>{{ category.name }}</td>
                    <td>{{ category.description }}</td>
                </tr>
            {% else %}
                <tr>
                    <td colspan="2">Aucune catégorie disponible.</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</div>

{{ include('layouts/footer.php') }}