
[![Build Status](https://travis-ci.org/goetas/twital.png?branch=dev)](https://travis-ci.org/goetas/twital)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/goetas/twital/badges/quality-score.png?s=617ac058fc3c486427752fd3fb1f3931bca971ed)](https://scrutinizer-ci.com/g/goetas/twital/)
[![Code Coverage](https://scrutinizer-ci.com/g/goetas/twital/badges/coverage.png?s=de8d714be4a97b4b11bb44a2ff6601dbda86696c)](https://scrutinizer-ci.com/g/goetas/twital/)


What is Twital?
==============

Twital is a little "plugin" for Twig that change its templating language syntax, adding some shortcuts ad making it syntax more suitable for HTML based (XML, HTML5) templates.

To learn more about Twig you can read more on "Twig Official Site":(http://www.)

To understand better what are Twital benefits consider this Twig Template that simply shows a list of users from an array.


```jinja
{% if users %}
<div>
    <ul>
        {% for user in users %}
            <li>{{ user.name }}</li>
        {% endfor %}
    </ul>
</div>
{% endif %}
```

To express the same template using Twital plugin for Twig you can do:

```xml
<div t:if="users">
    <ul t:for="user in users">
        <li>{{ user.name }}</li>
    </ul>
</div>
```

As you can see, using Twital template is more readable and you have not to worry about opening and closing block instructions, they are inherited from HTML structure.

Of course Twital supports all other Twig functionalities as template inheritance, translations, looping, escaping etc.

Here you can find a complete list of Twital attributes and elements.

If some Twig functionality is not available for Twital you can freely mix these two syntaxes:

```jinja
<div t:if="users">
    <h1>{% trans %}Members{% endtrans %}</h1>
    <ul t:for="user in users">
        <li>
            {{ user.name }}
        </li>
    </ul>
</div>
```

In the previous template we are mixing Twital and Twig syntax to use the Twig``trans`` tag (of course ``trans`` is anyway avaiable using Twital syntax).

Prerequisites
*************

Twital needs at least **Twig 1.10** to run.

Installation
************

The recommended way to install Twig is via Composer.

Using  ``composer require`` command

```bash
composer require goetas/twital:1.0.*
```
Adding dependency to your ``composer.json`` file

```js
{
    "require":{
        ..
        "goetas/twital":"1.0.*",
        ..
}
```
.. note::

    To learn more about composer please refer to its original site.

Basic API Usage
***************

This section gives you a brief introduction to the PHP API for Twig.

```php
require_once '/path/to/vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader);

$twital = new Twig_Environment($twig);

echo $twital->render('template.html', array('name' => 'Fabien'));
```
Twital uses Twig to compile and render templates, so Twital performance is exactly the same of any other Twig Template.
=======
Twig tal wrapper

