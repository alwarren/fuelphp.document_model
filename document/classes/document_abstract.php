<?php
namespace Document;
/**
 * Document Model
 *
 * A system for storing document information and rendering tags and collections
 * of tags. This allows for a modular approach to manipulating and rendering
 * various components of an HTML document.
 *
 * The model is separated into two components:
 *  - an abstract document class with properties, containers, and business logic
 *  - a document class that extends DocumentAbstract and contains rendering methods
 *
 * LICENSE
 *
 * Copyright (c) 2012 Al Warren
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF S
 * UBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Document Model
 * @author Al Warren
 * @copyright Copyright (c) 2012, Al Warren
 * @link https://github.com/alwarren
 * @version 1.0 beta
 * @filesource
 */

/**
* Document Container Class
* 
* An abstract document class with properties, containers, and business logic
*
*/

class DocumentAbstract
{
	/**
	 * Document type
	 * 
	 * @var string
	 */
	static protected $doctype = null;

	/**
	* Character set
	*
	* @var string
	*/	
	static protected $charset = 'UTF-8';
	
	/**
	* Language
	*
	* @var string
	*/
	static protected $language = 'en';
	
	/**
	* Rendering direction
	*
	* @var string
	*/
	static protected $direction = 'ltr';
	
	/**
	* End of line character(s)
	*
	* @var string
	*/
	static protected $eol = "\12";
	
	/**
	* Tab character
	*
	* @var string
	*/
	static protected $tab = "\11";
	
	/**
	* Title Separator
	*
	* @var string
	*/
	static protected $separator = " : ";
	
	/**
	* Document Title
	*
	* @var string
	*/
	static protected $title = array();
	
	/**
	* Valid Languages
	*
	* @var array
	*/
	static protected $validLanguages = array();
	
	/**
	* Document Areas
	*
	* @var array
	*/
	static protected $areas = array();
	
	/**
	* Logging
	*
	* @var boolean
	*/
	static protected $logging = true;
	
	/**
	* Is Document registry initialized
	*
	* @var boolean
	*/
	public static $initialized = false;
	
	/**
	* Get method
	*
	* @param string
	* @return mixed
	*/
	static public function get($name)
	{
		switch ($name) {
			// disallowed for get
			case 'doctypes':
				if(self::$logging)
					logger(\Fuel::L_WARNING, "Getting static Document::$$name is not allowed", __METHOD__);
				return null;
			break;
					
			default:
				if(isset(self::$$name))
					return self::$$name;
			break;
		}
		if(self::$logging)
			logger(\Fuel::L_WARNING, "Document::$$name has not been set", __METHOD__);
	}
	
	/**
	* Set method
	*
	* @param string
	* @return mixed
	*/
	static public function set($name, $value)
	{
		switch ($name) {
			// disallowed for set
			case 'doctypes':
				if(self::$logging)
					logger(\Fuel::L_WARNING, "Setting static Document::$$name is not allowed", __METHOD__);
				return;
			break;
			case 'title':
				self::$title = array((string) $value);
			break;
			case 'logging':
				self::$logging = (bool) $value;
			break;
			
			default:
				self::$$name = $value;
			break;
		}
	}
	
	/**
	* Prepend a value to a container
	*
	* @param string
	* @param mixed
	*/
	static public function prepend($name, $value)
	{
		if(empty($value))
		{
			if(self::$logging)
				logger(\Fuel::L_WARNING, "Value can not be empty", __METHOD__);
			return null;
		}
	
		if(!isset(self::$$name))
		{
			if(self::$logging)
				logger(\Fuel::L_WARNING, "Document::$$name does not exist", __METHOD__);
			return null;
		}
	
		if(!is_array(self::$$name) || is_object(self::$$name))
		{
			if(self::$logging)
				logger(\Fuel::L_WARNING, "Document::$$name is not an array", __METHOD__);
			return null;
		}
	
		$list = self::$$name;
		array_unshift($list, $value);
		self::$$name = $list;
	}
	
	/**
	* Append a value to a container
	*
	* @param string
	* @param mixed
	*/
	static public function append($name, $value)
	{
		if(empty($value))
		{
			if(self::$logging)
				logger(\Fuel::L_WARNING, "Value can not be empty", __METHOD__);
			return null;
		}
	
		if(!isset(self::$$name))
		{
			if(self::$logging)
				logger(\Fuel::L_WARNING, "Document::$$name does not exist", __METHOD__);
			return null;
		}
	
		if(!is_array(self::$$name) || is_object(self::$$name))
		{
			if(self::$logging)
				logger(\Fuel::L_WARNING, "Document::$$name is not an array", __METHOD__);
			return null;
		}
	
		$list = self::$$name;
		$list[] = $value;
		self::$$name = $list;
	}
	
	/**
	* Genric render method
	*
	* @param string
	* @param mixed
	* @return string
	*/
	static public function render($method, $data=null)
	{
		$method = "render". ucfirst($method);
		if(!method_exists('Document', $method))
		{
			if(self::$logging)
				logger(\Fuel::L_WARNING, "Document::$method() does not exist", __METHOD__);
			return null;
		}
	
		return Document::$method($data);
	}

	/**
	* Is document type xhtml?
	*
	* @return boolean
	*/
	static public function doctypeIsXhtml()
	{
		return (stristr(self::$doctype, 'xhtml') ? true : false);
	}
	
	/**
	 * Try to detect the browser language
	 * 
	 * @return boolean
	 */
	static protected function guessLanguage()
	{
		$language = null;
		$prefered_languages = array();
		if(preg_match_all("#([^;,]+)(;[^,0-9]*([0-9\.]+)[^,]*)?#i",
		$_SERVER["HTTP_ACCEPT_LANGUAGE"],
		$matches,
		PREG_SET_ORDER))
		{
			$priority = 1.0;
			foreach($matches as $match) {
				if(!isset($match[3])) {
					$pr = $priority;
					$priority -= 0.001;
				} else {
					$pr = floatval($match[3]);
				}
				$prefered_languages[$match[1]] = $pr;
			}
			arsort($prefered_languages, SORT_NUMERIC);
	
			if(count($prefered_languages))
			{
				$languages = array();
				foreach($prefered_languages as $language => $priority) {
					$languages[] = $language;
				}
				$language = array_shift($languages);
				$doReplace = strstr($language, '_');
				$language = $doReplace ? str_replace('_', '-', $language) : $language;
				$parts = explode('-', $language);
				$lang = $parts[0];
				$language = $doReplace ? str_replace('-', '_', $language) : $language;
				$language = in_array($lang, self::$validLanguages ) ? $language : null;
			}
		}
		return $language;
	}
	
	/**
	 * Wrapper for Asset::css()
	 * 
	 * @return string
	 */
	static public function css()
	{
		if(func_num_args())
			return \Asset::css(func_get_args());
		else
			return \Asset::css();
	}
	
	/**
	 * Wrapper for Asset::js()
	 * 
	 * @return string
	 */
	static public function js()
	{
		if(func_num_args())
			return \Asset::js(func_get_args());
		else
			return \Asset::js();
	}
	
	/**
	 * Wrapper for Asset::img()
	 * 
	 * @return string
	 */
	static public function img()
	{
		if(func_num_args())
			return \Asset::img(func_get_args());
		else
			return \Asset::img();
	}
	
	/**
	 * Initialize the container
	 * 
	 */
	static public function _init()
	{
		if(static::$initialized)
		{
			return;
		}
		
		self::$validLanguages =
			array('aa', 'ab', 'af', 'ak', 'sq', 'am', 'ar', 'an', 'hy', 'as',
				'av', 'ae', 'ay', 'az', 'ba', 'bm', 'eu', 'be', 'bn', 'bh', 'bi', 
				'bs', 'br', 'bg', 'my', 'ca', 'ch', 'ce', 'zh', 'cu', 'cv', 'kw', 
				'co', 'cr', 'cs', 'da', 'dv', 'nl', 'dz', 'en', 'eo', 'et', 'ee', 
				'fo', 'fj', 'fi', 'fr', 'fy', 'ff', 'ka', 'de', 'gd', 'ga', 'gl', 
				'gv', 'el', 'gn', 'gu', 'ht', 'ha', 'he', 'hz', 'hi', 'ho', 'hr', 
				'hu', 'ig', 'is', 'io', 'ii', 'iu', 'ie', 'ia', 'id', 'ik', 'it', 
				'jv', 'ja', 'kl', 'kn', 'ks', 'kr', 'kk', 'km', 'ki', 'rw', 'ky', 
				'kv', 'kg', 'ko', 'kj', 'ku', 'lo', 'la', 'lv', 'li', 'ln', 'lt', 
				'lb', 'lu', 'lg', 'mk', 'mh', 'ml', 'mi', 'mr', 'ms', 'mg', 'mt', 
				'mn', 'na', 'nv', 'nr', 'nd', 'ng', 'ne', 'nn', 'nb', 'no', 'ny', 
				'oc', 'oj', 'or', 'om', 'os', 'pa', 'fa', 'pi', 'pl', 'pt', 'ps', 
				'qu', 'rm', 'ro', 'rn', 'ru', 'sg', 'sa', 'si', 'sk', 'sl', 'se', 
				'sm', 'sn', 'sd', 'so', 'st', 'es', 'sc', 'sr', 'ss', 'su', 'sw', 
				'sv', 'ty', 'ta', 'tt', 'te', 'tg', 'tl', 'th', 'bo', 'ti', 'to', 
				'tn', 'ts', 'tk', 'tr', 'tw', 'ug', 'uk', 'ur', 'uz', 've', 'vi', 
				'vo', 'cy', 'wa', 'wo', 'xh', 'yi', 'yo', 'za', 'zu'
			);
		
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			self::$language = self::guessLanguage();
		
		static::$initialized = true;
	}
}