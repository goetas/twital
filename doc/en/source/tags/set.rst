``set``
=======

The Twital instruction for Twig ``set`` tag is the ``t:set`` attribute.


You can use ``set`` to assign variables. The syntax to use ``set`` attribute is:

.. code-block:: xml+jinja

    <p t:set=" name = 'tommy' ">Hello {{ name }}</p>
    <p t:set=" foo = {'foo': 'bar'} ">Hello {{ foo.bas }}</p>
    <p t:set=" name = 'tommy', surname='math' ">
        Hello {{ name }} {{ surname }}
    </p>

.. note::

    For more information about ``set`` please refer to Twig official ducumentation.