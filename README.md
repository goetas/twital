[![Build Status](https://travis-ci.org/goetas/twital.png?branch=dev)](https://travis-ci.org/goetas/twital)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/goetas/twital/badges/quality-score.png?s=617ac058fc3c486427752fd3fb1f3931bca971ed)](https://scrutinizer-ci.com/g/goetas/twital/)
[![Code Coverage](https://scrutinizer-ci.com/g/goetas/twital/badges/coverage.png?s=de8d714be4a97b4b11bb44a2ff6601dbda86696c)](https://scrutinizer-ci.com/g/goetas/twital/)

What is Twital?
==============

Twital is a little "plugin" for Twig (a template engine for PHP) that change its language syntax, adding
some shortcuts ad making it more suitable for HTML based (XML, HTML5, XHTML,
SGML) templates.

To understand better what are Twital benefits, consider the following **Twital** template that
simply shows a list of users from an array.

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

As you can see, using Twital template is **more readable**, **less verbose** and
you have **not to worry about opening and closing block instructions**, they
are inherited from HTML structure.


Of course Twital supports all other Twig's nice functionalities as template
inheritance, translations, looping, filtering, escaping etc.

If some Twig functionality is not available directly for Twital you can
**freely mix Twig and Twital** syntaxes. In the above example we have mixed
Twital and Twig syntax to use the Twig ``autoescape`` tag.

```jinja
<h1 t:if="users">
    {% spaceless %}
        Members
    {% spaceless %}
</h1>
```

Prerequisites
------------

Twital needs at least **Twig 1.10** and **PHP 5.3.8** to run.

Installation
-----------

The recommended way to install Twig is via Composer.

Using  ``composer require`` command

```bash
composer require goetas/twital:1.0.*
```

Or adding its dependency to your ``composer.json`` file

```js
"require": {
    ..
    "goetas/twital":"1.0.*",
    ..
}
```

Documentation
-------------

Go here http://twital.readthedocs.org/ to read a more detailed documentation about Twital.


Basic Usage
-----------

This section gives you a brief introduction to Twital.

On the "design" side you have to create a file that contains your template
(named for example ``demo.twital.html``):

```jinja
<div t:if="name">
    Hello {{ name }}
</div>
```

On the PHP side you have to create a PHP script and load a Twital instance:

```php
require_once '/path/to/composer/vendor/autoload.php';
use Goetas\Twital\TwitalLoader;

$fs = new Twig_Loader_Filesystem('/path/to/templates');
$twitalLoader = new TwitalLoader($fs);

$twig = new Twig_Environment($twitalLoader);
echo $twig->render('template.twital.html', array('name' => 'John'));
```


Symfony2 Users
--------------

If you are a Symfony2 user, you can add Twital to your project using the TwitalBunbdle:https://github.com/goetas/twital-bundle.

The bundle integrates all most common functionalies as Assetic, Forms, Translations etc.

Note
----

I'm sorry for the *terrible* english fluency used inside the documentation, I'm trying to improve it. 
Pull Requests are welcome.
