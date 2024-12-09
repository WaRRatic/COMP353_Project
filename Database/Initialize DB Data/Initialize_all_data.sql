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
   
    'inactive',
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
    'jh3333', 
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
    'pw4444', 
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
    6, 
    6, 
    2, 
    'modify-permission'
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
    7, 
    2, 
    1, 
    'moderate'
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

-- Add test members with categories


INSERT INTO kpc353_2.member_categories (category_type, category_name) VALUES
('interest', 'Gaming'),
('interest', 'Music'),
('interest', 'Sports'),
('interest', 'Technology'),
('interest', 'Art'),
('age_group', '18-24'),
('age_group', '25-34'),
('age_group', '35-44'),
('age_group', '45+'),
('profession', 'Technology'),
('profession', 'Healthcare'),
('profession', 'Education'),
('profession', 'Business'),
('profession', 'Arts'),
('region', 'North America'),
('region', 'Europe'),
('region', 'Asia'),
('region', 'South America'),
('region', 'Africa'),
('region', 'Oceania');


INSERT INTO kpc353_2.member_category_assignments (member_id, category_id) VALUES
(1, 1), (1, 7), (1, 11), (1, 16), -- Admin: Gaming, 25-34, Technology, North America
(2, 2), (2, 8), (2, 12), (2, 16), -- Steve: Music, 35-44, Healthcare, North America 
(3, 3), (3, 7), (3, 13), (3, 17), -- Jimi: Sports, 25-34, Education, Europe
(4, 4), (4, 9), (4, 14), (4, 18), -- Jim: Technology, 45+, Business, Asia
(5, 5), (5, 7), (5, 15), (5, 16); -- Matt: Art, 25-34, Arts, North America

-- Add gift registries
INSERT INTO kpc353_2.gift_registry (gift_registry_id, organizer_member_id, gift_registry_name, gift_registry_description) VALUES
(1, 2, 'Steve\'s Birthday', 'Birthday wishlist for September'),
(2, 3, 'Jimi\'s Wedding', 'Wedding registry for summer wedding'),
(3, 4, 'Jim\'s Housewarming', 'New house celebration gifts'),
(4, 5, 'Matt\'s Graduation', 'PhD graduation celebration');

SELECT member_id, username FROM kpc353_2.members;

-- Add participants
INSERT INTO kpc353_2.gift_registry_participants (participant_member_id, target_gift_registry_id) VALUES
(1, 1), (3, 1), (4, 1), (5, 1), -- Steve's registry participants
(1, 2), (2, 2), (4, 2), (5, 2), -- Jimi's registry participants
(1, 3), (2, 3), (3, 3), (5, 3), -- Jim's registry participants
(1, 4), (2, 4), (3, 4), (4, 4); -- Matt's registry participants

-- Add gift ideas
INSERT INTO kpc353_2.gift_registry_ideas (target_gift_registry_id, idea_owner_id, gift_idea_description) VALUES
(1, 2, 'Vintage Record Player'),
(1, 3, 'Concert Tickets'),
(1, 4, 'Vinyl Collection'),
(2, 3, 'Kitchen Aid Mixer'),
(2, 4, 'Wine Glass Set'),
(2, 5, 'Cooking Class Voucher'),
(3, 4, 'House Plants'),
(3, 2, 'Art Piece'),
(3, 5, 'Coffee Machine'),
(4, 5, 'Professional Camera'),
(4, 1, 'Photography Books'),
(4, 3, 'Camera Lens Set');

-- Add some gifts
INSERT INTO kpc353_2.gift_registry_gifts (target_gift_registry_id, gift_registry_idea_id, sender_member_id, target_member_id, gift_status, gift_date) VALUES
(1, 1, 3, 2, 'received', '2024-01-15'),  -- to Steve
(1, 2, 4, 2, 'sent', '2024-02-01'),      -- to Steve
(2, 4, 2, 3, 'received', '2024-02-10'),   -- to Jimi
(2, 5, 5, 3, 'sent', '2024-02-15'),      -- to Jimi
(3, 7, 2, 4, 'pending', '2024-02-20'),    -- to Jim
(4, 10, 1, 5, 'received', '2024-02-25');  -- to Matt