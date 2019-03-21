<?php
	function userPhotoUpdate($user_id, $photo_id) {
		$fileInfo = fileGet($photo_id);
		$status = $fileInfo['status'];
		if (!$status || $fileInfo['file']['type'] != 'image') {
			return [
				'status' => false
			];
		} else {
			$sqr = "UPDATE `users` SET `photo_id` = '".$photo_id."' WHERE `id` = ".$user_id.";";
			squery($sqr);
			return [
				'status' => true,
				'file' => $fileInfo['file']
			];
		}
	}

	function userPhotoRemove($user_id) {
		$sqr = "UPDATE `users` SET `photo_id` = '' WHERE `id` = ".$user_id.";";
		squery($sqr);
		return [
			'status' => true
		];
	}

	function blogPhotoUpdate($blog_id, $photo_id) {
		$fileInfo = fileGet($photo_id);
		$status = $fileInfo['status'];
		if (!$status || $fileInfo['file']['type'] != 'image') {
			return [
				'status' => false
			];
		} else {
			$sqr = "UPDATE `blogs` SET `photo` = '".$photo_id."' WHERE `id` = ".$blog_id.";";
			squery($sqr);
			return [
				'status' => true,
				'file' => $fileInfo['file']
			];
		}
	}

	function blogPhotoRemove($blog_id) {
		$sqr = "UPDATE `blogs` SET `photo` = '' WHERE `id` = ".$blog_id.";";
		squery($sqr);
		return [
			'status' => true
		];
	}