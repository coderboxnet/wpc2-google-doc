# How to contribute

## Recommended local development

### Requierements

- PHP v7.4 or higher
- Composer
- Google account to test
- A WordPress instance to test

### Install

```bash
$ git clone git@github.com:coderboxnet/wpc2-google-doc.git
$ composer install
```

### Scripts

```bash
# When adding new classes, you need to update the autoload configuration
$ composer dump-autoload

# Check coding standards
$ composer run-script check-cs
```

### VS Code

#### Extensions

```json
{
	"recommendations": ["esbenp.prettier-vscode", "valeryanm.vscode-phpsab", "bmewburn.vscode-intelephense-client"]
}
```

#### Settings

```json
{
	"[json]": {
		"editor.defaultFormatter": "esbenp.prettier-vscode"
	},
	"editor.formatOnSave": true,
	"intelephense.environment.includePaths": ["./vendor/php-stubs/wordpress-stubs"],
	"phpsab.fixerEnable": true,
	"phpsab.snifferEnable": true,
	"phpsab.executablePathCBF": "./vendor/bin/phpcbf",
	"phpsab.executablePathCS": "./vendor/bin/phpcs",
	"phpsab.standard": "./phpcs.xml"
}
```
