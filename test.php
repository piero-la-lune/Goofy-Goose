<?php

define('PHPPREFIX', '<?php /* '); # Prefix to encapsulate data in php code.
define('PHPSUFFIX', ' */ ?>'); # Suffix to encapsulate data in php code.

file_put_contents('test2.json', PHPPREFIX.base64_encode(gzdeflate(serialize(json_decode(file_get_contents('test.json'), true)))).PHPSUFFIX);

?>