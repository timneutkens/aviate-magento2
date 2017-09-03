# Aviate Magento 2

Aviate implementation for Magento 2.

## Installation

```
composer require weprovide/aviate-magento2
```

## Configuration

```js
const path = require('path')

// NAMESPACE/THEME Needs to match your exact theme namespace and name. Since the module will automatically load NAMESPACE/THEME.css
module.exports = {
    // For production we expect the assets to be in web/dist
    distLocations: [
        path.join(__dirname, 'app/design/frontend/NAMESPACE/THEME/web/dist')
    ],
    decorateConfig(config) {
        config.entry = Object.assign({}, config.entry, {
            'NAMESPACE/THEME': [
                path.join(__dirname, 'app/design/frontend/NAMESPACE/THEME/styles/styles.scss')
            ]
        })

        // Allows you to use relative paths to theme in your CSS and Javascript. For SASS you can use @import "~theme/path/to/file"
        config.resolve = {
            alias: {
                theme: path.join(__dirname, 'app/design/frontend/NAMESPACE/THEME')
            }
        }

        return config
    }
}
```

## Adding custom files

In some cases you'll want to add custom files. For example a React application. You can achieve this using [Magento 2 Interceptors](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/plugins.html).

`aviate.config.js` (in project root):

```js
const path = require('path')

// NAMESPACE/THEME Needs to match your exact theme namespace and name. Since the module will automatically load NAMESPACE/THEME.css
module.exports = {
    // For production we expect the assets to be in web/dist
    distLocations: [
        path.join(__dirname, 'app/design/frontend/NAMESPACE/THEME/web/dist')
    ],
    decorateConfig(config) {
        config.entry = Object.assign({}, config.entry, {
            'NAMESPACE/THEME': [
                path.join(__dirname, 'app/design/frontend/NAMESPACE/THEME/styles/styles.scss')
            ],
            // This part is the custom file
            'NAMESPACE/THEME/react': [
                'react-hot-loader/patch',
                path.join(__dirname, 'app/design/frontend/NAMESPACE/THEME/react/app.js'),
            ]
        })

        // Allows you to use relative paths to theme in your CSS and Javascript. For SASS you can use @import "~theme/path/to/file"
        config.resolve = {
            alias: {
                theme: path.join(__dirname, 'app/design/frontend/NAMESPACE/THEME')
            }
        }

        return config
    }
}
```

`etc/di.xml`:

```xml
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
	<type name="WeProvide\Aviate\Magento2\Block\DevServer">
		<plugin disabled="false" name="Add_React" sortOrder="10" type="NAMESPACE\MODULE\Plugin\AddReact"/>
	</type>
</config>
```

`Plugin/AddReact.php`:

```php
<?php

namespace NAMESPACE\MODULE\Plugin;

use WeProvide\Aviate\Magento2\Block\DevServer;

class AddReact
{
    public function afterGetFiles(
        DevServer $subject,
        $types
    ) {
        $aviate = $subject->aviate();
        $themePath = $aviate->getTheme()->getThemePath();

        if ($aviate->isDevMode()) {
            $types['js'][] = $aviate->getDevServerUrl($themePath . '/react.js');

            return $types;
        }

        $types['js'][] = $subject->getViewFileUrl('dist/' . $themePath . '/react.js');

        return $types;
    }
}

```
