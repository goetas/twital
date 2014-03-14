``spaceless``
=============

The Twital instruction for Twig ``spaceless`` tag is ``t:spaceless`` node or the ``t:spaceless`` attribute.


.. code-block:: xml+jinja

    <t:spaceless>
        {% include 'user.html' %}
    </t:spaceless>

    <div t:spaceless="">
        {% include 'user.html' %}
    </div>


.. note::

    For more information about ``spaceless`` tag please refer to
    `Twig official documentation <http://twig.sensiolabs.org/doc/tags/spaceless.html>`_.
