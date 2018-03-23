**This folder is used to copy phpunit's default html report template.**

### Creating new themes

If you want to create a custom theme, you can either copy the files 
under `vendor/phpunit/php-code-coverage/src/Report/Html/Renderer/Template` under
a different folder or simply run:
 
```bash
$ candybar new:theme 'mycustomtheme'
```

### Distributing

You can distribute your themes as a zip files, and use the `--theme` 
option to select the theme when running candybar: 
```bash
$ candybar --theme <theme-zip-file-or-url.zip>
```

#### Packaging with composer

*This feature is under development. Pull requests are welcomed.*


