<?php
namespace PeterBenke\PbFileinfo\Service;

use TYPO3\CMS\Core\SingletonInterface;
use \TYPO3\CMS\Core\Core\Environment;

class ModifyContentService implements SingletonInterface
{

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * Sets the configuration
	 * @param $configuration
	 */
	private function setConfiguration($configuration)
	{
		$this->configuration = $configuration;
	}


	/**
	 * Clean the HTML with formatter
	 * @param string $content
	 * @param array $config Typoscript of this extension
	 * @return string
	 */
	public function clean($content, $config = [])
	{

		if (empty($config) || !isset($config['enable']) || (bool)$config['enable'] === false) {
			return $content;
		}
		$this->setConfiguration($config);

		$content = $this->modifyContent($content);
		return $content;

	}

	/**
	 * Modifies the content
	 * @param $content
	 * @return string|string[]|null
	 */
	private function modifyContent($content)
	{

		// $regExpression = '#<a(.*)>(.*)</a>#siU';
		$regExpression = '#<a\s+(.*)>(.*)</a>#siU';
		$content = preg_replace_callback($regExpression, 'self::addFileInfo', $content);
		return $content;

	}


	/*
	 * Adds the file info
	 * @param array $match
	 * @return string the new link
	 */
	private function addFileInfo($match){

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

		if(
			!isset($this->configuration['fileInfos.'])
			||
			!isset($this->configuration['wrap'])
		){
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
		foreach($this->configuration['fileInfos.'] as $key => $value){
			if($fileExt == '.' . $key){
				$fileExtDefined = true;
				$fileInfo = str_replace('|', $value, $this->configuration['wrap']);
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

		// Get the file size
		$file = Environment::getPublicPath() . $attr_array['href'];

		// Consider special characters
		if(is_file(urldecode($file))) {
			$file = urldecode($file);
		}
		if(is_file($file)){
			$fileInfo = ' ' . str_replace('%s', $this->byteSize(filesize($file)), $fileInfo);
		}else{
			$fileInfo = '';
		}


		if(isset($this->configuration['mode']) && $this->configuration['mode'] == 'inner'){
			// Remember: $reg = '#<a(.*)>(.*)</a>#siU';
			$return = '<a ' . $match[1] . '>' . $match[2] . $fileInfo . '</a>';
		}else{
			$return = $match[0] . $fileInfo;
		}

		return $return;

	}

	/*
 * Returns a formatted file size
 * @param string not formatted file size
 * @return string formatted file size
 */
	private function byteSize($bytes){

		if (!is_int($bytes) || $bytes < 0){
			return false;
		}

		$map = [
			'GB' => [1024000000, 1],
			'MB' => [1024000, 2],
			'KB' => [1024, 2],
			'Bytes' => [1, 0],
		];

		$v = 1;
		$k = '';
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
