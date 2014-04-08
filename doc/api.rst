Twital for Developers
=====================

This chapter describes the PHP API to Twital and not the template language.
It will be most useful as reference to those implementing the template interface to the application
and not those who are creating Twig templates.

Basics
------

Twital is a Twig Loader that compiles some templates before sending 
them back to Twig, that compiles and run the templates.

The first step to use Twital is to configure a valid Twig instance. Later then we can configure the
Twital object.

.. code-block:: php

    <?php
    use Goetas\Twital\TwitalLoader;

    $loader = new Twig_Loader_Filesystem('/path/to/templates');
    $twitalLoader = new TwitalLoader($loader);
    
    $twig = new Twig_Environment($twitalLoader, array(
        'cache' => '/path/to/compilation_cache',
    ));


By default Twital will compile only templates that ends with `.twital.xml`, `.twital.html`, `.twital.xhtml`
(using the right source adapter).
If you want to change it, adding more supported file formats, you can do something like this:

.. code-block:: php

    <?php
    
    $twital = new TwitalLoader($loader);
    $twital->addSourceAdapter('\.wsdl$', new XMLAdapter()); // handle .wsdl files as XML
    $twital->addSourceAdapter('\.htm$', new HTML5Adapter()); // handle .htm files as HTML5
    
.. note::

    Built in adapters are: `XMLAdapter`, `XHTMLAdapter` and `HTML5Adpater`.

.. note::

    To learn more about adapters, you can read the dedicated chapter :ref``Creating a SourceAdpater``.


Finally, to render a template with some variables, simply call the ``render()`` method on Twig instance:

.. code-block:: php

    <?php
    echo $twig->render('index.twital.html', array('the' => 'variables', 'go' => 'here'));


How does Twital work?
~~~~~~~~~~~~~~~~~~~~~

Twital uses Twig to render templates, but before pass a template to Twig,
Twital pre-compiles it in its own way.

The rendering of a template can be summarized into this steps:

* **Load** the template (done by Twig): If the template is already compiled, load it and go
  to the *evaluation* step, otherwise:
  
  * A `SourceAdapter` is chosen (from a set of configured adapters)
  * The **compiler.pre_load** event is fired. 
    Here, listeners can transform the template source code before DOM loading;
  * The `SourceAdapter` will `load` the source code into a valid DOMDocument_ object;
  * Fourth the **compiler.post_load** event is fired.
  * The compiler transforms the recognized attributes and nodes into relative Twig code;
  * The **compiler.pre_dump** event is fired.
  * The `SourceAdapter` will `dump` transform the compiled `DOMDocument` into Twig source code;
  * The **compiler.post_dump** event is fired. Here, listeners can perform some 
    non DOM transformations to the new template source code;
  * Pass the final template source code to Twig.
* Here Twig compiles the Twig source code into PHP code 
* **Evaluate** the template  (done by Twig): It basically means calling the ``display()``
  method of the compiled template and passing it the context.



Extending Twital
----------------

As Twig, Twital is very extensible and you can hook into it.
The best way to extend Twital is create your own "extension" and provide
your functionalities.


Creating a `SourceAdpater`
~~~~~~~~~~~~~~~~~~~~~~~~~~

 
The aim of source adapters is to "adapt" the a resource representation (usually a file or a string) 
to something that can be converted into a PHP `DOMDocument`_ object, 
and later the same object has to be "re-adapted" into its original representation.

If you want to provide a source adapter there is no need to create an extension,
you can simply implement the ``Goetas\Twital\SourceAdpater`` interface and use it.

.. literalinclude:: ../src/Goetas/Twital/SourceAdapter.php
   :language: php


To enable an adapter, you have to add it to Twital's loader instance by using the ``addSourceAdapter()`` method:

.. code-block:: php

    <?php
    use Goetas\Twital\TwitalLoader;
    
    $twital = new TwitalLoader($fileLoader);
    $twital->addSourceAdapter('/.*.xml$/i', new MyXMLAdapter());


A "naive" implementation of `MyXMLAdapter` can be:

.. code-block:: php

    <?php
    use Goetas\Twital\SourceAdapter;
    use Goetas\Twital\Template;
    
    class MyXMLAdapter implements SourceAdapter
    {
        public function load($source)
        {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $someMetadata = null; // you can also extract some metadata from original source
            
            return new Template($dom, $someMetadata);
        }
        
        public function dump(Template $template)
        {
            $metedata = $template->getMetadata();
            $dom = $template->getDocument();
    
            return $dom->saveXML();
        }
    }
 
- As you can see, ``load`` takes a string (containing a Twital template source code), and returns a ``Goetas\Twital\Template`` object.
 - ``Goetas\Twital\Template`` is a object that requires a `DOMDocument`_ as first argument and a generic variable as second argument (useful to hold some metadata extracted from the original source, later this data can be used during the "dump" phase).

- The ``dump`` method takes a ``Goetas\Twital\Template`` instance and returns a string. The returned string contains the template source code that will be be passed to Twig.

Creating an `Extension`
~~~~~~~~~~~~~~~~~~~~~~~

An extension is simply a container of functionalities that can be added to Twital.
The functionalities that can be added are node parsers, attribute parses and generic event listeners.

To create an extension you have to implement ``Goetas\Twital\Extension`` interface or extend the `Goetas\Twital\Extension\AbstractExtension` class.

This is the ``Goetas\Twital\Extension`` interface:

.. literalinclude:: ../src/Goetas/Twital/Extension.php
   :language: php


To enable your extensions, you have to add them to your's Twital instance by using the ``Goetas\Twital\Twital::addExtension()`` method:

.. code-block:: php

    <?php
    use Goetas\Twital\Twital;
    use Goetas\Twital\TwitalLoader;
    
    $twital = new Twital($twig);
    $twital->addExtension(new MyNewCustomExtension());
    
    $fsLoader = new Twig_Loader_Filesystem('/path/to/templates');
    $twitalLoader = new TwitalLoader($fsLoader, $twital);
    

.. tip::

    The bundled extensions are great examples of how extensions work.

.. note::

    In some special cases you may need to create a Twig extension instead of Twital one.
    To learn how to create a Twig extension you can read the  `Twig official documentation <http://twig.sensiolabs.org/doc/advanced.html>`_

Creating a `Node` parser
~~~~~~~~~~~~~~~~~~~~~~~~

Node parsers is aimed to handle any custom XML/HTML tag.

Suppose that we would to create an extension to handle a tag ``<my:hello>`` that simply echoes `"Hello {name}"`.

.. code-block:: xml

    <div class="red" xmlns:my="http://www.example.com/namespace">
        <my:hello name="John"/>
    </div>


First, you have to create your node parser that handles this "new" tag. 
To do this, you have to implement the ``Goetas\Twital\Node`` interface.

.. literalinclude:: ../src/Goetas/Twital/Node.php
   :language: php
   

The ``HelloNode`` class can be something like this:

.. code-block:: php

    <?php
    use Goetas\Twital\Node;
    use Goetas\Twital\Compiler;
    
    class HelloNode implements Node
    {
        function visit(\DOMElement $node, Compiler $twital)
        {
            $helloNode = $node->ownerDocument->createTextNode("hello");
            $nameNode = $twital->createPrintNode(
               $node->ownerDocument,
               "'".$node->getAttribute("name")."'"
            );

            $node->parentNode->replaceChild($nameNode, $node);
            $node->parentNode->insertBefore($helloNode, $nameNode);
        }
    }

Let's take a look to ``Goetas\Twital\Node::visit`` method signature:

- ``$node``: Gets the the `DOMElement`_ node of our ``my:hello`` tag.
- ``$twital``: Gets the Twital compiler.
- No return value for ``visit`` method will be required.

The aim of ``Goetas\Twital\Node::visit`` method is to transform the Twital template representation into Twig template syntax.

.. tip::

    ``$compiler->applyTemplatesToChilds()`` or ``$compiler->applyTemplates()`` or ``$compiler->applyTemplatesToAttributes()``
    can be very useful when you need to process recursivley the content of a node.

Finally you have to create your extension that ships your node parser.

.. code-block:: php

    <?php
    class MyExtension extends AbstractExtension
    {
        public function getNodes()
        {
            return array(
                'http://www.example.com/namespace'=>array(
                    'hello' => new HelloNode()
                )
            );
        }
    }

As you can see, the ``getNodes`` method have to return a two-level hash.

- The first level is the node namespace
- The second level is the node name

Of course, an extension can ship nodes that works with multiple namespaces.

To make ``xmlns:my`` declaration optional, you can also use the event listener  listener as ``Goetas\Twital\EventSubscriber\CustomNamespaceRawSubscriber``.

Creating an `Attribute` parser
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

An attribute parser is aimed to handle custom XML/HTML attributes.

Suppose that we would to create an extension to handle an attribute that simply appends some text inside a node,
removing its original content.

.. code-block:: xml

    <div class="red" xmlns:my="http://www.example.com/namespace">
        <p my:replace="rawHtmlVar">
            This text will be replaced with the content of "rawHtmlVar" variable.
        </p>
    </div>

To add your attribute parser, first you have to implement the ``Goetas\Twital\Attribute`` interface.

.. literalinclude:: ../src/Goetas/Twital/Attribute.php
   :language: php

The ``HelloAttribute`` class can be something like this:

.. code-block:: php

    <?php
    class HelloAttribute implements Attribute
    {
        function visit(\DOMAttr $attr, Compiler $twital)
        {
            $printNode = $twital->createPrintNode($attr->ownerNode->ownerDocument, $attr." | raw");
    
            $attr->ownerNode->appendChild($printNode);
            $node->parentNode->insertBefore($helloNode, $nameNode);
    
            return Attribute::STOP_NODE;
        }
    }

Let's take a look to ``Goetas\Twital\Attribute::visit`` method:

- ``$attr``: Gets the the `DOMAttr` node of our attribute.
- ``$twital``: Gets the Twital compiler.

The ``visit`` method have to transform the custom attribute into valid Twig code.

The ``visit`` method can also return one of the following constants:

- ``Attribute::STOP_NODE``: instructs the compiler to skip to next node (go to next sibling) stoping the processing of possible node childs.
- ``Attribute::STOP_ATTRIBUTE``: instructs the compiler to stop processing attributes of current node (continues normaily with child and sibling nodes)

Finally you have to create your extension that ships your attribute parser.

.. code-block:: php

    <?php
    class MyExtension extends AbstractExtension
    {
      public function getAttributes()
      {
         return array(
             'http://www.example.com/namespace'=>array(
                 'replace' => new HelloAttribute()
             )
         );
      }
    }

As you can see, the ``getAttributes`` method have to return a two-level hash.
- The first level is the attribute namespace
- The second level is the attribute name

Of course, an extension can ship nodes that works with multiple namespaces.

To make ``xmlns:my`` declaration optional, you can also use the event listener  listener as ``Goetas\Twital\EventSubscriber\CustomNamespaceRawSubscriber``.


Event Listeners
~~~~~~~~~~~~~~~

Another convenient way to hook into Twital is to create an event listener.

The possible entry points for listeners are:

- **compiler.pre_load**, fired before the source is passed to the source adapter. 
- **compiler.post_load**, fired when the source has been loaded into a DOMDocument.
- **compiler.pre_dump**, fired before source has been passed to source adapter for the dumping phase.
- **compiler.post_dumo**, fired when the DOMDocument has been dumped into a string by source adapter.


A valid listener must implement ``Symfony\Component\EventDispatcher\EventSubscriberInterface`` interface.

This is one example for a valid listener:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface {
        public static function getSubscribedEvents() {
            return array(
                'compiler.post_dump' => 'modifySource'                
                'compiler.pre_dump' => 'modifyDOM'
                
                'compiler.post_load' => 'modifyDOM',
                'compiler.pre_load' => 'modifySource'
            );
        }
        public function modifyDOM(TemplateEvent $event) {
            $event->getTemplate(); // do something with template (returns a Template instance)
        }
        public function modifySource(SourceEvent $event) {
            $event->getTemplate(); // do something with template (returns a string)
        }
    }
    

Event ``compiler.pre_load``
...........................

This event is fired just before a `SourceAdapeter` will try to load the source code into a `DOMDocument`_.
Here you can modify the source code, adapting it for a souce adpater.

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface
    {
        public static function getSubscribedEvents(){
            return array(
                'compiler.pre_load' => 'modifySource'
            );
        }
        public function modifySource(SourceEvent $event) {
            $str = $event->getTemplate();
            $str = str_replace("&nbsp;", "&#160;", $str);
            
            $event->setTemplate($str);
        }
    }


.. tip::
    
    Take a look to ``Goetas\Twital\EventSubscriber\CustomNamespaceRawSubscriber`` to see what can be done using this event.

Event ``compiler.post_load``
............................

This event is fired just after a ``Goetas\Twital\SourceAdapeter::load()`` call.
Here you can modify the `DOMDocument`_ object; it is a good point where apply modifications 
that can't be done by node parsers. 
You can also add nodes that will be parsed by Twital (eg: ``t:if`` attribute, ``t:include`` nodes, etc). 

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface {
        public static function getSubscribedEvents() {
            return array(
                'compiler.post_load' => 'modifyDOM'
            );
        }
        public function modifyDOM(TemplateEvent $event) {
            $template = $event->getTemplate();
            $dom = $template->getTemplate();
            
            $nodes = $dom->getElementsByTagName('mynode');
            
            // do something with $nodes
        }
    }

.. tip::
    
    Take a look to ``Goetas\Twital\EventSubscriber\CustomNamespaceSubscriber`` to see what can be done using this event.


Event ``compiler.pre_dump``
...........................

This event is fired when the Twital compilation process ends. 
It is sililar to ``compiler.post_load`` event except that you can't add elements that needs to be parsed
by Twital.

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface
    {
        public static function getSubscribedEvents(){
            return array(
                'compiler.pre_dump' => 'modifyDOM'
            );
        }
        public function modifyDOM(TemplateEvent $event) {
            $template = $event->getTemplate();
            $dom = $template->getTemplate();
            
            $body = $dom->getElementsByTagName('body')->item(0);
            // do something with body node...
        }
    }


Event ``compiler.post_dump``
............................

This event is fired just after ``Goetas\Twital\SourceAdapeter::dump()`` call. 
Here you can modify the final source code that will be passed to Twig. 

Here an example:

.. code-block:: php

    <?php
    class MySubscriber implements EventSubscriberInterface {
        public static function getSubscribedEvents() {
            return array(
                'compiler.post_dump' => 'modifySource'
            );
        }
        public function modifySource(SourceEvent $event) {
            $str = $event->getTemplate();
            $str.=" {# generated by Twital #}";
            
            $event->setTemplate($str);
        }
    }

    
.. tip::
    
    Take a look to ``Goetas\Twital\EventSubscriber\DOMMessSubscriber`` to see what can be done using this event.

Ship your listeners
...................
    
If you have created your listeners, you have to add them to Tiwtal.
To do this you have to create and extension that ships your listeners.

.. code-block:: php

    <?php
    class MyExtension extends AbstractExtension
    {
     public function getSubscribers()
     {
         return array(
             new MySubscriber(),
             new MyNewSubscriber()
         );
     }
    }    
    
    
    
.. _DOMDocument: http://www.php.net/manual/en/class.domdocument.php
.. _DOMElement: http://www.php.net/manual/en/class.domelement.php