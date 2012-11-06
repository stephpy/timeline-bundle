# Renderer

Using twig

```yaml
# Define
render:
    path:     'AcmeBundle:Timeline'
    fallback: 'AcmeBundle:Timeline:default.html.twig'
    i18n: #Do you want to use i18n when rendering ? if not, remove this not.
        fallback: en
```

```twig
{{ timeline_render(entry) }}
# This will try to call "AcmeBundle:Timeline:**verb**.html.twig
# If exception, it return the fallback defined on config

{{ timeline_render(entry, "your template") }}
# This will try to call "your template"
# If exception, it return the fallback defined on config

{{ i18n_timeline_render(entry, "en") }}
# This will try to call "AcmeBundle:Timeline:**verb**.en.html.twig
# If exception, it return the i18n fallback defined on config and then on global fallback
```

# Timeline Action Component Rendering

Components of the timeline action are rendered, similarly to
[Symonfy 2's Forms](http://symfony.com/doc/current/cookbook/form/form_customization.html), using template fragements
via the twig function `timeline_component_render()`

```twig
{% timeline_component_render(timeline, 'subject') %}
```

These fragments are defined as twig blocks within [HighcoTimelineBundle:Action:components.html.twig](TimelineBundle/).

Rendering the subject component will generate html using the __toString() method of the subject model.

```twig
{% timeline_component_render(timeline, 'subject') %}
```
Renders:
```twig
<span class="subject">Subject String</span>
```

## Themes

Like Symfony's Forms, you can customize the rendering of action components by importing a _Theme_.

To customize your component output you need only override the correct template fragment.

### Component Blocks

The default theme file, [HighcoTimelineBundle:Action:components.html.twig](TimelineBundle/), defines several
blocks which form the basis for the theme system. In the previous example, rendering the subject component uses the
`subject_component` block, which in-turn uses the `action_component` block.

To customize subject-specific rendering, override the `subject_component` block. If you want to customize all components
in the same manner, override the `action_component` block.

The following variables are passed to the component blocks: `value`, `model`, `id`, `text`\*, `type`
_(subject, verb, direct_complement, indirect_complement)_, and `timelineAction`.

_\* Not available for action subjects_

### Model-Specific Custom Blocks

Additionally, for the subject, direct_complement, and indirect_complement components, you can provide model-specific
overrides using the following block naming scheme:
    `_[lowercase-underscored-model-namespace]_[component_name]_component`

For example, create one of the following blocks to customize the rendering of `\Acme\UserBundle\Entity\User`:
```twig
{# Customize the subject component rendering for User objects #}
{% block _acme_userbundle_entity_user_subject_component %}
    {# Show an avatar and link to the user's profile #}
    <img class="avatar" src="…" /> <a href="path(…)">{{ value.name }}</a>
{% endblock _acme_userbundle_entity_user_default_component %}
```
To customize the rendering when the User is the directComplement or indirectComplement 'subject' to 'direct_complement'
or 'indirect_complement' respectively,
```twig
{% block _acme_userbundle_entity_user_direct_complement_component %}…{% endblock %}

{% block _acme_userbundle_entity_user_indirect_complement_component %}…{% endblock %}
```

To provide a generic customization, regardless of component, use 'default' in place of the component:
```twig
{# Override for this model in any component #}
{% block _acme_userbundle_entity_user_default_component %}
    {# Link to the user's profile #}
    <a href="path(…)">{{ value.name }}</a>
{% endblock _acme_userbundle_entity_user_default_component %}
```

Combine both techniques to minize code-reuse:

```twig
{# Show avatars when the user is the subject of action and not otherwise #}

{% block _acme_userbundle_entity_user_subject_component %}
    {% set avatar = true %}
    {{ block('_acme_userbundle_entity_user_default_component') }}
{% endblock _acme_userbundle_entity_user_subject_component %}

{% block _acme_userbundle_entity_user_default_component %}
    {% if avatar|default(false) %}
    {# Show Avatar #}
        <img src="…" />
    {% endif %}

    {# Link to the user's profile #}
    <a href="path(…)">{{ value.name }}</a>
{% endblock _acme_userbundle_entity_user_default_component %}
```

### Creating Themes
See [Form Theming in Twig](http://symfony.com/doc/current/cookbook/form/form_customization.html#form-theming-in-twig)
for pros and cons for where you define your themes.

#### Method 1: Inside the same Template as the TimelineAction
```twig
{# AcmeUserBundle:Timeline:added.html.twig #}

{% use "AcmeUserBundle:Timeline:added_content.html.twig" %}

{{ block('timeline_action') }}
```
```twig
{# AcmeUserBundle:Timeline:added_content.html.twig #}

{% timeline_action_theme timeline _self %}

{% block subject_component %}
    <div class="subject_component">
        {# … #}
    </div>
{% endblock %}

{% block timeline_action %}
    {{ timeline_component_render(timeline, 'subject') }} {{ timeline_component_render(timeline, 'verb') }}

    {{ timeline_component_render(timeline, 'direct_complement') }}

    {% if timeline.indirectComplement|default(false) or timeline.indirectComplementText|default(false) %}
        {{ preposition|default('with') }} {{ timeline_component_render(timeline,'indirect_complement') }}
    {% endif %}
{% endblock timeline_action %}
```

By using the special {% timeline_action_theme timeline _self %} tag, Twig looks inside the same template for any
overridden component blocks.

#### Method 2: Inside a Separate Template
This method allows you to reuse the custom blocks in different template files.

```twig
{# Acme/UserBundle/Resources/views/Timeline/components.html.twig #}

{% block _acme_userbundle_entity_user_subject_component %}
    <div class="subject_component">
        {# … #}
    </div>
{% endblock %}

```

As before you can reference this theme resource using the `timeline_action_theme` tag:

```twig
{% timeline_action_theme timeline 'AcmeUserBundle:Timeline:components.html.twig' %}
{# … #}
{{ timeline_component_render(timeline, 'subject') }}
{# … #}
```

#### Application-wide Customizations

To configure an application-wide theme, specify your custom theme file in `config.yml`
```yaml
highco_timeline:
  render:
    resources:
        - 'AcmeUserBundle:Timeline:components.html.twig'
```