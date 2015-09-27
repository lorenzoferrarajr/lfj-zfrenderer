# lfj-zfrenderer

A library to render PHP view scripts using the Zend Framework 2 PhpRenderer

[![Build Status](https://travis-ci.org/lorenzoferrarajr/lfj-zfrenderer.png?branch=master)](https://travis-ci.org/lorenzoferrarajr/lfj-zfrenderer)

__Warning__: This library is in development. Please specify commit hash if you want to experiment with it.

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
composer require lorenzoferrarajr/lfj-zfrenderer
```

## Usage

The terms _template_ and _view script_ are used interchangeably.

Templates are PHP scripts that mix HTML and PHP.

### Rendering a simple template

This is the `view/hello-world.phtml` view script. No PHP is present

```php
<!-- view/hello-world.phtml-->
Hello, World!
```

To render the template, the path to the view script file must be passed as first parameter of the `render` method

```php
$template = realpath('view/hello-world.phtml');

$renderer = new \Lfj\ZfRenderer\Service\Renderer();

/** @var \Zend\View\View $view */
$view = $renderer->render($template);

echo $view->getResponse()->getContent();

```

The `render` method returns an instance of `\Zend\View\View`. The content can be retrieved with `$view->getResponse()->getContent()`.

### Passing data to the template

A template can make use of data contained in variables. This is an example

```php
<!-- view/hello-name.phtml -->
Hello, <?=$name?>!
```

To pass data to the view script, an associative array must be provided as the second argument of the `render` method

```php
$template = realpath('view/hello-name.phtml');

$renderer = new \Lfj\ZfRenderer\Service\Renderer();
$view = $renderer->render($template, array('name' => 'World'));

echo $view->getResponse()->getContent();
```

### Including partials

It could be useful to include other view scripts from within another view script. This can be done passing a list of template path resolvers as the third argument of the `render` method.

This is the `view/hello-partial.phtml` view script which includes a partial

```php
Hello, <?=$this->partial('partial/name.phtml', array('name' => $name))?>!
```

and this is the `partial/name.phtml` file

```php
<?=$name?>
```

The code to render the whole thing is this

```php
$template = realpath('view/hello-partial.phtml');

$templatePathStack = new \Zend\View\Resolver\TemplatePathStack();
$templatePathStack->addPath(realpath('view'));

$renderer = new \Lfj\ZfRenderer\Service\Renderer();
$renderedContent = $renderer->render(
    $template,
    array('name' => 'World'),
    array($templatePathStack)
);

echo $renderedContent->getResponse()->getContent();
```

The `$templatePathStack` object is a list of directories in which other view scripts can be found.

### Adding helpers

Helpers can be added injecting an instance of `Zend\View\HelperPluginManager` using the `setHelperPluginManager` method.

The following view script uses the `name` helper.

```php
Hello, <?=$this->name()?>!
```

An instance of the following `PrintName` class will be used the in the view script when `$this->name()` is called

```php
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HelperInterface;

class PrintName extends AbstractHelper implements HelperInterface
{
    public function __invoke()
    {
        echo "World";
    }
}
```

The code that puts all this together is the following

```php
$template = realpath('view/hello-helper.phtml');

$helperPluginManager = new \Zend\View\HelperPluginManager();
$helperPluginManager->setService('name', new PrintName());

$renderer = new \Lfj\ZfRenderer\Service\Renderer();
$renderer->setHelperPluginManager($helperPluginManager);

$renderedContent = $renderer->render($template);

echo $renderedContent->getResponse()->getContent();
```

