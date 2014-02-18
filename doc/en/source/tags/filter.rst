``filter``
==========

The Twital instruction for Twig ``filter`` tag is ``t:filter`` attribute.

To see how to use it, take a look to this example:

.. code-block:: xml+jinja

    <div t:filter="upper">
        This text becomes uppercase
    </div>

    <div t:filter="upper|escape">
        This text becomes uppercase
    </div>


.. note::

    To learn more about `filter` tab you can read the
    `Twig official documentation <http://twig.sensiolabs.org/doc/tags/filter.html>`_.