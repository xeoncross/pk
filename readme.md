## 1kB PHP Library

So how much can you cram into 1024 characters?

* SQL Database Layer (PostgreSQL, SQLite, & MySQL)
* Input Fetching
* Localization & Internationalization
* Validation
* [Your Library Here]

## Status

Currently a couple of the libraries are just over 1024 characters. So they need your help.

You can verify the file size of the code by using the `compact` file which will remove comments and newlines.

    $ php compact compressed/_.php _.php

## Requirements

* PHP 5.3+
* PDO if using the Database
* mb_string, [gettext](http://php.net/gettext), [iconv](http://www.php.net/manual/en/book.iconv.php), [ICU INTL](http://php.net/manual/en/book.intl.php) & SPL classes

*Where is the Locale Class?*

If you have errors about missing classes make sure you have the required PHP extensions installed.

Ubuntu/Debian: `$ sudo apt-get install php5-intl php5-mycrypt php-gettext`

## Open Source MIT License

Copyright (c) 2011 David Pennington

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
