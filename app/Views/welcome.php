{include header.php}


<?php if (isset($contents)): ?>
{$contents|noescape}
<?php else: ?>

<h1>Irfan's Engine</h1>

<p>A bare-minimum PHP framework, with the spirit with which the HTTP was invented.
focussing on the requests and the responses. A Swiss-knife for world-wide-web.</p>

<p>The objective of this library is to be a Bare-minimum, Embeddable and Educative.</p>

<p>Irfan's Engine now implements the PSR-7 classes and conforms to the validation
constraints imposed. You can break out of these constraints by using:</p>

<pre class="php">
// You can enable the hacker mode by defining this constant
define('HACKER_MODE', true);
</pre>

<p>If this constant is defined as a non false value, you can avoid all of the
validations, though certain constraints can not be eliminated, which are
essential for the proper functioning of the underlying system.</p>

<p>Now equiped with a console command <strong>ie</strong>, so that you can easily initialise
a basic application framework, create controllers, models, or views etc.</p>

<p class="note">Note: This documentation is just to get you started, you are encouraged to study
the code and the examples in the examples folder, which might help you get going
, by adding, extending or even writing your own classes and/or frameworks.</p>

<h2>Quick Start</h2>

<p> Its as easy as 1, 2, 3 :</p>

<h3>1. Installation</h3>

Install the latest version using composer.

<pre class="shell">
$ composer require irfantoor/engine
</pre>

<p class="note">Note: Irfan's Engine requires PHP 7.0 or newer.</p>


<h3>2. Basic Initialisation</h3>

Use the console command <strong>ie</strong> to create a basic app framework for 
you. When Irfan's Engine is installed, it will create a link to a shell 
command. Which can be used as follows to initialise the app.

<pre class="shell">
$ ./ie app:init
</pre>

<h3>3. Serve the app</h3>

App can be tested on basic php server using the following command:

<pre class="shell">
$ ./ie app:serve
</pre>

<p>Go to <a href="http://localhost:8000">http://localhost:8000</a> and voilà! your
welcome app alive. You can start experimenting by changing or adding the routes,
creating/modifying models, views, controllers etc.

Dont forget the shell command `ie` can be a big help in creating the models with
the associated databases, middlewares, controllers or views etc.

<?php endif; ?>
<?php $engine->trigger('footer'); ?>
{include footer.php}
