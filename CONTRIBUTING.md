# How to contribute

## Recommended local development

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
