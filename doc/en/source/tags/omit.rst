``omit``
======

This attribute asks the Twital parser to ignore the elements's open and close tag,
its content will still be evaluated.

.. code-block:: xml+jinja

    <a href="/private" t:omit="false">
        {{ username }}
    </a>

    <t:omit>
        {{ username }}
    </t:omit>


This attribute is useful when you want to create element optionally,
e.g. hide a link if certain condition is met.