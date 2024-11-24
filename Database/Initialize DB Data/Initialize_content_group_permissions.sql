insert into 
    cosn.content_group_permissions (
      content_group_permission_id, 
      target_content_id, 
      target_group_id, 
      content_group_permission_type
    )
  values
    (
      1, 
      2, 
      1, 
      'read'
    );

    insert into 
    cosn.content_group_permissions (
      content_group_permission_id, 
      target_content_id, 
      target_group_id, 
      content_group_permission_type
    )
  values
    (
      2, 
      3, 
      2, 
      'comment'
    );

    insert into 
    cosn.content_group_permissions (
      content_group_permission_id, 
      target_content_id, 
      target_group_id, 
      content_group_permission_type
    )
  values
    (
      3, 
      4, 
      3, 
      'share'
    );

    insert into 
    cosn.content_group_permissions (
      content_group_permission_id, 
      target_content_id, 
      target_group_id, 
      content_group_permission_type
    )
  values
    (
      4, 
      2, 
      1, 
      'link'
    );