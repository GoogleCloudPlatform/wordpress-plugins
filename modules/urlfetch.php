<?php
/**
 * Google App Engine URL Fetch functionality
 *
 * Replaces the in built WordPress HTTP clients (curl, sockets) with a URLFetch
 * based implementation.
 *
 * Copyright 2013 Google Inc.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package App Engine
 * @subpackage urlfetch
 */

UrlFetch::boostrap();

class UrlFetch {
  public static function boostrap() {
    add_filter('http_api_transports', __CLASS__ . '::filter_api_transports', 10, 3);
  }

  public static function filter_api_transports($transports, $args, $url) {
    // Drop the other transports and only use urlfetch.
    return ['urlfetch'];
  }
}

/**
 * Class WP_HTTP_urlfetch
 *
 * Uses the App Engine URLFetch API to retrieve external content.
 */

// TODO(slangley): Remove when autoloading is shipped.
require_once 'google/appengine/api/urlfetch_service_pb.php';
require_once 'google/appengine/ext/cloud_storage_streams/HttpResponse.php';
require_once 'google/appengine/runtime/ApiProxy.php';
require_once 'google/appengine/runtime/ApplicationError.php';

use google\appengine\runtime\ApiProxy;
use google\appengine\runtime\ApplicationError;
use google\appengine\URLFetchRequest\RequestMethod;

class WP_HTTP_urlfetch {

  private static $request_map = [
      "GET" => RequestMethod::GET,
      "POST" => RequestMethod::POST,
      "HEAD" => RequestMethod::HEAD,
      "PUT" => RequestMethod::PUT,
      "DELETE" => RequestMethod::DELETE,
      "PATCH" => RequestMethod::PATCH
  ];

  private static $request_defaults = [
      'method' => 'GET',
      'timeout' => 5,
      'redirection' => 5,
      'httpversion' => '1.0',
      'blocking' => true,
      'headers' => [],
      'body' => null,
      'cookies' => array()
  ];

  /**
   * Make a HTTP request to a supplied URL.
   *
   * Much of the logic here is plagiarized from the WP_Http_Curl as to avoid
   * trying to re-invent the wheel.
   *
   * @param $url
   * @param $args
   * @return bool
   */
  public function request($url, $args) {
    $r = wp_parse_args($args, self::$request_defaults);

    // Construct Cookie: header if any cookies are set.
    WP_Http::buildCookieHeader( $r );

    $is_local = isset($r['local']) && $r['local'];
    $ssl_verify = isset($r['sslverify']) && $r['sslverify'];
    if ($is_local) {
      $ssl_verify = apply_filters('https_local_ssl_verify', $ssl_verify);
    } else {
      $ssl_verify = apply_filters('https_ssl_verify', $ssl_verify);
    }

    // For now, lets not support streaming into a file and see what breaks
    if (isset($r['filename'])) {
      return new WP_Error( 'http_request_failed',
          __( 'Saving to a file is not currently supported.'));
    }

    $req = new \google\appengine\URLFetchRequest();
    $req->setUrl($url);
    $req->setMethod(self::$request_map[$r['method']]);
    $req->setMustValidateServerCertificate($ssl_verify);
    if (isset($r['body'])) {
      $req->setPayload($r['body']);
    }
    if (isset($r['timeout'])) {
      $req->setDeadline($r['timeout']);
    }
    // App Engine does not allow setting the number of redirects.
    $req->setFollowRedirects(isset($r['redirection']));

    foreach($r['headers'] as $key => $value) {
      $header = $req->addHeader();
      $header->setKey($key);
      $header->setValue($value);
    }

    $resp = new \google\appengine\URLFetchResponse();

    try {
      ApiProxy::makeSyncCall('urlfetch', 'Fetch', $req, $resp);
    } catch (ApplicationError $e) {
      syslog(LOG_ERR,
          sprintf("Call to URLFetch failed with application error %d.",
                  $e->getApplicationError()));
      return new \WP_Error('http_request_failed', $e->getMessage());
    }

    $response = [];
    $response['code'] = $resp->getStatusCode();
    $response['message'] = \get_status_header_desc($resp->getStatusCode());

    $headers = [];
    $cookies = [];

    foreach($resp->getHeaderList() as $header) {
      $key = strtolower($header->getKey());
      $value = trim($header->getValue());
      // If a header has multiple values then it is stored in an array
      if (isset($headers[$key])) {
        if (!is_array($headers[$key])) {
          $headers[$key] = [$headers[$key]];
        }
        $headers[$key][] = $value;
      } else {
        $headers[$key] = $value;
      }
      if ('set-cookie' == $key) {
        $cookies[] = new \WP_Http_Cookie($value, $url);
      }
    }

    $theBody = $resp->getContent();
    if (true === $r['decompress'] &&
        true === WP_Http_Encoding::should_decode($headers)) {
      $theBody = WP_Http_Encoding::decompress( $theBody );
    }

    if (isset( $r['limit_response_size']) &&
        strlen($theBody) > $r['limit_response_size']) {
      $theBody = substr($theBody, 0, $r['limit_response_size']);
    }

    $response = [
      'response' => $response,
      'body' => $theBody,
      'cookies' => $cookies,
      'headers' => $headers,
    ];

    return $response;
  }
  /**
   * Whether this class can be used for retrieving an URL.
   *
   * @param $args
   *
   * @return boolean False means this class can not be used, true means it can.
   */
  public static function test($args = [], $url = null) {
    // On App Engine we'll always use the url fetch to access remote sites.
    return apply_filters('use_urlfetch_transport', true, $args);
  }
}
