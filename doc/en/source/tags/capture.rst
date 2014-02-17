``capture``
=======

You can use ``capture`` attribute to  to 'capture' chunks of text:

.. code-block:: jinja

    <div id="pagination" t:capture="foo">
        ... any content ..
    </div>


All contents inside "pagination" div will be captured and saved iside a variable named `foo`.