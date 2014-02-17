``extends``
===========


The Twital instruction for Twig ``extends`` tag is ``t:extends`` node.
To see how to use it, take a look to this example:


Consider the following base template named ``layout.html.twital``.
Here we are creating a simple page that says hello to someone.

With `t:block` attribute we mark the body content as extensibile.

.. code-block:: xml+jinja

    <html>
        <head>
            <title>Hello world!</title>
        </head>
        <body>
            <div t:block="content">
            Hello!
            </div>
        </div>
    </html>


To improove the greating message we can extend it using the ``t:textends`` node,
so we can create a new template called ``hello.html.twital``.

.. code-block:: xml+jinja

    <t:extends from="layout.html.twital">
        <t:block name="content">
            Hello {{name}}!
        </t:block>
    </t:extends>

As you can see we have overwritten the content of ``content`` block, with a new one.
To do this whe have used a ``t:block`` node.

You can also **extend a Twig Template**, so you can mix Twig and Twital Templates.

.. code-block:: xml+jinja

    <t:extends from="layout.twig">
        <t:block name="content">
            Hello {{name}}!
        </t:block>
    </t:extends>


Sometimes is useful to obtain the layout **template name from a variable**,
to do this you  have to add the Twital namespace to attribute name:

.. code-block:: xml+jinja

    <t:extends t:from="layoutVar">
        <t:block name="content">
            <t:block-call t:name="variableBlock"/>
            Hello {{name}}!
        </t:block>
    </t:extends>

Now ``hello.html.twital`` can inherit dynamically from different templates.
Now the tempalte name can be any valid Twig expression.

.. note::

    To learn more about template inheritance you can read the `Twig official documentation<http://twig.sensiolabs.org/doc/tags/autoescape.html>`.