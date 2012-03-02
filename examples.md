
Some time ago, I created some methods to manipulate and render various pieces of an html document. It’s evolved into a pseudo document model. Originally written for CodeIgniter, I've refactored it for FuelPHP.

Here an example of working with the page title:
<pre>
<code>
Document::set('title', 'My Site');

// then somewhere else you can do this
Document::append('title', 'My Site');

//then in the view
echo Document::render('title');

// and get this
<title>My Site : Page 2</title>

// you can also prepend a value
Document::prepend('title', 'Some Text');

// set the title separator like this
Document::set('separator', ' :: ');
</code>
</pre>

Here's an example of rendering the DTD:

<pre>
<code>
// in a controller or anywhere else
Document::doctype = 'xhtml11';

//then in the view
echo Document::render('doctype');

// or you can do this
echo Document::doctype('xhtml11');
</code>
</pre>

How about rendering a complete html opening tag:

<pre>
<code>
echo Document::render('htmlopen');

// to get this based on the doctype, language, and direction
&lt;html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
</code>
</pre>

The following Asset methods are wrapped: css, js, and img

<pre>
<code>
// See the FuelPHP documentation for more information

// to add a stylesheet
Document::css('mycss.css');

// to render stylesheets
echo Document::css();
</code>
</pre>

You could use it as a global registry:

<pre>
<code>
// create a global variable
Document::set('myVar', 'myValue');

// access the global variable
$myVar = Document::get('myVar');
</code>
</pre>

Create a container as an array and append/prepend value:

<pre>
<code>
// Create the container
Document::set('myBucket' = array());

// add some values to the container
Document::append('myBucket', 'myValue1');
Document::append('myBucket', 'myValue2');

// prepend a value to the container
Document::prepend('myBucket', 'myValueA');

</code>
</pre>

For more information, see the inline documentation.