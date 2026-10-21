{{ include('layouts/header.php', {'title': 'Nouveau livre'}) }}

<div class="container">
    <form action="{{ base }}/book/store" method="post" novalidate>
        <h2>Nouveau livre</h2>

        <label>Titre
            <input type="text" name="title" required maxlength="150"
                   value="{{ draft.title|default('') }}">
        </label>
        {% if errors.title is defined %}
            <span class="error" role="alert">{{ errors.title }}</span>
        {% endif %}

        <fieldset>
            <legend>Auteur(s) — choisir au moins un auteur</legend>

            {% for author in authors %}
                <label>
                    <input type="checkbox"
                           name="authors[]"
                           value="{{ author.id }}"
                           {% if author.id in (draft.authors|default([])) %}checked{% endif %}>
                    {{ author.name }}
                </label>
            {% else %}
                <p>Aucun auteur enregistré.</p>
            {% endfor %}

            {% for key, author in draft.new_authors|default([]) %}
                <label>
                    <input type="checkbox" name="authors[]" value="new_{{ key }}"
                           {% if ('new_' ~ key) in (draft.authors|default([])) %}checked{% endif %}>
                    {{ author.name }} (nouvel auteur)
                </label>
            {% endfor %}

            <button type="submit"
                    class="btn"
                    formaction="{{ base }}/author/create?from=book"
                    formnovalidate>
                Ajouter un auteur
            </button>
        </fieldset>
        {% if errors.authors is defined %}
            <span class="error" role="alert">{{ errors.authors }}</span>
        {% endif %}

        <label>Description
            <input type="text" name="description"
                   value="{{ draft.description|default('') }}">
        </label>
        {% if errors.description is defined %}
            <span class="error" role="alert">{{ errors.description }}</span>
        {% endif %}

        <label>Nombre de pages
            <input type="number" name="page_count"
                   min="1" max="2147483647"
                   value="{{ draft.page_count|default('') }}">
        </label>
        {% if errors.page_count is defined %}
            <span class="error" role="alert">{{ errors.page_count }}</span>
        {% endif %}

        <label>Prix
            <input type="number" step="0.01" name="price"
                   required min="0" max="99999999.99"
                   value="{{ draft.price|default('') }}">
        </label>
        {% if errors.price is defined %}
            <span class="error" role="alert">{{ errors.price }}</span>
        {% endif %}

        <label>Édition
            <input type="text" name="edition" maxlength="100"
                   value="{{ draft.edition|default('') }}">
        </label>
        {% if errors.edition is defined %}
            <span class="error" role="alert">{{ errors.edition }}</span>
        {% endif %}

        <label>Âge
            <input type="text" name="age_group" maxlength="50"
                   value="{{ draft.age_group|default('') }}">
        </label>
        {% if errors.age_group is defined %}
            <span class="error" role="alert">{{ errors.age_group }}</span>
        {% endif %}

        <label>Catégorie
            <select name="category_id">
                <option value="">Choisissez une cat&#233;gorie</option>
                {% for category in categories %}
                    <option value="{{ category.id }}"
                        {% if draft.category_id|default('') == category.id %}selected{% endif %}>
                        {{ category.name }}
                    </option>
                {% endfor %}
            </select>
        </label>
        {% if errors.category_id is defined %}
            <span class="error" role="alert">{{ errors.category_id }}</span>
        {% endif %}

        <input type="submit" class="btn" value="Enregistrer">
    </form>
</div>

{{ include('layouts/footer.php') }}
