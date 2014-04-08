What is Twital?
###############

Twital is a little "plugin" for `Twig`_ (a template engine for PHP) that change its syntax, adding
some shortcuts and making it more suitable for HTML based (XML, HTML5, XHTML,
SGML) templates.
Twital takes inspiration from PHPTal_, TAL_ and AngularJS_ (just for some aspects), 
mixing their language syntaxes with the powerful Twig_ templating engine system.


To understand better what are Twital benefits, consider the following **Twital** template that
simply shows a list of users from an array.

.. code-block:: xml+jinja

    <ul t:if="users">
        <li t:for="user in users">
            {{ user.name }}
        </li>
    </ul>

To do the same thing using Twig, you will need:

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


As you can see, using Twital template is **more readable**, **less verbose** and
you have to **don't worry about opening and closing block instructions**, they
are inherited from HTML structure.


One of the main advantages of Twital is the *implicit* presence of control statements that make
templates more readable and less verbose. On the other hand, Twital has also all Twig functionalities
such as template inheritance, translations, looping, filtering, escaping etc.
Here you can find a  :doc:`complete list of Twital attributes and nodes.<tags/index>`.

If some Twig functionality is not available directly for Twital 
you can **freely mix Twig and Twital** syntaxes. 

In the above example we have mixed Twital and Twig syntax to extend form ``base.twig`` and to use the Twig ``autoescape`` tag end .

.. code-block:: xml+jinja

    <t:extends from="base.twig">
    
        <t:block name="content">
            <h1 t:if="users">
                {% autoescape %}
                    {{ someUnsafeVariable }}
                {% autoescape %}
            </h1>
        </t:block>
    
    </t:extends>
    
If Twig is well configured, you can alo extend from a Twital template:

.. code-block:: xml+jinja

    {% extends 'base.html.twital' %}
    
    {% block content %}
        <!-- this block is a Twital block stored in 'base.html.twital' template -->
    {% endblock %}
    


Installation
************

The recommended way to install Twital is via Composer_.

Using  ``composer require`` command:

.. code-block:: bash

    composer require 'goetas/twital:*'

Otherwise you can add the dependency to your ``composer.json`` file

.. code-block:: js

    "require": {
        ..
        "goetas/twital":"*",
        ..
    }



Basic Usage
***********

This section gives you a brief introduction to Twital.

On the "design" side you have to create a file that contains your template
(named for example ``demo.twital.html``):

.. code-block:: xml+jinja

    <div t:if="name">
        Hello {{ name }}
    </div>

On the PHP side you have to create a PHP script that creates the required object instances:

.. code-block:: php

    <?php

    require_once '/path/to/composer/vendor/autoload.php';
    use Goetas\Twital\TwitalLoader;

    $fileLoader = new Twig_Loader_Filesystem('/path/to/templates');
    $twitalLoader = new TwitalLoader($fileLoader);
    
    $twig = new Twig_Environment($twitalLoader);
    echo $twig->render('template.twital.html', array('name' => 'John'));


That's all!


.. note::

    Since Twital uses Twig to compile and render templates,
    Twital performance is exactly the same of any other Twig template.

Contents
********

.. toctree::
    :maxdepth: 3
    
    tags/index
    templates
    api
    mistakes
    symfony

Contributing
************

This is a open source project, contributions are welcome. If your are interested,
you can contribute to documentation, source code, test suite or anything else!

To start contributing right now, go to https://github.com/goetas/twital and fork
it!

You can read some tips to improve you contributing experience looking into https://github.com/goetas/twital/blob/master/CONTRIBUTING.md 
present inside the root directory of Twital GIT repository. 

Symfony2 Users
**************

If you are a Symfony2_ user, you can add Twital to your project using the 
TwitalBunbdle_.

The bundle integrates all most common functionalities as Assetic, Forms, Translations, Routing, etc.


Note
****

I'm sorry for the *terrible* english fluency used inside the documentation, I'm trying to improve it. 
Pull Requests are welcome.


.. _Twig: http://twig.sensiolabs.org/
.. _TwitalBunbdle: https://github.com/goetas/twital-bundle
.. _Symfony2: http://symfony.com
.. _Composer: https://getcomposer.org/
.. _TAL: http://en.wikipedia.org/wiki/Template_Attribute_Language
.. _PHPTal: http://phptal.org/
.. _AngularJS: http://angularjs.org/
.. _Twig: http://angularjs.org/
