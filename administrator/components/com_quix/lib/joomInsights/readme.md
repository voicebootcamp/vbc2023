# JoomInsights

- [Installation](#installation)
- [Insights](#insights)


## Installation

You can install JoomInsights Client in two ways, via composer and manually.

### 1. Composer Installation

Add dependency in your project (template/plugin/component/module):

```
composer require JoomInsights/client
```

Now add `autoload.php` in your file if you haven't done already.

```php
require __DIR__ . '/vendor/autoload.php';
```

### 2. Manual Installation

Clone the repository in your project.

```
cd /path/to/your/project/folder
git clone https://github.com/JoomInsights/client.git JoomInsights
```

Now include the dependencies in your plugin/theme.

```php
require __DIR__ . '/JoomInsights/src/Client.php';
```

## Credits
Created and maintained by [JoomInsights](https://www.themexpert.com)
Based on https://github.com/JoomInsights/client
