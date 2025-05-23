<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

/**
 * Class for working with ZIP archives to export
 * sliders with images and other attachments.
 *
 * @package LS_ExportUtil
 * @since 5.0.3
 * @author John Gera
 * @copyright Copyright (c) 2025  John Gera, George Krupa, and Kreatura Media Kft.
 */

class LS_ExportUtil {

	/**
	 * The managed ZipArchieve instance.
	 */
	private $zip;

	/**
	 * A temporary file in /wp-content/uploads/ to manipulate
	 * ZIPs on the fly without permanently saving to file system.
	 */
	private $file;


	/**
	 * Holds used image URLs in slider to be exported
	 */
	private $imageList;


	/**
	 * Prepares a ZipArchieve instance and the file system
	 * to work with the class.
	 *
	 * @since 5.0.3
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Check for ZipArchieve
		if(class_exists('ZipArchive')) {

			// Temporary directory for file operations
			$upload_dir = wp_upload_dir();
			$tmp_dir = $upload_dir['basedir'];

			// Prepare ZIP to work with
			$this->file = tempnam($tmp_dir, "zip");
			$this->zip = new ZipArchive;
			$this->zip->open($this->file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		}
	}


	/**
	 * Adds slider settings .json file to ZIP
	 *
	 * @since 5.0.3
	 * @access public
	 * @param string $data Slider settings JSON
	 * @return void
	 */
	public function addSettings($data, $folder = '') {
		$folder = !empty($folder) ? $folder.'/' : '';
		$this->zip->addFromString($folder.'settings.json', $data);
	}


	/**
	 * Adds slider images to ZIP
	 *
	 * @since 5.0.3
	 * @access public
	 * @param mixed $files Image path or an array of image paths to be added
	 * @param string $folder Sub-folder name
	 * @param string $imagesDir Name of the folder that stores the images
	 * @param string $forceDir Forces the provided directory name
	 * @return void
	 */
	public function addImage($files, $folder = '', $imagesDir = 'uploads', $forceDir = false ) {

		// Check file
		if( empty( $files ) ) {
			return false;
		}

		// Check file type
		if( ! is_array( $files ) ) {
			$files = [ $files ];
		}

		// Add contents to ZIP
		foreach( $files as $file ) {

			if( ! empty( $file ) && is_string( $file ) ) {

				if( ! $forceDir ) {
					$isAsset = ( strpos( $file, '/layerslider/assets/' ) !== false );
					$imagesDir = $isAsset ? 'assets' : 'uploads';
				}

				$projectDir = is_string($folder) ? $folder."/$imagesDir/" : "$imagesDir/";

				$this->zip->addFile( $file, $projectDir.basename( $file ) );
			}
		}
	}


	/**
	 * Adds any kind of file to the export ZIP
	 *
	 * @since 6.6.3
	 * @access public
	 * @param mixed $file File path or an array of file paths to be added
	 * @param string $path File location within the ZIP package
	 * @return void
	 */
	public function addFile($files, $path = '/') {

		// Check file
		if(empty($files)) { return false; }

		// Check file type
		if(!is_array($files)) { $files = [ $files ]; }

		// Add contents to ZIP
		foreach($files as $file) {
			if(!empty($file) && is_string($file)) {
				$this->zip->addFile($file,
					$path.sanitize_file_name(basename($file))
				);
			}
		}
	}


	/**
	 * Adds any kind of file with the provided contents to the ZIP package.
	 *
	 * @since 5.0.3
	 * @access public
	 * @param string $data Slider settings JSON
	 * @return void
	 */
	public function addFileFromString( $path = '/slider.html', $data = '') {
		$this->zip->addFromString($path, $data);
	}


	/**
	 * Closes all pending operations and downloads the ZIP file.
	 *
	 * @since 5.0.3
	 * @access public
	 * @return void
	 */
	public function download( $name = false ) {

		// Close ZIP operations
		$this->zip->close();

		// File name
		if( empty( $name ) ) {
			$name = 'LayerSlider_Export_'.ls_date('Y-m-d').'_at_'.ls_date('H.i.s').'.zip';
		}

		// Set headers and to user
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'.$name.'"');
		header("Content-length: " . filesize($this->file));
		header('Pragma: no-cache');
		header('Expires: 0');
		readfile($this->file);

		// Remove temporary file
		unlink($this->file);
		die();
	}


	public function getImagesForSlider($data) {

		// Array to hold image URLs
		$this->imageList = [];

		// Slider Preview
		if( ! empty($data['meta'] ) ) {
			$this->_addImageToList( $data['meta'], 'previewId', 'preview' );
		}

		$this->_addImageToList( $data['properties'], 'backgroundimageId', 'backgroundimage' );


		// Slides
		if(!empty($data['layers']) && is_array($data['layers'])) {
		foreach($data['layers'] as $slide) {

				if( ! empty( $slide['properties'] ) ) {
					$this->_addImageToList( $slide['properties'], 'backgroundId', 'background' );
					$this->_addImageToList( $slide['properties'], 'thumbnailId', 'thumbnail' );
				}


				// Layers
				if(!empty($slide['sublayers']) && is_array($slide['sublayers'])) {
					foreach($slide['sublayers'] as $layer) {

						$this->_addImageToList( $layer, 'imageId', 'image' );
						$this->_addImageToList( $layer, 'posterId', 'poster' );
						$this->_addImageToList( $layer, 'layerBackgroundId', 'layerBackground' );

						// Media uploads
						if( ! empty( $layer['mediaAttachments'] ) ) {
							foreach( $layer['mediaAttachments'] as $media ) {
								$this->imageList[] = $media['url'];
							}
						}
					}
				}
			}
		}

		return $this->imageList;
	}


	// DEPRECATED: Should not be used
	// Returns empty array as a compatibility measure.
	public function fontsForSlider( $data ) {
		return [];
	}


	public function getFSPaths($urls) {

		if(!empty($urls) && is_array($urls)) {

			$paths 		= [];
			$upload 	= wp_upload_dir();
			$uploadDir 	= basename($upload['basedir']);


			foreach($urls as $url) {

				// Get URL relative to the uploads folder
				$urlPath = parse_url($url, PHP_URL_PATH);
				$urlPath = explode("/$uploadDir/", $urlPath);

				if( empty($urlPath[1]) ) {
					continue;
				}

				$urlPath = $urlPath[1];

				// Get file path
				$filePath = $upload['basedir'] .'/'. $urlPath;
				$filePath = realpath($filePath);

				// Add to array
				if( file_exists($filePath) && is_file($filePath) ) {
					$paths[] = $filePath;
				}
			}

			return $paths;
		}

		return [];
	}

	protected function _addImageToList( $data, $idKey = '', $urlKey = '' ) {

		if( ! empty( $data[ $urlKey ] ) ) {
			$src = $data[ $urlKey ];
		}

		if( ! empty( $data[ $idKey ] ) ) {
			if( $result = wp_get_attachment_image_url( $data[ $idKey ], 'full' ) ) {
				$src = $result;
			}
		}

		if( ! empty( $src) ) {
			$this->imageList[] = $src;
		}
	}
}