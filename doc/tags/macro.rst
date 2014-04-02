``macro``
=========

The Twital instruction for Twig ``macro`` tag is ``t:macro`` node.

To declare a macro inside Twital, the syntax is:

.. code-block:: xml+jinja

    <t:macro name="input" args="value, type, size">
        <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
    </t:macro>


To use a macro inside your Twital template, take a look to :doc:``import<../tags/import>`` attribute.

.. note::

    For more information about ``macro`` tag please refer to
    `Twig official documentation <http://twig.sensiolabs.org/doc/tags/macro.html>`__.