Twig Internals
==============

As Twig, Twital is very extensible and you can hook into it.
The best way to extend Twital is create your own ``Extension`` and provide
your functionalities.

How does Twig work?
-------------------

Twital uses Twig to redener templates, but before pass a template to Twig,
Twital pre-compiles it in its own way.

The rendering of a  template can be summarized into this steps:

* **Load** the template (done by Twig): If the template is already compiled, load it and go
  to the *evaluation* step, otherwise:

  * First, the **PreFilters** callbacks can transform the template source code before DOM loading;
  * Second, the **DOMLoader** transform the source code into a valid DOMDocument object;
  * Third, the compiler transform the recognized attributes and nodes into relative Twig code;
  * Fourth, the **DOMDumper** transform the compiled new DOMDocument into Twig source code;
  * Fifth, The **PostFilters** callbacks can transform the new template source code before send it to Twig;
  * Sixth, pass the template to Twig:
      * First, the **lexer** tokenizes the template source code into small pieces
        for easier processing;
      * Then, the **parser** converts the token stream into a meaningful tree
        of nodes (the Abstract Syntax Tree);
      * Eventually, the *compiler* transforms the AST into PHP code.

* **Evaluate** the template  (done by Twig): It basically means calling the ``display()``
  method of the compiled template and passing it the context.