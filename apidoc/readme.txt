Generate the documentation from the source using phpDocumentor: 

1. go to http://www.phpdoc.org/ and download the phpDocumentor phar file
2. substituting the correct paths to project and doc folders below, run phpDocumentor.phar:
>php phpDocumentor.phar -d C:\projects\php-integration\src -t C:\projects\php-integration\apidoc

(make sure to delete the phpdoc-cache-xx files before you commit the documentation)