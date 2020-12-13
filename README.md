# Waiter for Hyperf

```
composer require gemini/waiter
```

## How to use

```php
<?php

$result = wait(function(){
    // Do something...
    return 'Hello World';
});

echo $result; // Hello World
```
