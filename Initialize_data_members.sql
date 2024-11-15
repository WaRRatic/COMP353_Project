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
    status
  )
values
  (
    1, 
    'private', 
    'pw', 
    'private@email.com', 
    'private', 
    'private', 
    'private', 
    '1999-12-31', 
    'administrator', 
    'private', 
    'system'
  );

--insert a "public" system member
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
    status
  )
values
  (
    2, 
    'public', 
    'pw', 
    'public@email.com', 
    'public', 
    'public', 
    'public', 
    '1999-12-31', 
    'administrator', 
    'public', 
    'system'
  );

  --insert a "jimi hendrix" senior member, with status 'inactive'
    insert into 
  members (
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
    status
  )
values
  (
    3, 
    'purple_haze', 
    'pw9999', 
    'jhendrix@email.com', 
    'Jimi', 
    'Hendrix', 
    '1524 Haight St', 
    '1942-11-27', 
    'senior', 
    'purple_haze69', 
    'inactive'
  );

  --insert a "jim morrison" senior member, with status 'suspended'
  insert into 
  members (
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
    status
  )
values
  (
    4, 
    'lizard_king', 
    'pw9999', 
    'jmorrison@email.com', 
    'Jim', 
    'Morrison', 
    '8021 Rothdell Trail', 
    '1943-12-08', 
    'senior', 
    'lizard_king01', 
    'suspended'
  );

  
  --insert a "Matt Bellamy" senior member, with status 'active'
  insert into 
  members (
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
    status
  )
values
  (
    5, 
    'starlight', 
    'pw9999', 
    'mbellamy@email.com', 
    'Matt', 
    'Bellamy', 
    '10250 Constellation Blvd', 
    '1978-06-09', 
    'senior', 
    'starlight78', 
    'active'
  );

    --insert a "Lana Del Rey" junior member, with status active
  insert into 
  members (
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
    status
  )
values
  (
    6, 
    'deadly_nightshade', 
    'pw9999', 
    'ldr@email.com', 
    'Lana', 
    'Del Rey', 
    '3415 Coldwater Canyon Ave', 
    '1985-06-21', 
    'junior', 
    'deadly_nightshade7', 
    'active'
  );
--insert a "sharon den adel" senior member, with status 'active'
  insert into 
  members (
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
    status
  )
values
  (
    7, 
    'icequeen', 
    'pw9999', 
    'sda@email.com', 
    'Sharon', 
    'Den Adel', 
    '123 Dublin', 
    '1960-10-25', 
    'senior', 
    'icequeen77', 
    'active'
  );
  --insert a "axl rose" senior member, with status 'inactive'
  insert into 
  members (
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
    status
  )
values
  (
    8, 
    'novemberrain', 
    'pw9999', 
    'gnr@email.com', 
    'Axl', 
    'Rose', 
    '66 Drive', 
    '1962-02-06', 
    'senior', 
    'novemberrain 66', 
    'inactive'
  );
  