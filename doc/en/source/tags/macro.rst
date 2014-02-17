``macro``
=========

Macros, as described by Twig, are comparable with functions in regular programming languages. 
To declare a macro inside Twital, the syntax is:

.. code-block:: jinja
    <t:macro name="input" args="value, type, size">
        <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
    </t:macro>

For more information about ``macro`` attrubute, please refer to Twig official ducumentation.

To use a macro inside your Twital template, take a look to ``import`` attribute.