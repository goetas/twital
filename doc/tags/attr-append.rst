``attr``
========

Twital allows you to create HTML/XML attributes in a very simple way.

`t:attr-append` is a different version of `t:attr`
that allows to append content to existing attributes instead of replacing it.

.. code-block:: xml+jinja

    <div class="row" t:attr-append=" condition ? class=' even'">
         class will be "row even" if 'i' is odd.
    </div>

In the same way of `t:attr`, `condition` and the value of attribute can be any valid Twig expression.

.. code-block:: xml+jinja

    <div class="row"
        t:attr-append=" i mod 2 ? class=' even'|upper">
         class will be "row EVEN" if 'i' is odd.
    </div>


When not needed you can omit he condition instruction.

.. code-block:: xml+jinja

    <div class="row" t:attr-append=" class=' even'">
         Class will be "row even"
    </div>