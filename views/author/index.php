{{ include('layouts/header.php', {'title': 'Auteurs'}) }}

<h1>Liste des auteurs</h1>

<div class="catalogue-frame">
    <table class="books-table authors-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Pays</th>
                <th>Année de naissance</th>
                <th>Biographie</th>
            </tr>
        </thead>

        <tbody>
            {% for author in authors %}
                <tr>
                    <td>{{ author.name }}</td>
                    <td>{{ author.country }}</td>
                    <td>{{ author.birth_year }}</td>
                    <td>{{ author.biography }}</td>
                </tr>
            {% else %}
                <tr>
                    <td colspan="5">Aucun auteur trouvé.</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</div>

{{ include('layouts/footer.php') }}
