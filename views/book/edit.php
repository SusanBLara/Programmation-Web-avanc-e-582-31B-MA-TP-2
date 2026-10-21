{{ include('layouts/header.php', {'title': 'Modifier un livre'}) }}

<h1>Modifier un livre</h1>

<div class="catalogue-frame">
    <form method="post" action="{{ base }}/book/update?id={{ book.id }}" novalidate>

        <div>
            <label for="title">Titre</label>
            <input type="text" id="title" name="title"
                   value="{{ book.title }}" required>
        </div>
        {% if errors.title is defined %}
            <span class="error" role="alert">{{ errors.title }}</span>
        {% endif %}

        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ book.description }}</textarea>
        </div>
        {% if errors.description is defined %}
            <span class="error" role="alert">{{ errors.description }}</span>
        {% endif %}

        <div>
            <label for="page_count">Nombre de pages</label>
            <input type="number" id="page_count" name="page_count"
                   value="{{ book.page_count }}" min="1">
        </div>
        {% if errors.page_count is defined %}
            <span class="error" role="alert">{{ errors.page_count }}</span>
        {% endif %}

        <div>
            <label for="price">Prix</label>
            <input type="number" id="price" name="price"
                   value="{{ book.price }}" min="0" step="0.01" required>
        </div>
        {% if errors.price is defined %}
            <span class="error" role="alert">{{ errors.price }}</span>
        {% endif %}

        <div>
            <label for="edition">Édition</label>
            <input type="text" id="edition" name="edition"
                   value="{{ book.edition }}">
        </div>
        {% if errors.edition is defined %}
            <span class="error" role="alert">{{ errors.edition }}</span>
        {% endif %}

        <div>
            <label for="age_group">Âge recommandé</label>
            <input type="text" id="age_group" name="age_group"
                   value="{{ book.age_group }}">
        </div>
        {% if errors.age_group is defined %}
            <span class="error" role="alert">{{ errors.age_group }}</span>
        {% endif %}

        <div>
            <label for="category_id">Catégorie</label>
            <select id="category_id" name="category_id" required>
                <option value="">Choisissez une cat&#233;gorie</option>
                {% for category in categories %}
                    <option value="{{ category.id }}"
                        {% if category.id == book.category_id %}selected{% endif %}>
                        {{ category.name }}
                    </option>
                {% endfor %}
            </select>
        </div>
        {% if errors.category_id is defined %}
            <span class="error" role="alert">{{ errors.category_id }}</span>
        {% endif %}

        <fieldset>
            <legend>Auteurs</legend>

            {% for author in authors %}
                <div>
                    <label>
                        <input type="checkbox" name="authors[]"
                               value="{{ author.id }}"
                               {% if author.id in selectedAuthors %}checked{% endif %}>
                        {{ author.name }}
                    </label>
                </div>
            {% endfor %}
        </fieldset>
        {% if errors.authors is defined %}
            <span class="error" role="alert">{{ errors.authors }}</span>
        {% endif %}

        <button type="submit" class="btn">
            Enregistrer les modifications
        </button>
    </form>
</div>

{{ include('layouts/footer.php') }}
