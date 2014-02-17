``if``
======

The Twital instruction for Twig ``if`` tag is ``t:if`` attribute.

.. code-block:: xml+jinja

    <p t:if="online == false">
        Our website is in maintenance mode. Please, come back later.
    </p>



``elseif`` and ``else`` are not supported, but you can always combine Twital with Twig.

.. code-block:: xml+jinja

    <p t:if="online_users > 0">
        {%if online_users == 1%}
            one user
        {% else %}
            {{online_users}} users
        {% endif %}
    </p>

.. note::

    For more information about ``if`` tag please refer to Twig official documentation.
