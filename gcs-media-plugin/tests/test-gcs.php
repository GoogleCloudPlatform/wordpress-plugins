<?php
/**
 * Copyright 2016 Google Inc.
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.  This program is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.  You should have received a copy of the
 * GNU General Public License along with this program; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 *
 * @package Google\Cloud\Storage\WordPress\Test;
 */

namespace Google\Cloud\Storage\WordPress\Test;

use Google\Cloud\Storage\WordPress\Uploads\Uploads;

/**
 * Unit tests for the plugin.
 */
class GcsPluginUnitTestCase extends \WP_UnitTestCase
{

    /**
     * A test for options_page_view.
     */
    public function test_options_page_view()
    {
        // Nothing for normal user
        ob_start();
        \Google\Cloud\Storage\WordPress\options_page_view();
        $html = ob_get_clean();
        $this->assertEmpty($html);

        // Showing options form to admins.
        $user_id = $this->factory->user->create(
            array('role' => 'administrator')
        );
        \wp_set_current_user($user_id);
        ob_start();
        \Google\Cloud\Storage\WordPress\options_page_view();
        $html = ob_get_clean();
        $this->assertRegexp('/form action="options.php"/', $html);
    }

    /**
     * A test for options_page.
     */
    public function test_options_page()
    {
        // TODO: actually check the side effect of this call.
        \Google\Cloud\Storage\WordPress\options_page();
    }

    /**
     * A test for activation_hook.
     */
    public function test_activation_hook()
    {
        // TODO: actually check the side effect of this call.
        \Google\Cloud\Storage\WordPress\activation_hook();
    }

    /**
     * A test for settings_link.
     */
    public function test_settings_link()
    {
        $links = [];
        $links = \Google\Cloud\Storage\WordPress\settings_link(
            $links,
            \plugin_basename(\Google\Cloud\Storage\WordPress\PLUGIN_PATH)
        );
        $this->assertRegexp('/options-general.php\\?page=gcs/', $links[0]);
    }

    /**
     * A test for register_settings().
     */
    public function test_register_settings()
    {
        // There is no settings initially.
        $ssl = get_option(Uploads::USE_HTTPS_OPTION);
        $this->assertFalse($ssl);
        // We have the option set to true (1).
        \Google\Cloud\Storage\WordPress\register_settings();
        $ssl = get_option(Uploads::USE_HTTPS_OPTION);
        $this->assertEquals(1, $ssl);
    }

    /**
     * A test for filter_delete_file.
     */
    public function test_filter_delete_file()
    {
        $result = Uploads::filter_delete_file(
            'gs://tmatsuo-test-wordpress/testfile'
        );
        $this->assertEquals(
            'gs://tmatsuo-test-wordpress/testfile',
            $result
        );
    }

    /**
     * A test for filter_upload_dir.
     */
    public function test_filter_upload_dir()
    {
        \Google\Cloud\Storage\WordPress\register_settings();
        // It does nothing without setting the option.
        $values = array();
        $values = Uploads::filter_upload_dir($values);
        $this->assertEmpty($values);

        $testBucket = getenv('TEST_BUCKET');
        if ($testBucket === false) {
            $this->markTestSkipped('TEST_BUCKET envvar is not set');
        }
        $values = array(
            'path' => '/tmp/uploads',
            'subdir' => '/2016/11',
            'url' => 'https://example.com/wp-content/2016/11/uploaded.jpg',
            'basedir' => '/tmp/uploads',
            'baseurl' => 'https://example.com/wp-content/2016/11/',
            'error' => false
        );
        \update_option(Uploads::BUCKET_OPTION, $testBucket);
        $values = Uploads::filter_upload_dir($values);
        $this->assertEquals(
            sprintf('gs://%s/1/2016/11', $testBucket),
            $values['path']
        );
        $this->assertEquals('/2016/11', $values['subdir']);
        $this->assertFalse($values['error']);
        $this->assertEquals(
            sprintf('https://storage.googleapis.com/%s/1/2016/11', $testBucket),
            $values['url']
        );
        $this->assertEquals(
            sprintf('gs://%s/1', $testBucket),
            $values['basedir']
        );
        $this->assertEquals(
            sprintf('https://storage.googleapis.com/%s/1', $testBucket),
            $values['baseurl']
        );
    }

    /**
     * A test for bucket_form.
     */
    public function test_bucket_form()
    {
        ob_start();
        Uploads::bucket_form();
        $html = ob_get_clean();
        $this->assertRegexp(
            '/<input id="gcs_bucket" name="gcs_bucket" type="text" value="">/',
            $html
        );
    }

    /**
     * A test for use_https_form.
     */
    public function test_use_https_form()
    {
        ob_start();
        Uploads::use_https_form();
        $html = ob_get_clean();
        $this->assertRegexp(
            '/input id="gcs_use_https_for_media", name="gcs_use_https_for_media" type="checkbox"/',
            $html
        );
    }

    /**
     * A test for validate_bucket.
     */
    public function test_validate_bucket()
    {
        $testBucket = getenv('TEST_BUCKET');
        if ($testBucket === false) {
            $this->markTestSkipped('TEST_BUCKET envvar is not set');
        }
        Uploads::validate_bucket($testBucket);
    }

    /**
     * A test for validate_use_https.
     */
    public function test_validate_use_https()
    {
        $result = Uploads::validate_use_https(0);
        $this->assertFalse($result);
        $result = Uploads::validate_use_https(1);
        $this->assertTrue($result);
    }
}
