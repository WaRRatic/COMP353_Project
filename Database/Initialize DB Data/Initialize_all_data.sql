-- truncate table kpc353_2.member_relationships;
-- truncate table kpc353_2.group_members;
-- truncate table kpc353_2.content_public_permissions;
-- truncate table kpc353_2.content_member_permission;
-- truncate table kpc353_2.content_group_permissions;
-- truncate table kpc353_2.groups;
-- truncate table kpc353_2.content;
-- truncate table kpc353_2.members;


-- INIT_MEMBERS
-- insert a "private" system member
insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
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
     
    'active',
    FALSE
  );

-- insert a "public" system member
  insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
    status,
    corporation_flag
  )
values
  (
    2, 
    'steve_colbert', 
    '1', 
    'scolbert@email.com', 
    'Stephen', 
    'Colbert', 
    '999 Roadblock', 
    '1964-04-13', 
    'senior', 
   
    'active',
    FALSE
  );

  -- insert a "jimi hendrix" senior member, with status 'inactive'
    insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
    status,
    corporation_flag
  )
values
  (
    3, 
    'purple_haze', 
    '3', 
    'jhendrix@email.com', 
    'Jimi', 
    'Hendrix', 
    '1524 Haight St', 
    '1942-11-27', 
    'senior', 
   
    'inactive',
    FALSE
  );

  -- insert a "jim morrison" senior member, with status 'suspended'
  insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
    status,
    corporation_flag
  )
values
  (
    4, 
    'lizard_king', 
    '4', 
    'jmorrison@email.com', 
    'Jim', 
    'Morrison', 
    '8021 Rothdell Trail', 
    '1943-12-08', 
    'senior', 
  
    'suspended',
    FALSE
  );

  
  -- insert a "Matt Bellamy" senior member, with status 'active'
  insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
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
  
    'active',
    FALSE
  );

    -- insert a "Lana Del Rey" junior member, with status active
  insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
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
  
    'active',
    FALSE
  );
-- insert a "sharon den adel" senior member, with status 'active'
  insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
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
     
    'active',
    FALSE
  );
  -- insert a "axl" senior member, with status 'inactive'
  insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
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
     
    'inactive'
    ,FALSE
  );
  
  -- evy mcgee-colbert, wife of steven colbert
 insert into 
  kpc353_2.members (
    member_id, 
    username, 
    password, 
    email, 
    first_name, 
    last_name, 
    address, 
    date_of_birth, 
    privilege_level, 
     
    status,
    corporation_flag
  )
values
  (
    9, 
    'evy', 
    '22', 
    'evy@email.com', 
    'Evelyn', 
    'McGee-Colbert', 
    'South Caroline', 
    '1963-07-03', 
    'senior', 
  
    'active'
    ,FALSE
  );

-- INIT_CONTENT
insert into 
  kpc353_2.content (
    content_id, 
    creator_id, 
    content_type, 
    content_data, 
    content_creation_date,
    content_title
    , moderation_status
  )
values
  (
    1, 
    1,
    'text', 
    "What's the difference between a hippo and a zippo? One is really heavy, and the other's a little lighter.", 
    '2024-01-01',
    'Joke',
    'approved'
  );
  
  insert into 
    kpc353_2.content (
      content_id,
      creator_id, 
      content_type, 
      content_data,
      content_title, 
      content_creation_date,
      moderation_status
    )
  values
    (
      2, 
      2,
      'image', 
      'content_data ="https://en.wikipedia.org/wiki/Duck#/media/File:Bucephala-albeola-010.jpg" ',
      'Duck Image',
      '2024-01-02'
      , 'approved'
    );

    insert into 
    kpc353_2.content (
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
    kpc353_2.content (
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
    kpc353_2.content (
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
    kpc353_2.content (
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
    kpc353_2.content (
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
    kpc353_2.content (
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

INSERT INTO kpc353_2.content 
(content_id, creator_id, content_type, content_data, content_creation_date, content_title, moderation_status)
VALUES 
(9, 9, 'text', 'Steven Colbert, come eat - the dinner is ready', NOW(), 'Dinner Call', 'approved');

INSERT INTO kpc353_2.content 
(content_id, creator_id, content_type, content_data, content_creation_date, content_title, moderation_status)
VALUES 
(10, 9, 'image', 'https://upload.wikimedia.org/wikipedia/commons/6/6b/American_Beaver.jpg', NOW(), 'American Beaver Image', 'approved');

INSERT INTO kpc353_2.content 
(content_id, creator_id, content_type, content_data, content_creation_date, content_title, moderation_status)
VALUES 
(11, 9, 'video', 'https://www.youtube.com/watch?v=eAPqQFWEoKg&t=117s&ab_channel=BBCEarth', NOW(), 'Beaver Dam Video', 'approved');

INSERT INTO kpc353_2.content 
(content_id, creator_id, content_type, content_data, content_creation_date, content_title, moderation_status)
VALUES 
(12, 8, 'image', 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Obscured_jaguar.jpg', NOW(), 'Jaguar', 'approved');

-- INIT_GROUPS
insert into 
  kpc353_2.groups (
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
  kpc353_2.groups (
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
  kpc353_2.groups (
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
  kpc353_2.groups (
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

  -- INIT_CONTENT_GROUP_PERMISSIONS
  insert into 
    kpc353_2.content_group_permissions (
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
    kpc353_2.content_group_permissions (
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
    kpc353_2.content_group_permissions (
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
    kpc353_2.content_group_permissions (
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
    insert into 
    kpc353_2.content_group_permissions (
      content_group_permission_id, 
      target_content_id, 
      target_group_id, 
      content_group_permission_type
    )
  values
    (
      5, 
      12, 
      1, 
      'read'
    );

-- INIT_CONTENT_MEMBER_PERMISSIONS

insert into 
  kpc353_2.content_member_permission (
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
  kpc353_2.content_member_permission (
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
  kpc353_2.content_member_permission (
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
  kpc353_2.content_member_permission (
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
  kpc353_2.content_member_permission (
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
  kpc353_2.content_member_permission (
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


INSERT INTO kpc353_2.content_member_permission
(content_member_permission_id, target_content_id, authorized_member_id, content_permission_type)
VALUES
(9,9, 2, 'comment');

INSERT INTO kpc353_2.content_member_permission
	( target_content_id, authorized_member_id, content_permission_type) VALUES ( 9, 2, 'edit' );
INSERT INTO kpc353_2.content_member_permission
	( target_content_id, authorized_member_id, content_permission_type) VALUES ( 10, 2, 'edit' );
INSERT INTO kpc353_2.content_member_permission
	( target_content_id, authorized_member_id, content_permission_type) VALUES ( 11, 2, 'share' );

  -- INIT_CONTENT_PUBLIC_PERMISSIONS
 
  INSERT INTO kpc353_2.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 1, 2, 'read' );

  INSERT INTO kpc353_2.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 2, 3, 'comment' );
  INSERT INTO kpc353_2.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 3, 4, 'share' );

  INSERT INTO kpc353_2.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 4, 5, 'link' );

    INSERT INTO kpc353_2.content_public_permissions
	( content_public_permission_id, target_content_id, content_public_permission_type) VALUES ( 5, 1, 'read' );

-- INIT_MEMBERS_GROUPS
insert into 
 kpc353_2.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    group_member_status
  )
values
  (
    1, 
    1, 
    2, 
    '2022-03-04', 
    'admin'
  );

  insert into 
  kpc353_2.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    group_member_status
  )
values
  (
    2, 
    2, 
    1, 
    '2021-04-20', 
    'admin'
  );

  insert into 
  kpc353_2.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    group_member_status
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
  kpc353_2.group_members (
    group_membership_id, 
    participant_member_id, 
    joined_group_id, 
    date_joined, 
    group_member_status
  )
values
  (
    4, 
    4, 
    3, 
    '2023-01-07', 
    'member'
  );

  -- INIT_MEMBERS_RELATIONSHIPS

INSERT INTO kpc353_2.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 1, 2, 3, 'friend', 'requested' );

INSERT INTO kpc353_2.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 2, 4, 5, 'family', 'approved' );

INSERT INTO kpc353_2.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 3, 2, 6, 'colleague', 'rejected' );

INSERT INTO kpc353_2.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 4, 1, 7, 'blocked', 'approved' );

-- steven colbert is friends with Matt Bellamy
  INSERT INTO kpc353_2.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 5, 2, 5, 'friend', 'approved' );

-- steven colbert has "family" relationship with evelyn mcgee-colbert
    INSERT INTO kpc353_2.member_relationships
	( relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
  VALUES ( 6, 2, 9, 'family', 'approved' );

-- make it so admin is friends with user id 2 and user id 3 and vice versa
INSERT INTO kpc353_2.member_relationships (relationship_id, origin_member_id, target_member_id, member_relationship_type, member_relationship_status) 
VALUES 
(7, 1, 2, 'friend', 'approved'), 
(8, 1, 3, 'friend', 'approved'),
(9,2,1,'friend','approved'),
(10,3,1,'friend','approved');

-- INIT CONTENT_COMMENT
INSERT INTO kpc353_2.content_comment
	( content_comment_id, commenter_member_id, comment_text, target_content_id, datetime_comment) 
  VALUES ( 1, 2, "ok, in 2 minutes!", 9,  NOW());

  INSERT INTO kpc353_2.content_comment
	( content_comment_id, commenter_member_id, comment_text, target_content_id, datetime_comment) 
  VALUES ( 2, 9, "Hurry up, the food will get cold!", 9,  NOW());


--INIT PERSONAL INFO PERMISSIONS
INSERT INTO kpc353_2.personal_info_permissions
	(owner_member_id, personal_info_type, authorized_member_id) VALUES (  3,'email', 2 );

-- INIT PERSONAL INFO PUBLIC PERMISSIONS
INSERT INTO kpc353_2.personal_info_public_permissions
	(owner_member_id, personal_info_type) VALUES ( 3,'first_name');
  
INSERT INTO kpc353_2.personal_info_public_permissions
	(owner_member_id, personal_info_type) VALUES ( 3,'last_name');

-- INIT MEMBER MESSAGES
insert into kpc353_2.member_messages 
    (origin_member_id, target_member_id, message_content)
    values
    (2, 1, 'wassup');

insert into kpc353_2.member_messages 
    (origin_member_id, target_member_id, message_content)
    values
    (1, 2, 'all good and you?');

insert into kpc353_2.member_messages 
    (origin_member_id, target_member_id, message_content)
    values
    (9, 2, 'dinner is readyyyy!');

insert into kpc353_2.member_messages 
    (origin_member_id, target_member_id, message_content)
    values
    (2, 9, 'coming!');

-- INIT Private and Public permissions
--everyone can see Steve's email
insert into kpc353_2.personal_info_public_permissions
(owner_member_id, personal_info_type)
values ( 2, 'email');

--only Jimi Hendrix can see Steve's address
insert into kpc353_2.personal_info_permissions
(owner_member_id, personal_info_type,authorized_member_id)
values ( 2, 'address',3);

--only Jim Morrison can see Steve's DOB
insert into kpc353_2.personal_info_permissions
(owner_member_id, personal_info_type,authorized_member_id)
values ( 2, 'date_of_birth',4);



select personal_info_type
FROM kpc353_2.personal_info_public_permissions
where owner_member_id = 2;

SELECT owner_member_id, personal_info_type,authorized_member_id
FROM kpc353_2.personal_info_permissions
where owner_member_id = 2;
