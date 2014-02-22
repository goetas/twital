``embed``
=========

The Twital instruction for Twig ``embed`` tag is ``t:embed`` node.

The embed tag combines the behaviour of include and extends.
It allows you to include another template's contents, just like include does.
But it also allows you to override any block defined inside the included template,
like when extending a template.

To learn about usefulness of `embed` you can read the official documentation.

Now let's see how to use it, take a look to this example:

.. code-block:: xml+jinja

    <t:embed from="teasers_skeleton.html.twital">
        <t:block name="left_teaser">
            Some content for the left teaser box
        </t:block>
        <t:block name="right_teaser">
            Some content for the right teaser box
        </t:block>
    <t:embed>

You can add additional variables by passing them after the ``with`` attribute:

.. code-block:: xml+jinja

    <t:embed from="header.html" with="{'foo': 'bar'}">
        ...
    </t:embed>


You can disable access to the current context by using the ``only`` attribute:

.. code-block:: xml+jinja

    <t:embed from="header.html" with="{'foo': 'bar'} only="true">
        ...
    </t:embed>

You can mark an include with ``ignore-missing`` attribute in which case Twital
 will ignore the statement if the template to be included does not exist.

.. code-block:: xml+jinja

    <t:embed from="header.html" with="{'foo': 'bar'} ignore-missing="true">
        ...
    </t:embed>

``ignore-missing`` can't be an expression, it has to be evaluated only at compile time.


To use Twig expressions as template name you have to use a namespace prefix on 'form' attribute:

.. code-block:: xml+jinja

    <t:embed t:from="ajax ? 'ajax.html' : 'not_ajax.html' ">
        ...
    </t:embed>
    <t:embed t:from="['one.html','two.html']">
        ...
    </t:embed>

.. note::

    For more information about ``embed`` tag please refer to `Twig official documentation <http://twig.sensiolabs.org/doc/tags/embed.html>`_.

.. seealso:: :doc:`include<../tags/include>`
