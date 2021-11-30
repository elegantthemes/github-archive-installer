<?php

namespace ET\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\{PluginInterface, PluginEvents, PreFileDownloadEvent};


/**
 * Class GithubArchiveInstaller
 */
class GithubArchiveInstaller implements PluginInterface, EventSubscriberInterface {

	const PACKAGE_TYPE = 'github-archive-installer';

	/**
	 * Composer instance.
	 *
	 * @var \Composer\Composer
	 */
	protected $composer;

	/**
	 * Input/Output interface.
	 *
	 * @var \Composer\IO\IOInterface
	 */
	protected $io;

	/**
	 * Apply plugin modifications to Composer.
	 *
	 * @param \Composer\Composer       $composer Composer instance
	 * @param \Composer\IO\IOInterface $io       Input/Output interface
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		$this->composer = $composer;
		$this->io       = $io;
	}

	/**
	 * Returns an array of event names this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public static function getSubscribedEvents(): array {
		return array(
			PluginEvents::PRE_FILE_DOWNLOAD => 'onPreFileDownload',
		);
	}

	/**
	 * Set distribution URL before installing.
	 */
	public function onPreFileDownload( PreFileDownloadEvent $event ) {

		/** @var \Composer\Package\Package $package */
		$package = $event->getContext();
		$version = $package->getFullPrettyVersion();

		if ( ! array_key_exists( 'elegantthemes/github-archive-installer', $package->getRequires() ) ) {
			return;
		}

		if ( ! version_compare( $version, '0.0.0', '>=' ) ) {
			return;
		}

		$repo      = $package->getName();
		$repo_name = $package->getExtra()['installer-name'] ?? explode( '/', $repo )[1];
		$dist_url  = "https://github.com/{$repo}/releases/download/{$version}/{$repo_name}.zip";

		$package->setDistUrl( $dist_url );
		$event->setProcessedUrl( $dist_url );
	}

	/**
	 * Deactivate
	 *
	 * @param \Composer\Composer       $composer Composer instance
	 * @param \Composer\IO\IOInterface $io       Input/Output interface
	 */
	public function deactivate( Composer $composer, IOInterface $io ) {
		// Nothing to do here.
	}

	/**
	 * Uninstall
	 *
	 * @param \Composer\Composer       $composer Composer instance
	 * @param \Composer\IO\IOInterface $io       Input/Output interface
	 */
	public function uninstall( Composer $composer, IOInterface $io ) {
		// Nothing to do here.
	}

}
