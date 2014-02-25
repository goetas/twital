What is Twital?
###############

Twital is a little "plugin" for `Twig`_ that change its language syntax,
adding some shortcuts ad making it more suitable for HTML based (XML, HTML5, XHTML, SGML) templates.

You can learn more about `Twig reading its official documentation <http://twig.sensiolabs.org/documentation>`_.

To understand better what are Twital benefits, consider this Twig template
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

To express the same template using **Twital** you can do:

.. code-block:: xml+jinja

    <ul t:if="users">
        <li t:for="user in users">
            {{ user.name }}
        </li>
    </ul>

As you can see, using Twital template is **more readable**, **less verbose**
and you have **not to worry about opening and closing block instructions**,
they are inherited from HTML structure.


Of course Twital supports all other Twig's nice functionalities as template inheritance, translations,
looping, escaping etc. :doc:`Here you can find a complete list of Twital attributes and elements.<tags/index>`

If some Twig functionality is not available for Twital you can **freely mix Twig and Twital** syntaxes.
In the above example we have mixed Twital and Twig syntax to use the Twig ``autoescape`` tag.

.. code-block:: xml+jinja

    <h1>{% autoescape %}Members{% endautoescape %}</h1>
    <ul t:if="users">
        <li t:for="user in users">
            {{ user.name }}
        </li>
    </ul>

Of course ``autoescape`` is available as Twital attribute :doc:`t:autoescape<tags/autoescape>`, this was just an example.

Prerequisites
*************

Twital needs at least **Twig 1.10** to run.

Installation
************

The recommended way to install Twig is via Composer.

Using  ``composer require`` command

.. code-block:: bash

    composer require goetas/twital:1.0.*

Or adding its dependency to your ``composer.json`` file

.. code-block:: js

    "require": {
        ..
        "goetas/twital":"1.0.*",
        ..
    }

.. note::

    To learn more about composer please refer to its original site (https://getcomposer.org/).

Basic Usage
***********

This section gives you a brief introduction to Twital.

On the "design" side you have to create a file your template (named for example ``demo.twital.html``):

.. code-block:: xml+jinja

    <div t:if="name">
        Hello {{ name }}
    </div>

On the PHP side you have to create a PHP script and load a Twital instance:

.. code-block:: php

    require_once '/path/to/composer/vendor/autoload.php';
    use Goetas\Twital\Twital;

    $loader = new Twig_Loader_Filesystem('/path/to/templates');
    $twig = new Twig_Environment($loader);

    $twital = new Twital($twig);

    echo $twital->render('template.twital.html', array('name' => 'John'));



Thats all!


.. note::

    Since Twital uses Twig to compile and render templates,
    Twital performance is exactly the same of any other Twig template.

Contents
********

.. toctree::
    :maxdepth: 1

    tags/index
    api
    extending

.. _Twig: http://twig.sensiolabs.org/