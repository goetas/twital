``if``
======

The element and its content will be shown only if the ``if`` condition is evaluated to true.


.. code-block:: jinja

    <p t:if="online == false">
        Our website is in maintenance mode. Please, come back later.
    </p>

.. code-block:: jinja

    {% if online == false %}
    <p>
        Our website is in maintenance mode. Please, come back later.
    </p>
    {% endif %}


``elseif`` and ``else`` are not supported, but you can always combine Twital with Twig.

.. code-block:: jinja

    <p t:if="online_users > 0">
        {%if online_users == 1%}
            one user
        {% else %}
            {{online_users}} users
        {% endif %}
    </p>
