# Handle Manager for concrete5


## Database setup

Create a table like this (you can customize the system_... fields):

```sql
CREATE TABLE HandleUsages (
  handle varchar(64) NOT NULL COMMENT 'The package handle',
  system_prb tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Handle used in: concrete5 marketplace',
  system_translate tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Handle used in: translate.concrete5.org',
  PRIMARY KEY (handle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of package handle usages';
```


## Environment setup

Set the environment variables listed in [`HandleStoreFactory.php`](https://github.com/mlocati/concrete5_handle_manager/blob/master/src/HandleStoreFactory.php).


## Usage

```php

use concrete5\HandleManager\HandleStoreFactory;

$handle = 'the-package-handle';

// Get the instance
$factory = new HandleStoreFactory();
$store = $factory->create();

// Check if the handle is valid
if (!$store->isHandleValid($handle)) {
    throw new Exception('Invalid package handle');
}

// Check if a handle is in use
if ($store->isHandleUsedInSystem($handle, 'translate')) {
    // Users have two options:
    // 1. change the package handle, if the package is different that the one on the "translate" system
    // 2. say that the package is the same, so we can add add the handle to the "prb" system
    if ($case_1) {
        return;
    } elseif ($case_2) {
        $store->setHandleUsage($handle, 'prb', ['translate']);
    }
} else {
    // The package handle is not in use in the "translate" system
    $store->setHandleUsage($handle, 'prb');
}
```
