-- Active: 1731288670016@@127.0.0.1@3306@cosn
--insert a "private" system member

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
    1, 
    'admin', 
    'admin', 
    'admin@cosn.com', 
    'admin', 
    'admin', 
    'private', 
    '1999-12-31', 
    'administrator', 
    'admin', 
    'active'
  );

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
    2, 
    'private', 
    'pw', 
    'private@email.com', 
    'private', 
    'private', 
    'private', 
    '1999-12-31', 
    'administrator', 
    'private', 
    'active'
  );

--insert a "public" system member
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
    'public', 
    'pw', 
    'public@email.com', 
    'public', 
    'public', 
    'public', 
    '1999-12-31', 
    'administrator', 
    'public', 
    'active'
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
    4, 
    'purple_haze', 
    'purple_haze', 
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
    5, 
    'lizard_king', 
    'lizard_king', 
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
    6, 
    'starlight', 
    'starlight', 
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
    7, 
    'deadly_nightshade', 
    'deadly_nightshade', 
    'ldr@email.com', 
    'Lana', 
    'Del Rey', 
    '3415 Coldwater Canyon Ave', 
    '1985-06-21', 
    'junior', 
    'deadly_nightshade7', 
    'active'
  );
  