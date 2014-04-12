``if``
======

The Twital instruction for Twig's ``if`` tag is  the``t:if`` attribute.

.. code-block:: xml+jinja

    <p t:if="online == false">
        Our website is in maintenance mode. Please, come back later.
    </p>


``elseif`` and ``else`` are not *well* supported, but you can always combine Twital with Twig.

.. code-block:: xml+jinja

    <p t:if="online_users > 0">
        {%if online_users == 1%}
            one user
        {% else %}
            {{online_users}} users
        {% endif %}
    </p>

But if you are really interested to use ``elseif`` and ``else`` tags with Twital 
you can do it anyway.

.. code-block:: xml+jinja

    <p t:if="online">
        I'm online
    </p>
    <p t:elseif="invisible">
        I'm invisible
    </p>
    <p t:else="">
        I'm offline
    </p>

This syntax will work if there are no non-space charachters between the ``p`` tags.

This example will not work:

.. code-block:: xml+jinja

    <p t:if="online">
        I'm online
    </p>
    <hr />
    <p t:else="">
        I'm offline
    </p>
    
    <p t:if="online">
        I'm online
    </p>
    some text...
    <p t:else="">
        I'm offline
    </p>

.. note::

    To learn more about Twig ``if`` tag please refer to `Twig official documentation <http://twig.sensiolabs.org/doc/tags/if.html>`_.
