-- truncate table cosn.member_relationships;
-- truncate table cosn.group_members;
-- truncate table cosn.content_public_permissions;
-- truncate table cosn.content_member_permission;
-- truncate table cosn.content_group_permissions;
-- truncate table cosn.groups;
-- truncate table cosn.content;
-- truncate table cosn.members;


--INIT_MEMBERS
--insert a "private" system member
insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    1, 
    'admin', 
    'admin', 
    'jhomme@email.com', 
    'Josh', 
    'Homme', 
    '567 Road', 
    '1973-04-17', 
    'administrator', 
    'joshieboy', 
    'active',
    FALSE
  );

-- insert a "public" system member
  insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    2, 
    'steve_colbert', 
    'sc2222', 
    'scolbert@email.com', 
    'Stephen', 
    'Colbert', 
    '999 Roadblock', 
    '1964-04-13', 
    'administrator', 
    'stevie', 
    'inactive',
    FALSE
  );

  -- insert a "jimi hendrix" senior member, with status 'inactive'
    insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    3, 
    'purple_haze', 
    'jh3333', 
    'jhendrix@email.com', 
    'Jimi', 
    'Hendrix', 
    '1524 Haight St', 
    '1942-11-27', 
    'senior', 
    'purple_haze69', 
    'inactive',
    FALSE
  );

  -- insert a "jim morrison" senior member, with status 'suspended'
  insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    4, 
    'lizard_king', 
    'pw4444', 
    'jmorrison@email.com', 
    'Jim', 
    'Morrison', 
    '8021 Rothdell Trail', 
    '1943-12-08', 
    'senior', 
    'lizard_king01', 
    'suspended',
    FALSE
  );

  
  -- insert a "Matt Bellamy" senior member, with status 'active'
  insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    5, 
    'starlight', 
    'pw5555', 
    'mbellamy@email.com', 
    'Matt', 
    'Bellamy', 
    '10250 Constellation Blvd', 
    '1978-06-09', 
    'senior', 
    'starlight78', 
    'active',
    FALSE
  );

    -- insert a "Lana Del Rey" junior member, with status active
  insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    6, 
    'deadly_nightshade', 
    'pw6666', 
    'ldr@email.com', 
    'Lana', 
    'Del Rey', 
    '3415 Coldwater Canyon Ave', 
    '1985-06-21', 
    'junior', 
    'deadly_nightshade7', 
    'active',
    FALSE
  );
-- insert a "sharon den adel" senior member, with status 'active'
  insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    7, 
    'icequeen', 
    'pw7777', 
    'sda@email.com', 
    'Sharon', 
    'Den Adel', 
    '123 Dublin', 
    '1960-10-25', 
    'senior', 
    'icequeen77', 
    'active',
    FALSE
  );
  -- insert a "axl" senior member, with status 'inactive'
  insert into 
  cosn.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
    pseudonym, 
    status,
    corporation_flag
  )
values
  (
    8, 
    'novemberrain', 
    'pw8888', 
    'gnr@email.com', 
    'Axl', 
    'Rose', 
    '66 Drive', 
    '1962-02-06', 
    'senior', 
    'novemberrain 66', 
    'inactive'
    ,FALSE
  );
  
--INIT_CONTENT
insert into 
  cosn.content (
    content_id, 
    creator_id, 
    content_type, 
    content_data, 
    content_creation_date
  )
values
  (
    1, 
    1,
    'text', 
    'URL for text', 
    '2024-01-01'
  );
  
  insert into 
    cosn.content (
      content_id,
      creator_id, 
      content_type, 
      content_data, 
      content_creation_date
    )
  values
    (
      2, 
      2,
      'image', 
      'URL for image', 
      '2024-01-02'
    );

    insert into 
    cosn.content (
      content_id,
      creator_id, 
      content_type, 
      content_data, 
      content_creation_date
    )
  values
    (
      3, 
      3,
      'video', 
      'URL for video', 
      '2024-01-03'
    );

    insert into 
    cosn.content (
      content_id,
      creator_id, 
      content_type, 
      content_data, 
      content_creation_date
    )
  values
    (
      4, 
      4,
      'video', 
      'URL for video', 
      '2024-01-04'
    );

insert into 
    cosn.content (
      content_id,
      creator_id, 
      content_type, 
      content_data, 
      content_creation_date
    )
  values
    (
      5, 
      5,
      'video', 
      'URL for video', 
      '2024-01-05'
    );

    insert into 
    cosn.content (
      content_id,
      creator_id, 
      content_type, 
      content_data, 
      content_creation_date
    )
  values
    (
      6, 
      6,
      'video', 
      'URL for video', 
      '2024-01-06'
    );


    insert into 
    cosn.content (
      content_id,
      creator_id, 
      content_type, 
      content_data, 
      content_creation_date
    )
  values
    (
      7, 
      7,
      'video', 
      'URL for video', 
      '2024-01-07'
    );

    insert into 
    cosn.content (
      content_id,
      creator_id, 
      content_type, 
      content_data, 
      content_creation_date
    )
  values
    (
      8, 
      8,
      'video', 
      'URL for video', 
      '2024-01-08'
    );

--INIT_GROUPS
insert into 
  cosn.groups (
    group_id, 
    group_name, 
    owner_id, 
    description, 
    creation_date
  )
values
  (
    1, 
    'Games', 
    1, 
    'Group that is interested in the discussion and content sharing related to computer games', 
    '2022-03-04'
  );

  insert into 
  cosn.groups (
    group_id, 
    group_name, 
    owner_id, 
    description, 
    creation_date
  )
values
  (
    2, 
    'Tunes', 
    2, 
    'Group that is interested in the discussion and content sharing related to music of all genres', 
    '2021-04-20'
  );
  
  insert into 
  cosn.groups (
    group_id, 
    group_name, 
    owner_id, 
    description, 
    creation_date
  )
values
  (
    3, 
    'Foodies', 
    3, 
    'Group that is interested in the discussion and content sharing related to food and fine dining', 
    '2020-06-12'
  );

    insert into 
  cosn.groups (
    group_id, 
    group_name, 
    owner_id, 
    description, 
    creation_date
  )
values
  (
    4, 
    'Movies', 
    4, 
    'Group that is interested in the discussion and content sharing related to movies and filmmaking', 
    '2024-02-21'
  );

  --INIT_CONTENT_GROUP_PERMISSIONS
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
      5, 
      1, 
      'link'
    );

--INIT_CONTENT_MEMBER_PERMISSIONS

insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    1, 
    1, 
    1, 
    'read'
  );

  insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    2, 
    2, 
    2, 
    'read'
  );

  insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    3, 
    3, 
    1, 
    'edit'
  );

  insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    4, 
    4, 
    2, 
    'comment'
  );

  insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    5, 
    5, 
    1, 
    'share'
  );

  insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    6, 
    6, 
    2, 
    'modify-permission'
  );

  insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    7, 
    2, 
    1, 
    'moderate'
  );

  insert into 
  cosn.content_member_permission (
    content_member_permission_id, 
    target_content_id, 
    authorized_member_id, 
    content_permission_type
  )
values
  (
    8, 
    4, 
    2, 
    'link'
  );

  --INIT_CONTENT_PUBLIC_PERMISSIONS
  INSERT INTO cosn.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 1, 2, 'read' );

  INSERT INTO cosn.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 2, 3, 'comment' );
  INSERT INTO cosn.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 3, 4, 'share' );

  INSERT INTO cosn.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 4, 5, 'link' );

--INIT_MEMBERS_GROUPS
insert into 
 cosn.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    role_of_member
  )
values
  (
    1, 
    1, 
    2, 
    '2022-03-04', 
    'owner'
  );

  insert into 
  cosn.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    role_of_member
  )
values
  (
    2, 
    2, 
    1, 
    '2021-04-20', 
    'owner'
  );

  insert into 
  cosn.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    role_of_member
  )
values
  (
    3, 
    3, 
    4, 
    '2022-05-28', 
    'member'
  );

  insert into 
  cosn.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    role_of_member
  )
values
  (
    4, 
    4, 
    3, 
    '2023-01-07', 
    'member'
  );

  --INIT_MEMBERS_RELATIONSHIPS

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