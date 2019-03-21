<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 07/12/2017
	 **/

	function complaintAdd($authorId, $objectId, $mainType, $optionalDescription = NULL, int $complaint_type) {
		$qInsertStr = qInsert("complaints", [
			"main_type" => intval($mainType),
			"complaint_author_id" => intval($authorId),
			"object_id" => intval($objectId),
			"description" => $optionalDescription,
			"create_date" => time(),
			"complaint_type" => intval($complaint_type)
		]);
		squery($qInsertStr['query']);
		return [
			"status" => true
		];
	}