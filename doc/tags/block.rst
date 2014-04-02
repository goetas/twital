``block``
=========


The Twital instruction for Twig ``block`` tag is ``t:block`` node.

To see how to use it, consider the following base template named ``layout.html.twital``:

.. code-block:: xml+jinja

    <html>
        <head>
            <title>Hello world!</title>
        </head>
        <body t:block="content">
            Hello!
        </div>
    </html>


To improve the greeting message we can extend it using the ``t:textends`` node,
so we can create a new template called ``hello.html.twital``.

.. code-block:: xml+jinja

    <t:extends from="layout.html.twital">
        <t:block name="content">
            Hello {{name}}!
        </t:block>
    </t:extends>

As you can see we have overwritten the content of ``content`` block, with a new one.
To do this we have used a ``t:block`` node.

Of course, if you need, you can also **call the parent block** from inside, it is simple:

.. code-block:: xml+jinja

    <t:extends from="layout.html.twital">
        <t:block name="content">
            {{parent()}}
            Hello {{name}}!
        </t:block>
    </t:extends>

.. note::

    To learn more about template inheritance you can read the
    `Twig official documentation <http://twig.sensiolabs.org/doc/tags/block.html>`_