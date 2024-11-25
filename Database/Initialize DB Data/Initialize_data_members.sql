-- Active: 1731271987208@@127.0.0.1@3306@cosn
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
  
