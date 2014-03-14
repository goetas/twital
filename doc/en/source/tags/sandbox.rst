``sandbox``
===========

The Twital instruction for Twig ``import`` tag is ``t:sandbox`` node or the ``t:sandbox`` attribute.

The ``sandbox`` tag can be used to enable the sandboxing mode for an included
template, when sandboxing is not enabled globally for the Twig environment:

.. code-block:: xml+jinja

    <t:sandbox>
        {% include 'user.html' %}
    </t:sandbox>

    <div t:sandbox="">
        {% include 'user.html' %}
    </div>


.. note::

    For more information about ``sandbox`` tag please refer to
    `Twig official documentation <http://twig.sensiolabs.org/doc/tags/sandbox.html>`_.