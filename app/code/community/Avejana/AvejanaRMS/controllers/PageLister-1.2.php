<?php
/**
 * This program is licenced under the The GNU General Public License (GPL).
 *
 * Copyright (C) Kjetil Hårtveit 2010
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public
 * License for more details.
 * @license http://www.opensource.org/licenses/gpl-license.html
 *
 *---------------------------------------
 *
 * Makes pages overview out of a collection of items.
 *
 * Future ideas:
 * @todo implement cleaner error catching?
 * @todo add truncation
 * @todo add customizeable page values? Instead of 1 2 3, make it possible with A B C?
 *
 * @author Kjetil Hårtveit <kjetil@kjetil-hartveit.com> http://www.kjetil-hartveit.com
 * @version 1.2
 */
class PageLister
{
	protected $allItems = array();
	protected $page = 1;
	protected $pageList = array();

	/**
	 * Default options
	 *
	 * @var array
	 */
	protected $opts = array(
		'itemsPerPage'		 => 10,
		'urlFormat'				 => '%d',
		'customFormatting' => false,
		'urlCallback'			 => null,
		'urlCallbackArgs'	 => array(),
		'varPage'					 => '{pagenum}',
		'pageLabels'			 => array(),
		'prevAndNext'			 => true,
		'prev'						 => '&laquo;',
		'next'						 => '&raquo;'
	);

	/**
	 * <p>
	 *	Note that you can only use either normal url formatting, custom formatting or
	 *  callback formatting. Presedence order is: callback, custom formatting, normal formatting.
	 * </p>
	 *
	 * <h3>Possible options:</h3>
	 * <table>
	 *	 <tr>
	 *		<th>Type</th>
	 *		<th>Option</th>
	 *		<th>Default</th>
	 *		<th>Description</th>
	 *	 </tr>
	 *	 <tr>
	 *		 <td>string</td>
	 *		 <td>itemsPerPage</td>
	 *     <td>5</td>
	 *     <td>Number of items shown per page</td>
	 *   </tr>
	 *   <tr>
	 *     <td>string</td>
	 *     <td>urlFormat</td>
	 *     <td>%d</td>
	 *     <td>
	 *		  Here you can choose the format of the url. A replacement value for the pagenumber is
	 *			required. The syntax of the replacement value and how to set it depends on different
	 *			options.<br />
	 *			If the <i>customFormatting</i> option is set then the value of <i>varPage</i> is used.<br />
	 *			If the <i>urlCallback</i> option is set then at least one of the callback arguments must
	 *			contain the value of <i>varPage</i>.<br />
	 *			Lastly if neither of the mentioned options are set then <i>urlFormat</i> uses the sprintf
	 *			argument syntax (ie. %d).
	 *		 </td>
   *   </tr>
	 *	 <tr>
	 *		 <td>boolean</td>
	 *		 <td>customFormatting</td>
	 *     <td>false</td>
	 *     <td>
	 *			With custom formatting enabled you can define your own syntax for the pagenumber format.
	 *			This is handy in case your url format accidently includes format arguments
	 *			(like url characters encoded in utf-8) and which collides with the pagenumber argument.
	 *		 </td>
	 *   </tr>
	 *	 <tr>
	 *		 <td>string|array</td>
	 *		 <td>urlCallback</td>
	 *     <td>null</td>
	 *     <td>
	 *			If a callback is given then the pageurl will be formatted based on the result of this
	 *			callback.
	 *		 </td>
	 *   </tr>
	 *	 <tr>
	 *		 <td>array</td>
	 *		 <td>urlCallbackArgs</td>
	 *     <td>array()</td>
	 *     <td>
	 *			If the urlCallback option is set then you can specify additional callback arguments
	 *			with this option. <br />
	 *		  At least one of the callback arguments should contain the <i>varPage</i> value so the
	 *			corresponding pagenumber can be injected into the callback.
	 *		 </td>
	 *   </tr>
	 *	 <tr>
	 *		 <td>string</td>
	 *		 <td>varPage</td>
	 *     <td>{pagenum}</td>
	 *     <td>
	 *			The value of this option will be replaced by the pagenumber in numerous settings.<br />
	 *			It will be used when the option <i>customFormatting</i> is set, as well as when
	 *			a <i>urlCallback</i> is set. In the latter case then one of the callback arguments
	 *			should contain this value.
	 *		 </td>
	 *   </tr>
	 *   <tr>
	 *     <td>array</td>
	 *     <td>pageLabels</td>
	 *     <td>array()</td>
	 *     <td>Each individual page can be given a specific page label with this option. Each key in the array is the pagenumber and the value is the page label.</td>
   *   </tr>
	 *   <tr>
	 *     <td>boolean</td>
	 *     <td>prevAndNext</td>
	 *     <td>true</td>
	 *     <td>Whether or not to include the "previous" and "next" items in the page list</td>
   *   </tr>
	 *   <tr>
	 *     <td>string</td>
	 *     <td>prev</td>
	 *     <td>&laquo;</td>
	 *     <td>The text shown as "previous page"</td>
   *   </tr>
	 *   <tr>
	 *     <td>string</td>
	 *     <td>next</td>
	 *     <td>&raquo;</td>
	 *     <td>The text shown as "next page"</td>
   *   </tr>
	 * </table>
	 *
	 * @param array $allItems All of the items which you want to generate a pagelist from
	 * @param int $page Current page
	 * @param array $opts array with options given as key-value pair.
	 */
	function __construct($allItems, $page, $opts=array())
	{
		$this->setAllItems($allItems);
		$this->setPage($page);
		$this->setOpts(array_merge($this->getOpts(), $opts));
	}

	/**
	 * Makes page overview
	 *
	 * <p>The page list is an associative array consisting of the following key pairs:</p>
	 * <table>
	 *   <tr>
	 *	   <th>Type</th>
	 *	   <th>Key</th>
	 *	   <th>Value</th>
	 *   </tr>
	 *	 <tr>
	 *		 <td>string</td>
	 *		 <td>url</td>
	 *		 <td>The formatted url for the page. Note that when using the <i>prevAndNext</i> option and
				there is no valid previous or next page, then the url will be null. This is also the case
				if a pagelink points to the current page. You can use this info to remove links that the
				user should not click on.</td>
	 *   </tr>
	 *	 <tr>
	 *		 <td>int</td>
	 *		 <td>page</td>
	 *		 <td>The pagenumber</td>
	 *   </tr>
	 *	 <tr>
	 *		 <td>string</td>
	 *		 <td>label</td>
	 *		 <td>The page label</td>
	 *   </tr>
	 * </table>
	 *
	 * @return array Page list
	 */
	function makePageList()
	{
		$page = $this->getPage();
		$allItems = $this->getAllItems();
		$prevAndNext = $this->getOpt('prevAndNext');
		$pageLabels = $this->getOpt('pageLabels');
		$itemsPerPage = $this->getOpt('itemsPerPage');

		$numPages = ceil(count($allItems)/$itemsPerPage);

		$pageList = array();

		$prev = $page-1;
		$next = $page+1;

			if ($prevAndNext)
			{
				$pageList[] = array(
					'page'  => $prev,
					'url'	  => $prev>0 ? $this->formatUrl($prev) : null,
					'label' => $this->getOpt('prev')
				);
			}

			for ($i=1; $i<=$numPages; $i++)
			{
				$curUrl = $page!=$i ? $this->formatUrl($i) : null;
				$pageList[] = array(
					'page'  => $i,
					'url'	  => $curUrl,
					'label' => isset($pageLabels[$i]) ? $pageLabels[$i] : $i
				);
			}

			if ($prevAndNext)
			{
				$pageList[] = array(
					'page'  => $next,
					'url'	  => $next<=$numPages ? $this->formatUrl($next) : null,
					'label' => $this->getOpt('next')
				);
			}

		$this->setPageList($pageList);
		return $pageList;
	}

	/**
	 * Formats url based on options
	 *
	 * @param string $format
	 * @param int $page
	 * @return string
	 */
	protected function formatUrl($page)
	{
		$res = '';
			if ($this->getOpt('urlCallback')) {
				$args = str_replace($this->getOpt('varPage'), $page, $this->getOpt('urlCallbackArgs'));
				$res = call_user_func_array($this->getOpt('urlCallback'), $args);
			} else if ($this->getOpt('customFormatting')) {
				$res = str_replace($this->getOpt('varPage'), $page, $this->getOpt('urlFormat'));
			} else {
				$res = sprintf($this->getOpt('urlFormat'), $page);
			}

		return $res;
	}

	/**
	 * Returns current items
	 *
	 * @return array
	 */
	function getCurrentItems()
	{
		$allItems = $this->getAllItems();
		$page = $this->getPage();
		$itemsPerPage = $this->getOpt('itemsPerPage');

		return array_slice($allItems, ($page-1)*$itemsPerPage, $itemsPerPage);
	}

	/**
	 * Returns a single option
	 *
	 * @param string $opt
	 * @return mixed
	 */
	function getOpt($opt)
	{
		$opts = $this->getOpts();
			if (isset($opts[$opt]))
			{
				return $opts[$opt];
			}
		return null;
	}

	function setAllItems($allItems) { $this->allItems = $allItems; }
	function getAllItems() { return $this->allItems; }
	function setPage($page) { $this->page = $page; }
	function getPage() { return $this->page; }
	function setOpts($opts) { $this->opts = $opts; }
	function getOpts() { return $this->opts; }
	function setPageList($pageList) { $this->pageList = $pageList; }
	function getPageList() { return $this->pageList; }
}
?>