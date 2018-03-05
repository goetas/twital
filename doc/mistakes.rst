Common mistakes and tricks
--------------------------

Since Twital internally uses XML, you need to pay attention to some aspects while writing a template.
All templates must be XML valid (some exceptions are allowed...).

- All templates must have **one** root node.
  When needed, you can use the `t:omit` node to enclose other nodes.

  .. code-block:: xml

    <t:omit>
        <div>one</div>
        <div>two</div>
    </t:omit>
    

- A template must be well formatted (opening and closing nodes, entities, DTD, etc...). 
  Some aspects as namespaces, HTML5 & HTML entities, non-self closing tags can be "repaired", 
  but it is recommended to be closer to XML as much as possible.
  
  The example below lacks the `br` self closing slash, but using the HTML5 source adapter it can be omitted.

  .. code-block:: html

    <div>
        <br>
    </div>  
    
    
    

- The usage of `&` must follow XML syntax rules.

  .. code-block:: html

    <div>
        &amp; <!-- to output "&" you have to write "&amp;" -->
        &lt; <!-- to output "<" you have to write "&lt;" -->
        &gt; <!-- to output ">" you have to write "&gt;" -->
        
        <!-- you can use all numeric entities -->
        &#160; &#160;
        
        <!-- you should not use named entities (&euro;)-->
    </div>
    
- To be compatible with all browsers, the use of the `script` tag should be combined with  `CDATA` sections and script comments.

  .. code-block:: html

    <script>
    //<![CDATA[
    if ( 1 > 2 && 2 < 0){
        alert(' ok ')
    }
    //]]>
    </script>  
    <style>
    /*<![CDATA[*/
    head {
        color: red;
    }
    /*]]>*/
    </style>
