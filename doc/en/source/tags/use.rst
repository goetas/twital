``use``
===========

The Twital instruction for Twig ``use`` tag is ``t:use`` node.

This is a fature that allow horizontal reuse of templates.
To learn more about it you can read the official documentation.

Let's see how it works:

.. code-block:: xml+jinja

    <t:use from="bars.html"/>

    <t:block name="sidebar">
        ...
    </t:block>


You can create some aliases for block inside "used" template to avoid name conflicting:

.. code-block:: xml+jinja
    <t:extends from="layout.html.twig">
        <t:use from="bars.html" aliases="sidebar as sidebar_original, footer as old_footer"/>

        <t:block name="sidebar">
            {{ block('sidebar_original') }}
        </t:block>
    </t:extends>

.. note::

    For more information about ``use`` tag please refer to
    `Twig official documentation <http://twig.sensiolabs.org/doc/tags/use.html>`_.
