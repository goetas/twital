What is Twital?
============

Twital is a little "plugin" for Twig that change its templating language syntax, adding some shortcuts ad making it syntax more suitable for HTML based (XML, HTML5) templates.

To learn more about Twig you can read more on "Twig Official Site":(http://www.)

To understand better what are Twital benefits consider this Twig Template that simpy shows a listo of users from an array.

```jinja
{% if users %}
<div>
    <h1>Members</h1>
    <ul>
        {% for user in users %}
        <li>
            {{ user.name }}
        </li>
        {% endfor %}
    </ul>
</div>
{% endif %}
```

To express the same template using Twital plugin for Twig you can do:
```jinja
<div t:if="users">
    <h1>Members</h1>
    <ul t:for="user in users">
        <li>
            {{ user.name }}
        </li>
    </ul>
</div>
```

As you can see, using Twital tempalte is more readable and you have not to worry about opneing and closing block istructions, they are inherithed from HTML structure.

Of course Twital supports all other Twig functionalitiers as template ihretitane, translations, looping, escaping etc.

Here you can find a complete list of Twital attributes and elements.

If some Twig functionality is not avaiabel for Twital you can freely mix these two lyntaxeds:
```jinja
<div t:if="users">
    <h1>{% trans %}Members{% %}</h1>
    <ul t:for="user in users">
        <li>
            {{ user.name }}
        </li>
    </ul>
</div>
```

In the previous template we are mixing Twital and Twig syntax to use the Twig``trans`` tag (of course ``trans`` is anyway avaiable using Twital syntax).

Prerequisites
-------------

Twital needs at least **Twig 1.10** to run.

Installation
------------

The recommended way to install Twig is via Composer:

```
composer require goetas/twital:1.0.*
```


To learn more about composer please refer to its original site.

Basic API Usage
---------------

This section gives you a brief introduction to the PHP API for Twig.

```php
require_once '/path/to/vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader);

$twital = new Twig_Environment($twig);

echo $twital->render('template.html', array('name' => 'Fabien'));
```

Twital uses Twig to compile and reder templates, so Twital performace is esactly the same of any other Twig Template.
