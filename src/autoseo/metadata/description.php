<?php

namespace AutoSeo\Metadata;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

class Description extends Plugin
{
	public static function getSubscribedEvents() {

		return [
			'onPageProcessed' => ['onPageProcessed', 0],
		];
	}

	public function onPageProcessed(Event $e)
	{
		$page = $e['page'];

		$available = [
			'page' => (array) $page->header(),
			'site' => $this->config->get('site')
		];

		$metadata = $page->metadata();
		$siteMetadataContent=$metadata['description'];


		$length = $this->config->get('plugins.autoseo.description.length');
		
		if ($length <=1 ) $length=20; 

		if (isset($available['page']['metadata']['description'])) {
			$pageMetadataContent=$available['page']['metadata']['description'];
		} else {
			// we create a description using the page content
			$autoContent = $page->rawMarkdown();

			$autoContent = substr($autoContent,0, 1000 );
			$autoContent = str_replace("\n",' ',$autoContent);
			$autoContent = str_replace("&nbsp;",' ',$autoContent);
			$autoContent = str_replace('"',"'",$autoContent);
			$autoContent = trim($autoContent);
			$autoContent = preg_replace('/[^A-Za-z0-9\-]/', '', $autoContent); // Removes special chars.
	    $autoContent = preg_replace('/((\w+\W*){'.$length.'}(\w+))(.*)/', '${1}', $autoContent);    

			$pageMetadataContent = $autoContent;
		}

		if (!empty($pageMetadataContent))
			$metadata['description'] = [ 'property' => 'description', 'content' => $pageMetadataContent];

		$page->metadata($metadata);
	}


}