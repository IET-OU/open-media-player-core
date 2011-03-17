<?php
/** Prezi service provider.
 *
 * @copyright Copyright 2011 The Open University.
 */
//1 March 2011.

require_once 'base_service.php';

class Prezi_serv extends Base_service {

  public function call($url, $matches) {
  //protected function _meta_prezi($url, $matches=null) {

    $meta = array(
      'url'=>$url,
      'provider_name'=>'prezi',
      'provider_mid' =>$matches[1],
      'title' => ucfirst(str_replace('-', ' ', $matches[2])),
      'timestamp'=>null,
    );
#var_dump($meta);

      /* */
  #echo "GET $url";
    $result = $this->_http_request_curl($url, $spoof=TRUE);
    if (! $result->success) {
  var_dump($result);
      die("Error, Prezi_serv woops");
      return FALSE; //Error.
    }

    preg_match('#(<head.*</head>)#ms', $result->data, $matches);
    $head = $this->_safe_xml($matches[1]);
    $xh = new SimpleXmlElement($head);

    foreach ($xh->children() as $name => $value) {
      $attr = $value->attributes();
      if ('meta'==$name && isset($attr['name'])) {
        if ('description'==$attr['name']) {
          $desc = (string) $attr['content'];
          if (preg_match('#(.*)presented by(.*)#', $desc, $m_desc)) {
            //$meta['author']= trim($m_desc[2]);
            $meta['timestamp'] = strtotime($m_desc[1]);
          } else {
            $meta['description'] = $desc;
          }
        } elseif ('title'==$attr['name']) {
          $meta['title'] = (string) $attr['content'];
        }
      }
      elseif ('link'==$name && 'image_src'==$attr['rel']) {
        $meta['thumbnail_url'] = (string) $attr['href'];
      }
      elseif ('title'==$name
        && preg_match('#by(.*)on Prezi#', (string) $value, $m_title)) {
        $meta['author'] = trim($m_title[1]);
      }
    }
    //$desc = $xh->xpath("//meta[@name='description']/@content");
#var_dump($meta);

    //$cache_id = $this->embed_cache_model->insert_embed($meta);
    
    return (object) $meta;
  }
}