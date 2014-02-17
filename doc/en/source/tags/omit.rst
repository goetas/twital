``omit``
======

This attribute asks the Twital parser to ignore the elements's open and close tag, its content will still be evaluated.

.. code-block:: jinja

    <a href="/private" t:omit="deny">
        {{ username }}
    </a>

.. code-block:: jinja

    {% if deny %}<a href="/private">{% endif %}
        {{ username }}
    {% if deny %}</a>{% endif %}

This attribute is useful when you want to create element optionally, e.g. hide a link if certain condition is met.

If you want element that is never output, you can use ``omit`` tag