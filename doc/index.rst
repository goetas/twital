What is Twital?
###############

Twital is a small "plugin" for Twig_ (a template engine for PHP) 
that adds some shortcuts and makes Twig's syntax more suitable for HTML based (XML, HTML5, XHTML, SGML) templates.
Twital takes inspiration from PHPTal_, TAL_ and AngularJS_ (just for some aspects), 
mixing their language syntaxes with the powerful Twig templating engine system.


To better understand the Twital's benefits, consider the following **Twital** template, which
simply shows a list of users from an array:

.. code-block:: xml+jinja

    <ul t:if="users">
        <li t:for="user in users">
            {{ user.name }}
        </li>
    </ul>

To do the same thing using Twig, you need:

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


As you can see, the Twital template is **more readable**, **less verbose** and
and **you don't have to worry about opening and closing block instructions** 
(they are inherited from the HTML structure).


One of the main advantages of Twital is the *implicit* presence of control statements, which makes
templates more readable and less verbose. Furthermore, it has all Twig functionalities,
such as template inheritance, translations, looping, filtering, escaping, etc.
Here you can find a :doc:`complete list of Twital attributes and nodes.<tags/index>`.

If some Twig functionality is not directly available for Twital, 
you can **freely mix Twig and Twital** syntaxes. 

In the example below, we have mixed Twital and Twig syntaxes to use Twig custom tags:

.. code-block:: xml+jinja

    <h1 t:if="users">
        {% custom_tag %}
            {{ someUnsafeVariable }}
        {% endcustom_tag %}
    </h1>


Installation
************

There are two recommended ways to install Twital via Composer_:

* using the ``composer require`` command:

.. code-block:: bash

    composer require 'goetas/twital:1.*'

* adding the dependency to your ``composer.json`` file:

.. code-block:: js

    "require": {
        ..
        "goetas/twital":"*",
        ..
    }


Getting started
***************

First, you have to create a file that contains your template
(named for example ``demo.twital.html``):

.. code-block:: xml+jinja

    <div t:if="name">
        Hello {{ name }}
    </div>

Afterwards, you have to create a PHP script that instantiate the required objects:

.. code-block:: php

    <?php

    require_once '/path/to/composer/vendor/autoload.php';
    use Goetas\Twital\TwitalLoader;

    $fileLoader = new Twig_Loader_Filesystem('/path/to/templates');
    $twitalLoader = new TwitalLoader($fileLoader);
    
    $twig = new Twig_Environment($twitalLoader);
    echo $twig->render('demo.twital.html', array('name' => 'John'));


That's all!


.. note::

    Since Twital uses Twig to compile and render templates,
    their performance is the same.

Contents
********

.. toctree::
    :maxdepth: 3
    :hidden:
    
    tags/index
    templates
    api
    mistakes
    symfony

Contributing
************

This is an open source project: contributions are welcome. If your are interested,
you can contribute to documentation, source code, test suite or anything else!

To start contributing right now, go to https://github.com/goetas/twital and fork
it!

To improve your contributing experience, you can take a look into https://github.com/goetas/twital/blob/master/CONTRIBUTING.md 
inside the root directory of Twital GIT repository. 

Symfony2 Users
**************

If you are a Symfony2_ user, you can add Twital to your project using the 
TwitalBundle_.

The bundle integrates all most common functionalities as Assetic, Forms, Translations, Routing, etc.


Note
****

I'm sorry for the *terrible* english fluency used inside the documentation, I'm trying to improve it. 
Pull Requests are welcome.


.. _Twig: http://twig.sensiolabs.org/
.. _TwitalBundle: https://github.com/goetas/twital-bundle
.. _Symfony2: http://symfony.com
.. _Composer: https://getcomposer.org/
.. _TAL: http://en.wikipedia.org/wiki/Template_Attribute_Language
.. _PHPTal: http://phptal.org/
.. _AngularJS: http://angularjs.org/
.. _Twig: http://angularjs.org/
