ALTER TABLE `#__xbgroupperson` ADD `joined` VARCHAR(20) NOT NULL DEFAULT '' AFTER `person_id`, ADD `until` VARCHAR(20) NOT NULL DEFAULT '' AFTER `joined`;