

INSERT INTO cosn.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 1, 2, 3, 'friend', 'requested' );

INSERT INTO cosn.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 2, 4, 5, 'family', 'approved' );

INSERT INTO cosn.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 3, 2, 6, 'colleague', 'rejected' );

INSERT INTO cosn.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 4, 1, 7, 'blocked', 'approved' );
