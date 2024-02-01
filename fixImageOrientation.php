<?php
function static fixImageOrientation( $imagePath )
	{
		if (!is_readable($imagePath)) {
			throw new Exception("Cannot read '{$imagePath}' file!");
		}

		if (!function_exists('exif_read_data')) {
			return 0;
		}

		$data        = exif_read_data($imagePath);

		$orientation = isset($data['Orientation']) ? (int)$data['Orientation'] : 0;

		switch($orientation) {
			case 1:
			case 2:
				$fixOrientationTo = 0;
				break;
			case 3:
			case 4:
				$fixOrientationTo = 180;
				break;
			case 5:
			case 6:
				$fixOrientationTo = 90;
				break;
			case 7:
			case 8:
				$fixOrientationTo = 270;
				break;
			default:
				$fixOrientationTo = 0;
				break;
		}

		if ( $fixOrientationTo && is_writable($imagePath) ) {

			Yii::import('ext.EWideImage.EWideImage');
			$imagePathInfo = pathinfo($imagePath);

			if ( empty($imagePathInfo['extension']) || $imagePathInfo['extension'] == 'tmp' )
			{
				copy($imagePath, $imagePath.'.jpg');
				EWideImage::load($imagePath.'.jpg')->rotate($fixOrientationTo)->saveToFile($imagePath.'.jpg');
				copy($imagePath.'.jpg', $imagePath);
				unlink($imagePath.'.jpg');
			}
			else
			{
				EWideImage::load($imagePath)->rotate($fixOrientationTo)->saveToFile($imagePath);
			}


		} elseif ( !is_writable($imagePath) ) {
			throw new Exception("Cannot write '{$imagePath}' file!");
		}

		return $fixOrientationTo;
	}
