<?php
$config["assets_folder"] = "ui";
$config["assets_path"] = $_SERVER['DOCUMENT_ROOT']."/".$config["assets_folder"];
$config["default_level"] = "shared";
$config["assets_version"] = "20121031";
$config["assets_url"] = "http://res.500mi.com";

// set asset types and folders
$config["asset_types"] = array(
								"flv"		=>	"flash",
								"swf"		=>	"flash",
								"jpg"		=>	"images",
								"png"		=>	"images",
								"gif"		=>	"images",
								"js"		=>	"js",
								"css"		=>	"css"
								);
?>