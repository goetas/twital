``autoescape``
==============

The Twital instruction for Twig ``autoescape`` tag is ``t:autoescape`` attribute.


Whether automatic escaping is enabled or not, you can mark a section of a
template to be escaped or not by using the ``autoescape`` tag.

To see how to use it, take a look to this example:

.. code-block:: xml+jinja

    <div t:autoescape="true">
        Everything will be automatically escaped in this block
        using the HTML strategy
    </div>

    <div t:autoescape="html">
        Everything will be automatically escaped in this block
        using the HTML strategy
    </div>

    <div t:autoescape="js">
        Everything will be automatically escaped in this block
        using the js escaping strategy
    </div>

    <div t:autoescape="false">
        Everything will be outputted as is in this block
    </div>

When automatic escaping is enabled everything is escaped by default except for
values explicitly marked as safe. Those can be marked in the template by using
the Twig ``raw`` filter:

.. code-block:: xml+jinja

    <div t:autoescape="false">
        {{ safe_value|raw }}
    </div>

