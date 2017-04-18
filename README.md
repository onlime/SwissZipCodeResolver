# Onlime's Swiss Zip Code Resolver

A simple zip code lookup library to get information like city, commune, canton, and coordinates (LV03) for a Swiss zip code (PLZ).

## Installation

PHP versions 5.6 up to PHP 7.1 are currently supported.

The [zip](http://php.net/zip) extension is required. PHP should be compiled with zip support by using the `--enable-zip` configure option.

It is recommended to use [Composer](https://getcomposer.org) to install the library.

```bash
$ composer require onlime/swiss-zip-code-resolver
```

You can also use any other [PSR-4](http://www.php-fig.org/psr/psr-4/) compliant autoloader.


## Usage

* Create `Resolver` object
```
$Resolver = new Onlime\SwissZipCodeResolver\Resolver();
```

* Call `lookup()` method
```
$Result = $Resolver->lookup($zipcode);
```

* Sample of a `Result` object:
```
Onlime\SwissZipCodeResolver\Result Object
(
    [zipcode] => 8046
    [city] => Zürich
    [extraDigit] => 0
    [commune] => Zürich
    [bfsNr] => 261
    [canton] => ZH
    [east] => 680711
    [north] => 252925
    [validZipCode] => 1
)
```

* You may choose from 5 different return types. The types are array, object, json, serialize and xml. By default it is object. If you want to change that call the format method before calling the parse method or provide the format to the constructor. If you are not using object and an error occurs, then exceptions will not be trapped within the response and thrown directly.
```
$Resolver->setFormat('json');
$Resolver = new Onlime\SwissZipCodeResolver\Resolver('json');
```

* The `Resolver` supports the fluent interface on its setters, e.g.:
```
$Resolver = new Onlime\SwissZipCodeResolver\Resolver();
$Resolver
    ->setCachePath('/tmp')
    ->setCacheTime(86400)
    ->setFormat('json');
$json = $Resolver->lookup(8046);
```

## Credits

The data is provided and updated by GEO.ADMIN.CH:

- [GEO.ADMIN.CH - Geoinformationsplattform der Schweizerischen Eidgenossenschaft](http://data.geo.admin.ch/)
- [Schweizerisches Katasterwesen - Amtliches Ortschaftenverzeichnis](https://www.cadastre.ch/de/services/service/plz.html)
- [PLZO_CSV_LV03.zip](http://data.geo.admin.ch/ch.swisstopo-vd.ortschaftenverzeichnis_plz/PLZO_CSV_LV03.zip)


## Issues

Please report any issues via https://github.com/onlime/SwissZipCodeResolver/issues


## LICENSE and COPYRIGHT

Copyright (c) 2007 - 2016 Onlime Webhosting (https://www.onlime.ch)

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
