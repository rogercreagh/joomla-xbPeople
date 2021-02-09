DELETE FROM `#__ucm_history` WHERE ucm_type_id in 
	(select type_id from `#__content_types` where type_alias in ('com_xbpeople.person','com_xbpeople.character','com_xbpeople.category'));
DELETE FROM `#__ucm_base` WHERE ucm_type_id in 
	(select type_id from `#__content_types` WHERE type_alias in ('com_xbpeople.person','com_xbpeople.character','com_xbpeople.category'));
DELETE FROM `#__ucm_content` WHERE core_type_alias in ('com_xbpeople.person','com_xbpeople.character','com_xbpeople.category');
DELETE FROM `#__contentitem_tag_map`WHERE type_alias in ('com_xbpeople.person','com_xbpeople.character','com_xbpeople.category');
DELETE FROM `#__content_types` WHERE type_alias in ('com_xbpeople.person','com_xbpeople.character','com_xbpeople.category');
