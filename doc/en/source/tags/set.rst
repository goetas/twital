``set``
=======
You can use ``set`` to assign variables. The syntax to use ``set`` attribute is:

.. code-block:: jinja

    <p t:set=" name = 'tommy' ">Hello {{ name }}</p>
    <p t:set=" foo = {'foo': 'bar'} ">Hello {{ foo.bas }}</p>
    <p t:set=" name = 'tommy', surname='math' ">
        Hello {{ name }} {{ surname }}
    </p>


For more information about ``set`` please refer to Twig official ducumentation.