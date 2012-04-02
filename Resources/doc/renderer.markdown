# Renderer

Using twig

````yaml
    # Define
    render:
        path:     'AcmeBundle:Timeline'
        fallback: 'AcmeBundle:Timeline:default.html.twig'

    {{ timeline_render(entry) }}
    # This will try to call "AcmeBundle:Timeline:**verb**.html.twig
    # If exception, it return the fallback defined on config

    {{ timeline_render(entry, "your template") }}
    # This will try to call "your template"
    # If exception, it return the fallback defined on config
````
