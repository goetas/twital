``attr``
========

Twital allows you to create HTML/XML attributes in a very simple way.
You have not to mess uop with control structures inside HTML tags.

Let's see how it works:

.. code-block:: xml+jinja

    <div t:attr=" condition ? class='header'">
        My Company
    </div>


Here we add conditionaly an attribute based on the value of `condition` expression.


You can use any Twig test expression as **condition** and **attribute value**,
but the attribute name must be a litteral.

.. code-block:: xml+jinja

    <div t:attr="
        users | length ? class='header'|upper ,
        item in array ? class=item">
        Here wins the last class that condition will be evaluated to true.
    </div>

When not needed you can omit he condition instruction.

.. code-block:: xml+jinja

    <div t:attr="class='row'">
         Class will be "row"
    </div>

.. tip::

    `attr-append`