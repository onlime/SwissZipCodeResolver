<?php
/**
 * Swiss Zip Code Resolver
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   Onlime
 * @package    SwissZipCodeResolver
 * @copyright  Copyright (c) 2007 - 2016 Onlime Webhosting (https://www.onlime.ch)
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Onlime\SwissZipCodeResolver;

use Onlime\SwissZipCodeResolver\Exception\ConnectException;
use Onlime\SwissZipCodeResolver\Exception\Exception;
use Onlime\SwissZipCodeResolver\Exception\NotFoundException;
use Onlime\SwissZipCodeResolver\Exception\OpenFileException;
use Onlime\SwissZipCodeResolver\Exception\WriteFileException;

/**
 * SwissZipCodeResolver
 *
 * @category   Onlime
 * @package    SwissZipCodeResolver
 * @copyright  Copyright (c) 2007 - 2016 Onlime Webhosting (https://www.onlime.ch)
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class Resolver
{
    /**
     * Output format 'object', 'array', 'json', 'serialize' or 'xml'
     *
     * @var string
     * @access protected
     */
    protected $format = 'object';

    /**
     * Should the exceptions be thrown or caught and trapped in the response?
     *
     * @var boolean
     * @access protected
     */
    protected $throwExceptions = false;

    /**
     * Should the cache file always be loaded from the server?
     *
     * @var boolean
     * @access protected
     */
    protected $reload = false;

    /**
     * Set cache path
     *
     * @var string
     * @access protected
     */
    protected $cachePath;

    /**
     * Life time of cached file
     *
     * @var int
     * @access protected
     */
    protected $cacheTime = 2592000;

    /**
     * List of all zip codes
     *
     * @var array
     * @access protected
     */
    protected $zipCodeList = [];

    /**
     * Is the zip code lookup list already be loaded?
     *
     * @var boolean
     * @access protected;
     */
    protected $loaded = false;

    /**
     * URL to zip code lookup list data
     *
     * @var string
     * @access protected
     */
    protected $dataUrl = 'http://data.geo.admin.ch/ch.swisstopo-vd.ortschaftenverzeichnis_plz/PLZO_CSV_LV03.zip';

    /**
     * Creates a SwissZipCodeResolver object
     *
     * @param  string $format
     * @param  string $cachePath
     */
    public function __construct($format = 'object', $cachePath = null)
    {
        $this->setFormat($format);
        $this->setCachePath($cachePath);
    }

    /**
     * Get current output format.
     * 
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set output format
     *
     * You may choose between 'object', 'array', 'json', 'serialize' or 'xml' output format
     *
     * @param  string $format
     * @return Resolver
     */
    public function setFormat($format = 'object')
    {
        $this->format = filter_var($format, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * Get the throwExceptions flag.
     * 
     * @return boolean
     */
    public function isThrowExceptions()
    {
        return $this->throwExceptions;
    }

    /**
     * Set the throwExceptions flag.
     *
     * Set whether exceptions encountered during processing should be thrown
     * or caught and trapped in the response as a string message.
     *
     * Default behaviour is to trap them in the response; call this
     * method to have them thrown.
     *
     * @param  boolean $throwExceptions
     * @return Resolver
     */
    public function setThrowExceptions($throwExceptions = false)
    {
        $this->throwExceptions = filter_var($throwExceptions, FILTER_VALIDATE_BOOLEAN);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isReload()
    {
        return $this->reload;
    }

    /**
     * Set the reload flag
     *
     * Set if the zip code lookup list should be reloaded independent from
     * the cache time.
     *
     * @param  boolean $reload
     * @return Resolver
     */
    public function setReload($reload = true)
    {
        $this->reload = filter_var($reload, FILTER_VALIDATE_BOOLEAN);
        return $this;
    }

    /**
     * Get cache path
     *
     * @return string
     */
    public function getCachePath()
    {
        return $this->cachePath;
    }

    /**
     * Set cache path
     *
     * @param  string $path
     * @return Resolver
     */
    public function setCachePath($path = null)
    {
        if (is_null($path)) {
            $this->cachePath = sys_get_temp_dir();
        } else {
            $this->cachePath = filter_var($path, FILTER_SANITIZE_STRING);
        }
        return $this;
    }

    /**
     * Get the cache time
     *
     * @return int
     */
    public function getCacheTime()
    {
        return $this->cacheTime;
    }

    /**
     * Set the cache time
     *
     * By default the cache time is 2592000 (equal to 30 days)
     *
     * @param  int $cacheTime
     * @return Resolver
     */
    public function setCacheTime($cacheTime = 2592000)
    {
        $this->cacheTime = filter_var($cacheTime, FILTER_VALIDATE_INT);
        return $this;
    }
    
    /**
     * Checks if given zip code is valid
     *
     * @param  int $zipcode
     * @return boolean
     */
    public function isValid($zipcode)
    {
        $this->setFormat('object');
        $Result = $this->lookup($zipcode);

        return $Result->validZipCode;
    }

    /**
     * Tries to lookup a zip code and returns its data in the expected format.
     *
     * @throws Exception if throwExceptions = true
     * @param  int $zipcode
     * @return array|Result|string
     */
    public function lookup($zipcode)
    {
        try {
            if (false === $this->loaded) {
                $this->load();
            }

            if (isset($this->zipCodeList['content'][$zipcode])) {
                $record = $this->zipCodeList['content'][$zipcode];
                $Result = new Result(
                    $zipcode,
                    $record['city'],
                    $record['extraDigit'],
                    $record['commune'],
                    $record['bfsNr'],
                    $record['canton'],
                    $record['east'],
                    $record['north'],
                    true
                );
            } else {
                throw new NotFoundException("Zip code $zipcode was not found.");
            }
        } catch (Exception $e) {
            if ($this->throwExceptions) {
                throw $e;
            }

            $Result        = new Result($zipcode);
            $Result->error = $e->getMessage();
        }

        return $Result->get($this->format);
    }

    /**
     * Checks if the zip code lookup list exists or cached time is reached
     *
     * @throws OpenFileException
     * @throws WriteFileException
     * @return void
     */
    private function load()
    {
        $cacheFile = $this->cachePath . '/swiss-zipcodes-list.txt';

        if (file_exists($cacheFile)) {
            $this->zipCodeList = unserialize(file_get_contents($cacheFile));

            // will reload tld list if timestamp of cache file is outdated
            if (time() - $this->zipCodeList['timestamp'] > $this->cacheTime) {
                $this->reload = true;
            }
        }

        // check connection - if there is no internet connection skip loading
        $cacheFileExists = file_exists($cacheFile);

        if (! $cacheFileExists || $this->reload === true) {
            $this->grabData($cacheFileExists);
            $file = fopen($cacheFile, 'w+');

            if ($file === false) {
                throw new OpenFileException('Could not open cache file.');
            }

            if (fwrite($file, serialize($this->zipCodeList)) === false) {
                throw new WriteFileException('Could not open cache file for writing.');
            }

            fclose($file);
        }

        $this->loaded = true;
    }

    /**
     * Grab zip code lookup list from server and parse CSV to to array.
     *
     * @param  boolean $cacheFileExists
     * @throws ConnectException
     * @throws OpenFileException
     * @return void
     */
    private function grabData($cacheFileExists)
    {
        $fileName = 'PLZO_CSV_LV03';
        $zipFile  = sprintf('%s/%s.zip', $this->cachePath, $fileName);

        if (false === copy($this->dataUrl, $zipFile)) {
            if (!$cacheFileExists) {
                throw new ConnectException('Could not grab file from server.');
            }
            // silently ignore the failed download, we're going to use the cached file instead
            return;
        }

        $csvData = trim(file_get_contents("zip://$zipFile#$fileName/$fileName.csv"));
        if (false === $csvData) {
            throw new OpenFileException('Could not extract $fileName.csv from $fileName.zip');
        }

        // parse CSV data
        $dataLines  = explode("\n", $csvData);
        array_shift($dataLines); // we don't need the headers
        $parsedData = [];
        foreach ($dataLines as $line) {
            list($city, $zipcode, $extraDigit, $commune, $bfsNr, $canton, $east, $north) = str_getcsv($line, ';');
            $parsedData[$zipcode] = [
                'zipcode'    => $zipcode,
                'city'       => $city,
                'extraDigit' => $extraDigit,
                'commune'    => $commune,
                'bfsNr'      => $bfsNr,
                'canton'     => $canton,
                'east'       => $east,
                'north'      => $north
            ];
        }

        $this->zipCodeList['content']   = $parsedData;
        $this->zipCodeList['timestamp'] = time();
    }
}
