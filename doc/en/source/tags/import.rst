``import``
==========

The Twital instruction for Twig ``import`` tag is ``t:import`` node.


Since Twig supports putting often used code into :doc:`macros<../tags/macro>`. These
macros can go into different templates and get imported from there.

There are two ways to import templates. You can import the complete template
into a variable or request specific macros from it.

Imagine we have a helper module that renders forms (called ``forms.html``):

.. code-block:: xml+jinja

    <t:macro name="input" args="name, value, type">
        <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" />
    </t:macro>
    <t:macro name="textarea" args="name, value">
        <textarea name="{{ name }}">{{ value|e }}</textarea>
    </t:macro>

To use your macro, you can do something like this:

.. code-block:: xml+jinja

    <t:import from="forms.html" alias="forms"/>
    <dl>
        <dt>Username</dt>
        <dd>{{ forms.input('username') }}</dd>
        <dt>Password</dt>
        <dd>{{ forms.input('password', null, 'password') }}</dd>
        {{ forms.textarea('comment') }}
    </dl>

If you want to import your macros directly into your template (without referring to it with a variable):

.. code-block:: xml+jinja

    <t:import from="forms.html" as="input as input_field, textarea"/>
    <dl>
        <dt>Username</dt>
        <dd>{{ input_field('username') }}</dd>
        <dt>Password</dt>
        <dd>{{ input_field('password', '', 'password') }}</dd>
    </dl>
    <p>{{ textarea('comment') }}</p>

.. tip::

    To import macros from the current file, use the special ``_self`` variable
    for the source.

.. note::

    For more information about ``import`` tag please refer to `Twig official documentation<http://twig.sensiolabs.org/doc/tags/autoescape.html>`_.

.. seealso:: :doc:`macro<../tags/macro>`
