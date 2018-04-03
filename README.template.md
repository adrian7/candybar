# Candybar

<coverage> <builddate> <license>

*Get the candies from your PHPUnit's tests results*

**(!) Warning: this project is under heavy development. 
Expect breaking changes with every new 0.x release.**

**Check [the roadmap](https://github.com/adrian7/candybar/blob/dev/ROADMAP.md) 
for upcoming features. Issues/pull requests are welcome.**

### Installation

```bash
$ composer require devlib/candybar --dev && vendor/bin/candybar init
``` 

### Running the bar

After installing (init), a folder called `candybar` will show up in your project's root directory.  
There you'll find the config.php, styles and themes folders. 
The `bar` section of the config file is used to add/remove commands supported by candybar and 
the styles folder is used to lookup styles when running the `coverage:style` command.


To get a list of all available commands run:
```bash
$ vendor/bin/candybar list
```

To get help for a specific command:
```bash
$ vendor/bin/candybar help [command]
```


### Available candies (commands)
 - `build:badge:date` generates build date badge with the current date/time
 - `coverage:style` applies a style to the html coverage presentation
 - `coverage:badge` generates badge from clover xml coverage stats
 - `license:badge` generates license badge, use arguments to set license and color 
 - `readme:add-badges` adds badges to the readme file using <badge-name> placeholders
 
### Examples

 - `vendor/bin/candybar coverage:style default`
 - `vendor/bin/candybar coverage:badge badges/coverage.svg --style=plastic`
 - `vendor/bin/candybar license:badge badges/license.svg --style=plastic`
 - `vendor/bin/candybar readme:add-badges badges --template=README.tpl.md --output=README.md --backup` 
 
### Making your own commands  
