{{ include('layouts/header.php', {'title': 'Nouvel auteur'}) }}

<div class="container">
    <form action="{{ base }}/author/store{% if fromBook %}?from=book{% endif %}" method="post">

        <h2>Nouvel auteur</h2>

        <label>Nom
            <input type="text"
                   name="name"
                   maxlength="100"
                   value="{{ data.name|default('') }}">
        </label>

        {% if errors.name is defined %}
            <span class="error" role="alert">{{ errors.name }}</span>
        {% endif %}

        <label>Pays
            <input type="text"
                   name="country"
                   maxlength="100"
                   value="{{ data.country|default('') }}">
        </label>

        <label>Année de naissance
            <input type="number"
                   name="birth_year"
                   min="1"
                   max="{{ 'now'|date('Y') }}"
                   step="1"
                   value="{{ data.birth_year|default('') }}">
        </label>

        <label>Biographie
            <textarea name="biography"
                      rows="4"
                      maxlength="16000">{{ data.biography|default('') }}</textarea>
        </label>

        <input type="submit" class="btn" value="Enregistrer">

        {% if not fromBook %}
            <a href="{{ base }}/authors">Retour aux auteurs</a>
        {% endif %}

    </form>
</div>

{{ include('layouts/footer.php') }}
