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
* Document Rendering Class
* 
* A document class that extends DocumentAbstract and contains rendering methods
*
* @extends abstractDocument
*/
class Document extends DocumentAbstract
{
	/**
	 * Render a DTD
	 * 
	 * @param string
	 * @return NULL|string
	 */
	static public function renderDtd($doctype=null)
	{
		return self::renderDoctype($doctype);
	}
	
	/**
	 * Render a DTD
	 * 
	 * @param string
	 * @return NULL|string
	 */
	static public function renderDoctype($doctype=null)
	{
		\Config::load('doctypes', true);
		$doctypes = \Config::get('doctypes');
		if(is_array($doctypes) && isset($doctypes[$doctype]))
			self::$doctype = $doctype;

		$result = \Html::doctype(self::$doctype);
		if(false === $result)
		{
			$doctype = $doctype ? $doctype : false;
			$doctype = $doctype ? $doctype : self::$doctype;
			$doctype = $doctype ? $doctype : 'empty';
			if(self::$logging)
				logger(\Fuel::L_WARNING, 'Can not render ' . $doctype . ' doctype', __METHOD__);
			return null;
		}
		return $result . self::$eol;
	}
	
	/**
	 * Render a well formed html open tag
	 * 
	 * @return string
	 */
	static protected function renderHtmlopen()
	{
		$html = '<html';
	
		$isXhtml = self::doctypeIsXhtml();
		if (true === $isXhtml)
			$html .= ' xmlns="http://www.w3.org/1999/xhtml"';
	
		$lang = '';
		if (!empty(self::$language))
		{
			$parts = explode('-', self::$language);
			$language = strtolower($parts[0]);
			if (in_array($language, self::$validLanguages))
			{
				$lang = ' lang="'.$language.'"';
				if(true === $isXhtml)
					$lang = ' xml:lang="'.$language.'"';
			}
		}
		$html .= $lang;
	
		$direction = htmlspecialchars(self::$direction);
		if(!empty($direction))
			$html .= " dir=\"$direction\"";
	
		$html .= ">" . self::$eol;
	
		return $html;
	}
	
	/**
	 * Render a character set meta tag
	 * 
	 * @return string
	 */
	static protected function renderCharset()
	{
		$charset = self::$charset ? htmlspecialchars(self::$charset) : 'UTF-8';
		return \Html::meta('Content-type', "text/html;charset='$charset'", 'http-equiv') . self::$eol;
	}
	
	/**
	 * Render a title tag
	 * 
	 * @param string
	 * @return string
	 */
	static protected function renderTitle($title=null)
	{
		$title = empty($title) ? implode(self::$separator, self::$title) : $title;
		$html = empty($title) ? null : html_tag('title', null, $title) . self::$eol;
		return $html;
	}
	
	/**
	 * Render the CSS container
	 * 
	 * @return string|NULL
	 */
	static protected function renderCss($css=null)
	{
		if (!empty($css))
		{
			$css = (array) $css;
			return parent::css($css);
		}
		
		if ( is_set(self::$css) && is_array(self::$css) && count(self::$css))
			return parent::css(self::$css);
		
		return null;
	}
	
	/**
	 * Render the Javascript container
	 * 
	 * @return string|NULL
	 */
	static protected function renderJs($js=null)
	{
		if (!empty($js))
		{
			$js = (array) $js;
			return parent::js($js);
		}
		
		if ( is_set(self::$js) && is_array(self::$js) && count(self::$js))
			return parent::js(self::$js);
		
		return null;
	}
}
