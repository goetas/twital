What is Twital?
###############

Twital is a little "plugin" for Twig that change its templating language syntax,
adding some shortcuts ad making it syntax more suitable for HTML based (XML, HTML5) templates.

You can learn more about Twig reading its official documentation on http://twig.sensiolabs.org/

To understand better what are Twital benefits consider this Twig template
that simply shows a list of users from an array.

.. code-block:: xml+jinja

    {% if users %}
        <ul>
            {% for user in users %}
                <li>
                    {{ user.name }}
                </li>
            {% endfor %}
        </ul>
    {% endif %}

To express the same template using **Twital** plugin for Twig you can do:

.. code-block:: xml+jinja

    <ul t:if="users">
        <li t:for="user in users">
            {{ user.name }}
        </li>
    </ul>

As you can see, using Twital template is more readable, less verbose
and you have not to worry about opening and closing block instructions,
they are inherited from HTML structure.




Of course Twital supports all other Twig functionalities as template inheritance, translations, looping, escaping etc.
`Here you can find a complete list of Twital attributes and elements. <tags/index>`_

If some Twig functionality is not available for Twital you can freely mix these the two syntaxes:

.. code-block:: xml+jinja

    <h1>{% trans %}Members{% endtrans %}</h1>
    <ul t:if="users">
        <li t:for="user in users">
            {{ user.name }}
        </li>
    </ul>

Here we have mixed Twital and Twig syntax to use the Twig ``trans`` tag.
(Of course ``trans`` is available as Twital attribute ``trans`.<tags/trans>`_, this was just an example).

Prerequisites
*************

Twital needs at least **Twig 1.10** to run and.

Installation
************

The recommended way to install Twig is via Composer.

Using  ``composer require`` command

.. code-block:: bash

    composer require goetas/twital:1.0.*

Adding dependency to your ``composer.json`` file

.. code-block:: js

    {
        "require": {
            ..
            "goetas/twital":"1.0.*",
            ..
        }
    }

.. note::

    To learn more about composer please refer to its original site (https://getcomposer.org/).

Basic API Usage
***************

This section gives you a brief introduction to the PHP API for Twig.

.. code-block:: php

    require_once '/path/to/vendor/autoload.php';

    $loader = new Twig_Loader_Filesystem('/path/to/templates');
    $twig = new Twig_Environment($loader);

    $twital = new Twig_Environment($twig);

    echo $twital->render('template.html', array('name' => 'Fabien'));

Twital uses Twig to compile and render templates, so Twital performance is exactly the same of any other Twig Template.


Basic API Usage
***************

.. toctree::
   :maxdepth: 2

   api
   extending
   tags/index Tags reference