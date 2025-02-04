<?php

namespace Biltorvet\Utility;

use Parsedown;
use stdClass;

class PrivatePluginUpdater
{
    private $slug;
    private $pluginData;
    private $username;
    private $repo;
    private $pluginFile;
    private $githubAPIResult;
    private $accessToken;
    private $pluginActivated;

    function __construct( $pluginFile, $gitHubUsername, $gitHubProjectName, $accessToken = '' )
    {
        add_filter( "pre_set_site_transient_update_plugins", array( $this, "setTransient" ) );
        add_filter( "plugins_api", array( $this, "setPluginInfo" ), 10, 3 );
        add_filter( "upgrader_pre_install", array( $this, "preInstall" ), 10, 3 );
        add_filter( "upgrader_post_install", array( $this, "postInstall" ), 10, 3 );

        $this->pluginFile       = $pluginFile;
        $this->username         = $gitHubUsername;
        $this->repo             = $gitHubProjectName;
        $this->accessToken      = $accessToken;
    }

    private function logDebug($message, $data = null)
    {
        $log = $message;
        if ($data !== null) {
            $log .= " " . print_r($data, true);
        }
        error_log("[Plugin Updater] " . $log);
    }

    private function initPluginData()
    {
        $this->slug = plugin_basename( $this->pluginFile );
        $this->pluginData = get_plugin_data( $this->pluginFile );
        $this->logDebug("Initialized Plugin Data", $this->pluginData);
    }

    private function getRepoReleaseInfo()
    {
        if (!empty($this->githubAPIResult)) {
            return;
        }

        $url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";
        
        $args = [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress Plugin Updater'
            ]
        ];

        if (!empty($this->accessToken)) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->accessToken;
        }

        $response = wp_remote_get($url, $args);
        $body = wp_remote_retrieve_body($response);
        
        if (is_wp_error($response)) {
            $this->logDebug("GitHub API Error", $response->get_error_message());
            return;
        }

        $this->githubAPIResult = json_decode($body);
        
        if (is_array($this->githubAPIResult) && !empty($this->githubAPIResult)) {
            $this->githubAPIResult = $this->githubAPIResult[0];
        }
        
        $this->logDebug("GitHub API Response", $this->githubAPIResult);
    }

    public function setTransient($transient)
    {
        if (!property_exists($transient, 'checked') || empty($transient->checked)) {
            return $transient;
        }

        $this->initPluginData();
        $this->getRepoReleaseInfo();
        
        if (!isset($this->githubAPIResult->tag_name)) {
            $this->logDebug("GitHub tag_name not found");
            return $transient;
        }

        $doUpdate = version_compare($this->githubAPIResult->tag_name, $transient->checked[$this->slug] ?? '', '>');
        
        if ($doUpdate) {
            $package = $this->githubAPIResult->zipball_url;
            $transient->response[$this->slug] = (object) [
                'slug' => $this->slug,
                'new_version' => $this->githubAPIResult->tag_name,
                'url' => $this->pluginData["PluginURI"] ?? '',
                'package' => $package
            ];
        }

        $this->logDebug("Update check", [
            'doUpdate' => $doUpdate,
            'current_version' => $transient->checked[$this->slug] ?? 'unknown',
            'new_version' => $this->githubAPIResult->tag_name
        ]);

        return $transient;
    }

	public function setPluginInfo($false, $action, $response)
	{
		$this->initPluginData();
		$this->getRepoReleaseInfo();

		if (empty($response->slug) || $response->slug != $this->slug) {
			return $false;
		}

		// Add our plugin information
		$response->last_updated = $this->githubAPIResult->published_at ?? '';
		$response->slug = $this->slug;
		$response->plugin_name = $this->pluginData["Name"] ?? '';
		$response->version = $this->githubAPIResult->tag_name ?? '';
		$response->author = $this->pluginData["AuthorName"] ?? '';
		$response->homepage = $this->pluginData["PluginURI"] ?? '';
		$response->name = $this->pluginData["Name"] ?? '';

		// Set the download link
		$downloadLink = $this->githubAPIResult->zipball_url ?? '';

		if (!empty($this->accessToken)) {
			$downloadLink = add_query_arg(["access_token" => $this->accessToken], $downloadLink);
		}

		$response->download_link = $downloadLink;

		// Ensure sections is an array (not an object)
		$response->sections = [];

		$response->sections['Description'] = $this->pluginData["Description"] ?? 'No description available.';
		
		if (!empty($this->githubAPIResult->body)) {
			$response->sections['changelog'] = class_exists("Parsedown")
				? Parsedown::instance()->parse($this->githubAPIResult->body)
				: $this->githubAPIResult->body;
		} else {
			$response->sections['changelog'] = "No changelog available.";
		}

		// Log debugging info
		$this->logDebug("Plugin Info Response", $response);

		return $response;
	}

    /**
     * Perform check before installation starts.
     *
     * @param  boolean $true
     * @param  array   $args
     * @return null
     */
    public function preInstall( $true, $args )
    {
        // Get plugin information
        $this->initPluginData();

        // Check if the plugin was installed before...
        $this->pluginActivated = is_plugin_active( $this->slug );
    }

    /**
     * Perform additional actions to successfully install our plugin
     *
     * @param  boolean $true
     * @param  string $hook_extra
     * @param  object $result
     * @return object
     */
    public function postInstall( $true, $hook_extra, $result )
    {
        global $wp_filesystem;

        // Since we are hosted in GitHub, our plugin folder would have a dirname of
        // reponame-tagname change it to our original one:
        $pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->slug );
        $wp_filesystem->move( $result['destination'], $pluginFolder );
        $result['destination'] = $pluginFolder;

        // Re-activate plugin if needed
        if ( $this->pluginActivated )
        {
            $activate = activate_plugin( $this->slug );
        }

        return $result;
    }


}