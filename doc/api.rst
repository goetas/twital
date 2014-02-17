Twig for Developers
===================

This chapter describes the API to Twital and not the template language. It will be most useful as reference to those implementing the template interface to the application and not those who are creating Twig templates.

Basics
------

Twital wraps a Twig instance and uses Twig to compile and run templates.

The first step is configure a valid Twig instance, then we can configure the
Twital object.

.. code-block:: php


    $loader = new Twig_Loader_Filesystem('/path/to/templates');
    $twig = new Twig_Environment($loader, array(
        'cache' => '/path/to/compilation_cache',
    ));

    $twital = new Twital($twig);


By default Twital comes with HTML5 tokenizer enabled. If you want to change it
to XHTML you can do it:

.. code-block:: php

    $twital = new Twital($twig, 'xhtml');


Other options avaiable are ``xml``, ``html5`` and ``xhtml``.


This will create a template environment with the default settings and a

To load a template just have to call the ``loadTemplate()`` method which then
returns a ``Twig_Template`` instance::

.. code-block:: php

    $template = $twital->loadTemplate('index.html.twital');

To render the template with some variables, call the ``render()`` method::


.. code-block:: php

    echo $template->render(array('the' => 'variables', 'go' => 'here'));

.. note::

    The ``display()`` method is a shortcut to output the template directly.

You can also load and render the template in one fell swoop::

.. code-block:: php

    echo $twital->render('index.html.twital', array('the' => 'variables', 'go' => 'here'));


Using Extensions
----------------

Twital extensions are packages that add new features to Twital. Using an
extension is as simple as using the ``addExtension()`` method::

.. code-block:: php

    $twital->addExtension(new InternationalizationExtension());


