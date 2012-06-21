<?php
namespace Maa\Html;
/**
 * Created by JetBrains PhpStorm.
 * User: lsv
 * Date: 6/21/12
 * Time: 4:19 PM
 * To change this template use File | Settings | File Templates.
 */
class Cache
{

	private $cachedir;
	private $cachettl;
	private $url = '';

	static protected $_instance = null;

	protected function __construct()
	{
		$this->cachedir = dirname(__FILE__) . '/Cache/';
		$this->cachettl = 2592000;
	}

	protected function __clone() {}

	static public function getInstance()
	{
		if (self::$_instance === null)
			return self::$_instance = new self;
		return self::$_instance;
	}

	public function loadData($url)
	{
		$this->url = $url;
		$data = $this->getCache();
		var_dump('Data: ' . $data);
		if (empty($data)) {
			return $this->setCache();
		}
		return $data;
	}

	private function setCache()
	{
		$data = file_get_contents($this->url);
		file_put_contents($this->cachedir . md5($this->url) . '.m-' . time() . '.cache', $data);
		return $data;
	}

	private function getCache()
	{
		$files = glob($this->cachedir . '*');
		foreach ($files AS $file) {
			$filename = str_replace($this->cachedir, '', $file);
			list($url, $tmp) = explode('.', $filename);
			if (md5($this->url) == $url) {
				$stamp = (int)str_replace(md5($this->url), '', str_replace('.m-', '', str_replace('.cache', '', $filename)));
				if ($stamp < (time() - $this->cachettl)) {
					unlink($file);
				} else {
					return file_get_contents($file);
				}
			}
		}
		return false;
	}

}
