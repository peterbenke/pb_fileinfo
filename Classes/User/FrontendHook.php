<?php
namespace PeterBenke\PbFileinfo\User;

/**
 * Hook: Front end rendering
 * @author Peter Benke <info@typomotor.de>
 * @package PeterBenke\PbFileinfo\User
 */
class FrontendHook implements \TYPO3\CMS\Core\SingletonInterface{

	/**
	 * @var array
	 */
	protected $conf = array();

	/**
	 * FrontendHook constructor
	 */
	public function __construct(){
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['tx_pb_fileinfo.'];
	}

	/**
	 * Modify content, called after caching (USER_INT)
	 * @param array $parameters
	 * @return void
	 */
	public function modifyUncachedContent(&$parameters){

		if($this->conf['enable'] != '1'){
			return;
		}

		$tsfe = &$parameters['pObj'];
		if ($tsfe instanceof \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController) {
			if ($tsfe->isINTincScript() === true) {
				$this->modifyContent($tsfe->content);
			}
		}
	}

	/**
	 * Modify content, called before caching
	 * @param array $parameters
	 * @return void
	 */
	public function modifyCachedContent(&$parameters){

		if($this->conf['enable'] != '1'){
			return;
		}

		$tsfe = &$parameters['pObj'];
		if ($tsfe instanceof \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController) {
			if ($tsfe->isINTincScript() === false) {
				$this->modifyContent($tsfe->content);
			}
		}
	}

	/**
	 * Modifies the content
	 * @param $content
	 * @return void
	 */
	private function modifyContent(&$content){

		// $regExpression = '#<a(.*)>(.*)</a>#siU';
		$regExpression = '#<a\s+(.*)>(.*)</a>#siU';
		$content = preg_replace_callback($regExpression, 'self::addFileinfo', $content);

	}


	/*
	 * Adds the fileinfo
	 * @param array $match
	 * @return string the new link
	 */
	private function addFileinfo($match){

		// $match[0] => the whole match
		// $match[1] => the first match
		// $match[2] => ...

		// echo $match[0]."\n";

		// Get only the link, because HTML-Entities inside of the a-tag can cause errors
		// $linkOnly = preg_replace('#<a(.*)>(.*)</a>#siU', '<a$1></a>', $match[0]);
		$linkOnly = preg_replace('#<a\s+(.*)>(.*)</a>#siU', '<a $1></a>', $match[0]);

		// replace & with &amp; in url
		$regEx = '#&(?!amp;)#';
		$linkOnly = stripslashes(preg_replace($regEx, '&amp;', $linkOnly));
		
		$xml = simplexml_load_string($linkOnly);

		if(!is_object($xml)){
			return $match[0];
		}

		// Get the link-attribiutes as an array
		$attr_object	= $xml->attributes();
		$attr_array		= (array)$attr_object;
		$attr_array		= $attr_array['@attributes'];

		// print_r($attr_array);

		// Only internal Links, with fileextension defined in typoscript
		$fileExt = strtolower(strrchr($attr_array['href'], '.'));
		$fileExtDefined = false;
		$fileInfo = '';
		foreach($this->conf['fileInfos.'] as $key => $value){
			if($fileExt == '.' . $key){
				$fileExtDefined = true;
				$fileInfo = str_replace('|', $value, $this->conf['wrap']);
				break;
			}
			// echo $key . '|' . $value . "<br />\n";
		}
		if(
			preg_match('#^http#', $attr_array['href'])
			||
			!$fileExtDefined
		){
			return $match[0];
		}

		// echo $match[0]."\n";

		// Get the filesize
		$file = PATH_site . $attr_array['href'];

		// Consider special characters
		if(is_file(urldecode($file))) {
			$file = urldecode($file);
		}
		if(is_file($file)){
			$fileInfo = ' ' . str_replace('%s', $this->byteSize(filesize($file)), $fileInfo);
		}else{
			$fileInfo = '';
		}


		if($this->conf['mode'] == 'inner'){
			// Remember: $reg = '#<a(.*)>(.*)</a>#siU';
			$return = '<a ' . $match[1] . '>' . $match[2] . $fileInfo . '</a>';
		}else{
			$return = $match[0] . $fileInfo;
		}

		return $return;

	}

	/*
	 * Returns a formatted filesize
	 * @param string unformatted filesize
	 * @return string formatted filesize
	 */
	private function byteSize($bytes){

		if (!is_int($bytes) || $bytes < 0){
			return false;
		}

		$map = array(
			// 'GB' => array(1073741824, 1),
			'GB' => array(1024000000, 1),
			// 'MB' => array(1048576, 2),
			'MB' => array(1024000, 2),
			'KB' => array(1024, 2),
			'Bytes' => array(1, 0),
		);

		foreach($map as $k => $v){
			if ($bytes >= $v[0]){
				break;
			}
		}

		# $f = number_format($bytes / $v[0], $v[1],',','.');
		$f = number_format($bytes / $v[0], 1,',','.');

		if ($bytes < 2){
			$k = 'Byte';
		}

		return sprintf ('%s&nbsp;%s',$f, $k);

	}

}