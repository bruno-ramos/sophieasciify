<?php
/**
 * Sophie Asciify
 * Transforms an Image to Ascii text
 *
 * @author Bruno C. Ramos <bruno@wee.ag>
 * @version 1.0
 */
	class SophieAsciify {
		/**
		 * Define offset to the applied mask
		 * @var int
		 */
		public $offset = 2;

		/**
		 * Define character to display after asciified, in case of colorful images
		 * @var string
		 */
		public $char = '@';

		/**
		 * Custom style to apply to each character for color mode
		 * @var string
		 */
		public $colorStyle = 'font-family:Arial;font-size:4pt';

		 /**
		 * Custom style to apply to each character for grayscale mode
		 * @var string
		 */
		public $grayscaleStyle = 'font-family:Courier New;font-size:4pt';


		/**
		 * Characters to use in case of a grayscale image
		 * @var array
		 */
		public $shade = array('#','$','O','=','+','|','-','^','.',' ');
		
		/**
		 * Maximum reach of grayscale
		 * @var int
		 */
		public $grayscale = 255;
		
		/**
		 * Input file to proccess
		 * @var string
		 */
		public $input;

		/**
		 * Processing type
		 * @var string
		 */
		private $type;
		
		/**
		 * Calculated frontier
		 * @var int
		 */
		private $frontier;
		
		/**
		 * Available Mimes
		 * @var array
		 */
		private $availableMimes = array('image/jpeg','image/gif','image/png');


		/**
		 * Constructor
		 * @param $file File to asciify
		 */
		public function __construct($file=null) {
			if($file)
				$this->input = $file;
		}

		/**
		 * Server Colored Image
		 * @param $file File to asciify
		 */
		public function serveColor($file=null) {
			if($file)
				$this->input = $file;

			$this->type = 'color';

			return $this->validateFile();
		}

		/**
		 * Server Grayscale Image
		 * @param $file File to asciify
		 */
		public function serveGrayscale($file=null) {
			if($file)
				$this->input = $file;

			$this->type = 'grayscale';

			return $this->validateFile();
		}


		/**
		 * Validates file
		 */
		private function validateFile() {
			if(!$this->input)
				throw new Exception('No file provided');

			if(!is_file($this->input))
				throw new Exception('Could not find file.');

			$imgInfo = getimagesize($this->input);
			if(!$imgInfo)
				throw new Exception('Provided file is not a valid image.');

			
			$mime = $imgInfo['mime'];
			if(!in_array($mime, $this->availableMimes))
				throw new Exception('Cannot parse current image type.');

			return $this->prepareImage($imgInfo);
		}

		/**
		 * Prepares image to be asciified
		 * @param $imgInfo Array containing current image info
		 */
		private function prepareImage($imgInfo) {
			$im = null;

			// Get image info
			list($w, $h, $imageType) = $imgInfo;
            switch ($imageType) {
                case 1: $im = imagecreatefromgif($this->input); break;
                case 2: $im = imagecreatefromjpeg($this->input);  break;
                case 3: $im = imagecreatefrompng($this->input); break;
                default:  throw new \SophieException('Unsupported filetype!');  break;
            }

            // Prepare offset
			$offset = ($this->offset % 2)? $this->offset : $this->offset+1;

			// Check Dimensions
			if ($offset > $w || $offset > $h)
				throw new Exception('Image too small or offset too large.');
			

			// Get frontiers
			$this->frontier = (int)($this->grayscale/(sizeof($this->shade)-1));
			
			// Apply style
			switch($this->type) {
				case 'color':
					echo '<div style="'.$this->colorStyle.'">';
					break;
				case 'grayscale':
					echo '<div style="'.$this->grayscaleStyle.'">';
					break;
			}

			// Runs through image
			for($i=$offset/2; $i < $h-$offset/2; $i+=$offset) {
				for($j=$offset/2;$j < $w-$offset/2; $j+=$offset) {
					$total_r = 0;
					$total_g = 0;
					$total_b = 0;

					// Get average by applying a offset x offset mask
					for($m = $i - $offset/2, $a=0; $a < $offset; $a++,$m++) {
						for($n = $j - $offset/2, $b=0; $b < $offset; $b++,$n++) {
							$rgb = ImageColorAt($im,$n,$m);
							
							// extract each value for r, g, b
							$rr = ($rgb >> 16) & 0xFF;
							$gg = ($rgb >> 8) & 0xFF;
							$bb = $rgb & 0xFF;
							
							// Sums
							$total_r += $rr;
							$total_g += $gg;
							$total_b += $bb;
						}	
					}
					// Get average
					$total_r= (int)($total_r/($offset*$offset));
					$total_g = (int)($total_g/($offset*$offset));
					$total_b = (int)($total_b/($offset*$offset));
					
					$this->serveCharacter($total_r, $total_g, $total_b);

				}
				// New line
				echo "<br />";
			}
			// End of div with style
			echo "</div>";

			$this->reset();
		}

		/**
		 * Serve character according to current type
		 * @param $total_r Total of reds
		 * @param $total_g Total of greens
		 * @param $total_b Total of blues
		 */
		private function serveCharacter(&$total_r, &$total_g, &$total_b) {
			switch($this->type) {
				// Colored image
				case 'color':
					// Print
					echo '<span style="color:rgb('.$total_r.','.$total_g.','.$total_b.')">'.$this->char.'</span>';
					break;

				// Grayscale image
				case 'grayscale':
					// Transform to Grayscale
					$g = (int)round(($total_r + $total_g + $total_b)/3);
					
					// Get equivalent shade char
					$charPos = round($g/$this->frontier);
					
					// Print
					echo '<span style="color:#000">'.$this->shade[$charPos].'</span>';

					break;
			}
		}

		/**
		 * Reset object
		 */
		public function reset() {
			$this->input = null;
			$this->type = null;
			$this->frontier = null;	
		}

	}
?>