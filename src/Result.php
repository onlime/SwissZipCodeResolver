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

/**
 * SwissZipCodeResolverResult
 *
 * @category   Onlime
 * @package    SwissZipCodeResolver
 * @copyright  Copyright (c) 2007 - 2016 Onlime Webhosting (https://www.onlime.ch)
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class Result
{
    /**
     * Swiss zip code (PLZ)
     * 
     * @var int
     */
    public $zipcode;

    /**
     * City/town name (Ortschaftsname)
     * 
     * @var string
     */
    public $city;

    /**
     * Extra digit (Zusatzziffer)
     * 
     * @var int
     */
    public $extraDigit;

    /**
     * Commune name (Gemeindename)
     * 
     * @var string
     */
    public $commune;

    /**
     * Canton (Kantonskürzel)
     * 
     * @var string
     */
    public $canton;

    /**
     * East coordinate
     * 
     * @var int
     */
    public $east;

    /**
     * North coordinate
     * 
     * @var int
     */
    public $north;

    /**
     * Is the zip code valid
     * 
     * @var boolean
     */
    public $validZipCode;

    /**
     * Constructs a new object from resolved zip code by SwissZipCodeResolver
     * 
     * @param int $zipcode
     * @param string $city
     * @param int $extraDigit
     * @param string $commune
     * @param string $canton
     * @param int $east
     * @param int $north
     * @param bool $validZipCode
     */
    public function __construct($zipcode, $city = '', $extraDigit = 0, $commune = '', $canton = '', 
                                $east = 0, $north = 0, $validZipCode = false)
    {
        $this->zipcode      = $zipcode;
        $this->city         = $city;
        $this->extraDigit   = $extraDigit;
        $this->commune      = $commune;
        $this->canton       = $canton;
        $this->east         = $east;
        $this->north        = $north;
        $this->validZipCode = $validZipCode;
    }

    /**
     * Writing data to properties
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * Checking data
     *
     * @param  mixed $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->{$name});
    }

    /**
     * Reading data from properties
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }

        return null;
    }
    
    /**
     * Returns the result by format
     * 
     * @param  string $format
     * @return Result|array|string
     */
    public function get($format)
    {
        switch ($format) {
            case 'json':
                return $this->toJson();
                break;
            case 'serialize':
                return $this->serialize();
                break;
            case 'array':
                return $this->toArray();
                break;
            case 'xml':
                return $this->toXml();
                break;
            default:
                return $this;
        }
    }

    /**
     * Convert properties to json
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Convert properties to array
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Serialize properties
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Convert properties to xml by using SimpleXMLElement
     *
     * @return string
     */
    public function toXml()
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><parser></parser>'
        );

        $xml->addChild('zipcode', $this->zipcode);
        $xml->addChild('city', $this->city);
        $xml->addChild('extraDigit', $this->extraDigit);
        $xml->addChild('commune', $this->commune);
        $xml->addChild('canton', $this->canton);
        $xml->addChild('east', $this->east);
        $xml->addChild('north', $this->north);
        $xml->addChild('validZipCode', $this->validZipCode);
        
        return $xml->asXML();
    }
}