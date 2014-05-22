``filter``
==========

The Twital instruction for Twig ``filter`` tag is ``t:filter`` attribute.

To see how to use it, take a look at this example:

.. code-block:: xml+jinja

    <div t:filter="upper">
        This text becomes uppercase
    </div>

    <div t:filter="upper|escape">
        This text becomes uppercase
    </div>


.. note::

    To learn more about the `filter` tab, you can read the
    `Twig official documentation <http://twig.sensiolabs.org/doc/tags/filter.html>`_.
