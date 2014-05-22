[![Build Status](https://travis-ci.org/goetas/twital.png?branch=dev)](https://travis-ci.org/goetas/twital)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/goetas/twital/badges/quality-score.png?s=617ac058fc3c486427752fd3fb1f3931bca971ed)](https://scrutinizer-ci.com/g/goetas/twital/)
[![Code Coverage](https://scrutinizer-ci.com/g/goetas/twital/badges/coverage.png?s=de8d714be4a97b4b11bb44a2ff6601dbda86696c)](https://scrutinizer-ci.com/g/goetas/twital/)

What is Twital?
==============

Twital is a small "plugin" for [Twig](http://twig.sensiolabs.org/) (a template engine for PHP) 
that adds some shortcuts and makes Twig's syntax more suitable for HTML based (XML, HTML5, XHTML, SGML) templates.
Twital takes inspiration from [PHPTal](http://phptal.org/), [TAL](http://en.wikipedia.org/wiki/Template_Attribute_Language) 
and [AngularJS](http://angularjs.org/) (just for some aspects), 
mixing their language syntaxes with the powerful Twig templating engine system.


To better understand the Twital's benefits, consider the following **Twital** template, which
simply shows a list of users from an array:

```xml
<ul t:if="users">
    <li t:for="user in users">
        {{ user.name }}
    </li>
</ul>
```

To do the same thing using Twig, you need:

```jinja
{% if users %}
    <ul>
        {% for user in users %}
            <li>
                {{ user.name }}
            </li>
        {% endfor %}
    </ul>
{% endif %}
```

As you can see, the Twital template is **more readable**, **less verbose** and
and **you don't have to worry about opening and closing block instructions** 
(they are inherited from the HTML structure).


One of the main advantages of Twital is the *implicit* presence of control statements, which makes
templates more readable and less verbose. Furthermore, it has all Twig functionalities,
such as template inheritance, translations, looping, filtering, escaping, etc.

If some Twig functionality is not directly available for Twital, 
you can **freely mix Twig and Twital** syntaxes. 

In the example below, we have mixed Twital and Twig syntaxes to use Twig custom tags:

```xml
<h1 t:if="users">
    {% custom_tag %}
        {{ someUnsafeVariable }}
    {% endcustom_tag %}
</h1>
```

You can also extend from a Twig template:

```xml
<t:extends from="layout.twig">
    
    <t:block name="content">
        Hello {{name}}!
    </t:block>
    
</t:extends>
```

Installation
-----------

There are two recommended ways to install Twital via [Composer](https://getcomposer.org/):

* using the``composer require`` command:

```bash
composer require 'goetas/twital:*'
```

* adding the dependency to your ``composer.json`` file:

```js
"require": {
    ..
    "goetas/twital":"*",
    ..
}
```

Documentation
-------------

Go here http://twital.readthedocs.org/ to read a more detailed documentation about Twital.


Getting started
---------------

First, you have to create a file that contains your template
(named for example `demo.twital.html`):

```xml
<div t:if="name">
    Hello {{ name }}
</div>
```

Afterwards, you have to create a PHP script that instantiate the required objects:

```php
require_once '/path/to/composer/vendor/autoload.php';
use Goetas\Twital\TwitalLoader;

$fileLoader = new Twig_Loader_Filesystem('/path/to/templates');
$twitalLoader = new TwitalLoader($fileLoader);

$twig = new Twig_Environment($twitalLoader);
echo $twig->render('demo.twital.html', array('name' => 'John'));
```

That's all!


Symfony2 Users
--------------

If you are a [Symfony2](http://symfony.com/) user, you can add Twital to your project using the 
[TwitalBundle](https://github.com/goetas/twital-bundle).

The bundle integrates all most common functionalities as Assetic, Forms, Translations, Routing, etc.

Note
----

I'm sorry for the *terrible* english fluency used inside the documentation, I'm trying to improve it. 
Pull Requests are welcome.



