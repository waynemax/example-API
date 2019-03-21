<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 07/09/2017
	 **/
	$_ERRORS = array(
		"methodNotFound" 		=> array("methodNotFound", 			"Method not found"),
		"fileNotFound" 			=> array("fileNotFound", 			"File not found"),
		"needFields" 			=> array("needFields", 				"Missing a required fields: "),
		"countLimitUsers" 		=> array("countLimitUsers", 		"Maximum number of users: 100"),
		"onlyPOST" 				=> array("onlyPOST", 				"Only POST requests are supported"),
		"onlyGET" 				=> array("onlyGET", 				"Only GET requests are supported"),
		"unacceptable_symbols" 	=> array("unacceptable_symbols", 	"Invalid characters in the field"),
		"access_denied" 		=> array("access_denied", 			"Access denied. Invalid:"),
		"not_found" 			=> array("not_found", 				"Nothing found on your request"),
		"permission_denied" 	=> array("permission_denied", 		"Permission_denied. Field:"),
		"samePhone" 			=> array("samePhone", 				"This phone number already exists"),
		"sameLogin" 			=> array("sameLogin", 				"This login already exists"),
		"same" 				    => array("same", 					"Field already exists"),
		"needAuth" 				=> array("needAuth", 				"authorization required"),
		"invalidField" 			=> array("invalidField", 			"invalid Field:"),
		"unknownError"			=> array("unknownError", 			"Unknown Error."),
        "unknownField"			=> array("unknownField", 			"unknown field."),
        "NotEnoughMoney"		=> array("NotEnoughMoney", 			"Not enough money."),
        "mustBeDifferent"	    => array("mustBeDifferent", 		"Must be different."),
        "countLimitObjects" 	=> array("countLimitObjects", 		"Maximum number of objects"),
        "notAllUsersAreValid"   => array("notAllUsersAreValid", 	"Not All Users Are Valid"),
        "maxMembersChatLimit"   => array("maxMembersChatLimit", 	"Exceeded the maximum number of people in the chat"),
        "maxNumberCharacters"   => array("maxNumberCharacters", 	"Exceeded maximum number of characters"),
        "copyChatNotExist"      => array("copyChatNotExist",        "Copy chat with this id not exist"),
        "userIsNotInChat"       => array("userIsNotInChat",         "User is not in chat"),
        "itsNotMultiChat"       => array("itsNotMultiChat",         "You can't leave the dialogue"),
        "emptyValue"            => array("emptyValue",              "The value cannot be empty"),
        "shortMessage"          => array("shortMessage",            "Message too short"),
        "longMessage"           => array("longMessage",             "Message too long"),
        "err_connectMC"         => array("err_connectMC",           "Connecting error / MC"),
        "notValidTs"            => array("notValidTs",              "notValidTs"),
        "longPollError1"        => array("longPollError1",          "delete please `use_mc`"),
        "maxUploadCountImages"  => array("maxUploadCountImages",    "`images`"),
	);