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
