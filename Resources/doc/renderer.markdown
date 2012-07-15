# Renderer

Using twig

````yaml
    # Define
    render:
        path:     'AcmeBundle:Timeline'
        fallback: 'AcmeBundle:Timeline:default.html.twig'
        i18n: #Do you want to use i18n when rendering ? if not, remove this not.
            fallback: en

    {{ timeline_render(entry) }}
    # This will try to call "AcmeBundle:Timeline:**verb**.html.twig
    # If exception, it return the fallback defined on config

    {{ timeline_render(entry, "your template") }}
    # This will try to call "your template"
    # If exception, it return the fallback defined on config

    {{ i18n_timeline_render(entry, "en") }}
    # This will try to call "AcmeBundle:Timeline:**verb**.en.html.twig
    # If exception, it return the i18n fallback defined on config and then on global fallback
````
