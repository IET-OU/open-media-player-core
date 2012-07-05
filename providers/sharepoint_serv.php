<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharepoint oEmbed service provider.
 *
 * @copyright Copyright 2012 The Open University.
 * @author N.D.Freear, 4 July 2012.
 */
require_once APPPATH.'libraries/Oembed_Provider.php';


class Sharepoint_serv extends Oembed_Provider {

  public $regex = 'https://intranet7.open.ac.uk/collaboration/iet-professional-development/Shared Documents/*'; #array()
  public $about = <<<EOT
    "Microsoft® SharePoint® 2010 makes it easier for people to work together. Using SharePoint, your people can set up Web sites...[and] manage documents from start to finish..."
    Use this service to embed links to shared documents stored in SharePoint, with associated meta-data. [Initially for the OU-internal E-PD site. Restricted access.]';
EOT;
  public $displayname = 'Microsoft SharePoint®';
  #public $name = 'sharepoint';
  public $domain = 'intranet7.open.ac.uk';
  public $subdomains = array();
  public $favicon = 'http://sharepoint.microsoft.com/_layouts/images/favicon.ico';
  #https://intranet7.open.ac.uk/collaboration/iet-professional-development/_layouts/images/siteIcon.png
  #https://intranet7.open.ac.uk/_layouts/images/siteIcon.png
  public $type = 'link';  # Initially 'link', later 'rich'

  public $_about_url = 'http://sharepoint.microsoft.com/';
  public $_regex_real = 'https://intranet7.open.ac.uk/collaboration/iet-professional-development/Shared Documents/Forms/DispForm.aspx\?ID=(\d+)';
  public $_examples = array(
    'Follow-up Induction Apr 2008 (Word doc)'=>'https://intranet7.open.ac.uk/collaboration/iet-professional-development/Shared Documents/Forms/DispForm.aspx?ID=1',
	'_RSS'=>'https://intranet7.open.ac.uk/collaboration/iet-professional-development/_layouts/listfeed.aspx?List=%7B55377B07-A07E-4BDD-9F8E-03CFD5524546%7D',
	'_OEM'=>'/oembed?url=https%3A//intranet7.open.ac.uk/collaboration/iet-professional-development/Shared Documents/Forms/DispForm.aspx?ID=1',
  );

  public $_access = 'private';


  protected $_list_id = '55377B07-A07E-4BDD-9F8E-03CFD5524546';
  protected $_root = 'https://intranet7.open.ac.uk/collaboration/iet-professional-development';


  public function call($url, $matches) {
      $document_id  = $matches[1]; #DispForm.aspx?ID=1

	  $rss_url = "$this->_root/_layouts/listfeed.aspx?List=%7B{$this->_list_id}%7D";

	  //TODO: access - check for either allowed IP address, or SAMS cookie..
	  $http_options = array(
	    'auth' => $this->CI->config->item('http_sharepoint_userpwd'), #'[domain\]user:password'
	    'debug' => TRUE,
	  );

	  $result = $this->_http_request_curl($rss_url, $spoof=FALSE, $http_options);
	  if (! $result->success) {
	    $this->_error("Sharepoint oEmbed provider HTTP problem, $rss_url", $result->http_code);
	  }

	  $xmlo = @simplexml_load_string($result->data);
	  if (! $xmlo) {
	    $this->_error("Sharepoint oEmbed provider XML problem, $rss_url");
	  }

	  // Trigger an error - $document_id++;


	  // We'd prefer the XPath 2.0 ends-with function.
	  // http://stackoverflow.com/questions/5435310/php-xpath-ends-with
	  // $xmlo->registerXPathNamespace('fn', 'http://www.w3.org/2005/xpath-functions');
	  $xpath_query = "//item[link[contains(., '?ID=$document_id')]]";
	  $item = $xmlo->xpath($xpath_query);
	  if (! $item) {
	    $this->_error("Sharepoint document not found, ". $matches[0], 404);
	  }
	  $item = $item[0];

	  $meta = array(
		'provider_name'=> (string) $xmlo->channel->title,
		'provider_url' => $this->_root,
		'provider_mid' => $document_id,
		'generator_name' => $this->displayname,
		'title'  => (string) $item->title,
		'author' => (string) $item->author,
		'timestamp' => strtotime((string) $item->pubDate),
		'document_url' => (string) $item->enclosure['url'],
		'document_type'=> NULL,
		#'document_size'
	  );

	  #$ext = pathinfo($meta['document_url'], PATHINFO_EXTENSION);

	  $this->CI->load->helper('file');
	  $meta['document_type'] = get_mime_by_extension($meta['document_url']);

	  #&source=https%3A%2F%2Fintranet7%2Eopen%2Eac%2Euk%2Fcollaboration%2Fiet-professional-development%2FShared%2520Documents%2FForms%2FAllItems%2Easpx
	  $meta['_viewer_url'] = $this->_root .'/_layouts/WordViewer.aspx?id='. urlencode(parse_url($meta['document_url'], PHP_URL_PATH));

	  return (object) $meta;
  }
}


/*object(stdClass)#23 (3) {
  ["data"]=>
  string(3075) "HTTP/1.0 200 Connection established

HTTP/1.1 401 Unauthorized
Server: Microsoft-IIS/7.5
SPRequestGuid: e3d19cb5-4057-468e-b490-9fc880569cb2
WWW-Authenticate: NTLM TlRMTVNTUAACAAAAHgAeADgAAAA1goniv2qtNNTxS8IAAAAAAAAAALQAtABWAAAABgGxHQAAAA9PAFAARQBOAC0AVQBOAEkAVgBFAFIAUwBJAFQAWQACAB4ATwBQAEUATgAtAFUATgBJAFYARQBSAFMASQBUAFkAAQAaAFMAUAAtAFcARQBCAC0ATABJAFYARQAtAEEABAAUAG8AcABlAG4ALgBhAGMALgB1AGsAAwAwAFMAUAAtAFcARQBCAC0ATABJAFYARQAtAEEALgBvAHAAZQBuAC4AYQBjAC4AdQBrAAUAFABvAHAAZQBuAC4AYQBjAC4AdQBrAAcACAAhQmiYlVrNAQAAAAA=
X-Powered-By: ASP.NET
MicrosoftSharePointTeamServices: 14.0.0.6029
Date: Thu, 05 Jul 2012 10:04:41 GMT
Content-Length: 0

...
"
    ["request_header"]=>
    string(621) "GET /collaboration/iet-professional-development/_layouts/listfeed.aspx?List=%7B55377B07-A07E-4BDD-9F8E-03CFD5524546%7D HTTP/1.1
Authorization: NTLM TlRMTVNTUAADAAAAGAAYAHAAAAAYABgAiAAAAAAAAABYAAAACgAKAFgAAAAOAA4AYgAAABAAEACgAAAANYKI4gYBsR0AAAAPyjv9LxTgCoCF0YFAqn8CM24AZABmADQAMgBQAEMASQBFADYANgAzAIaJ5MvLdwEBAAAAAAAAAAAAAAAAAAAAAHZfEm7QJVUN09s8DwfRYcmygRrGDdf+u4Y219OrJtffCVBQU91lGtU=
User-Agent: OU Player/0.9 (PHP/cURL)
Host: intranet7.open.ac.uk
Accept: *--/*
Referer: https://intranet7.open.ac.uk/collaboration/iet-professional-development/_layouts/listfeed.aspx?List=%7B55377B07-A07E-4BDD-9F8E-03CFD5524546%7D

"
  }
  ["success"]=>
  bool(true)
}*/
