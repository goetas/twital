``for``
=======

Loop over each item in a sequence. For example, to display a list of users
provided in a variable called ``users``:

.. code-block:: jinja

    <h1>Members</h1>
    <ul>
        <li t:for="user in users">{{ user.username|e }}</li>
    </ul>

.. code-block:: jinja

    <h1>Members</h1>
    <ul>
        {% for user in users %}
        <li>{{ user.username|e }}</li>
        {% endfor %}
    </ul>

As you can see, this two code snippet is very similar, the Twital just do not need opening and closing tags.