``attr``
========

Twital allows you to create HTML/XML attributes in a very simple way.
You do not have to mess up with control structures inside HTML tags.

Let's see how does it work:

.. code-block:: xml+jinja

    <div t:attr=" condition ? class='header'">
        My Company
    </div>


Here we add conditionally an attribute based on the value of the `condition` expression.


You can use any Twig test expression as **condition** and **attribute value**,
but the attribute name must be a litteral.

.. code-block:: xml+jinja

    <div t:attr="
        users | length ? class='header'|upper ,
        item in array ? class=item">
        Here wins the last class that condition will be evaluated to true.
    </div>

When not needed, you can omit the condition instruction.

.. code-block:: xml+jinja

    <div t:attr="class='row'">
         Class will be "row"
    </div>

.. tip::

    `attr-append`


To set an HTML5 boolean attribute, just use booleans as ``true`` or ``false``.

.. code-block:: xml+jinja

    <option t:attr="selected=true">
        My Company
    </option>

The previous template will be rendered as:

.. code-block:: html

    <option selected>
        My Company
    </option>

.. note::

    Since XML does not have the concept of "boolean attributes",
    this feature may break your output if you are using XML.



To to remove and already defined attribute, use ``false`` as attribute value

.. code-block:: xml+jinja

    <div class="foo" t:attr="class=false">
        My Company
    </div>

The previous template will be rendered as:

.. code-block:: html

    <div>
        My Company
    </div>

