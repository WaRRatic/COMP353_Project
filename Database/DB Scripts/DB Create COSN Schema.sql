CREATE SCHEMA cosn;

CREATE  TABLE cosn.members ( 
	member_id            INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	username             VARCHAR(100)    NOT NULL   ,
	password             VARCHAR(50)    NOT NULL   ,
	email                VARCHAR(50)    NOT NULL   ,
	first_name           VARCHAR(100)    NOT NULL   ,
	last_name            VARCHAR(100)    NOT NULL   ,
	address              VARCHAR(100)       ,
	date_of_birth        DATE       ,
	privilege_level      ENUM('administrator','senior','junior')  DEFAULT 'junior'  NOT NULL   ,
	pseudonym            VARCHAR(50)       ,
	`status`             ENUM('active','inactive','suspended')  DEFAULT 'active'  NOT NULL   ,
	corporation_flag     BOOLEAN    NOT NULL   
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE  TABLE cosn.personal_info_permissions ( 
	personal_info_permission_id INT UNSIGNED   NOT NULL   PRIMARY KEY,
	owner_member_id      INT UNSIGNED   NOT NULL   ,
	personal_info_type   ENUM('first_name','last_name','date_of_birth','address','pseudonym','email')    NOT NULL   ,
	authorized_member_id INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_personal_info_visibility_members FOREIGN KEY ( owner_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_personal_info_visibility_members_0 FOREIGN KEY ( authorized_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_personal_info_visibility_member ON cosn.personal_info_permissions ( owner_member_id );

CREATE INDEX fk_personal_info_visibility_member_0 ON cosn.personal_info_permissions ( authorized_member_id );

CREATE  TABLE cosn.personal_info_public_permissions ( 
	personal_info_public_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	owner_member_id      INT UNSIGNED   NOT NULL   ,
	personal_info_type   ENUM('first_name','last_name','date_of_birth','address','pseudonym','email')       ,
	CONSTRAINT fk_personal_info_public_permissions_members FOREIGN KEY ( owner_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_personal_info_public_permissions_members ON cosn.personal_info_public_permissions ( owner_member_id );

CREATE  TABLE cosn.content ( 
	content_id           INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	creator_id           INT UNSIGNED   NOT NULL   ,
	content_type         ENUM('text','image','video')    NOT NULL   ,
	content_data         TEXT    NOT NULL   ,
	content_creation_date DATE  DEFAULT curdate()  NOT NULL   ,
	content_title        VARCHAR(100)       ,
	moderation_status    ENUM('pending', 'approved', 'rejected')  DEFAULT 'pending'     ,
	CONSTRAINT fk_content_members FOREIGN KEY ( creator_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_content_members ON cosn.content ( creator_id );

CREATE  TABLE cosn.content_comment ( 
	content_comment_id   INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	commenter_member_id  INT UNSIGNED      ,
	comment_text         VARCHAR(250)       ,
	target_content_id    INT UNSIGNED      ,
	datetime_comment     DATETIME       ,
	CONSTRAINT fk_content_comment_members FOREIGN KEY ( commenter_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_content_comment_content FOREIGN KEY ( target_content_id ) REFERENCES cosn.content( content_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE cosn.content_link_relationship ( 
	content_link_rel_id  INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	origin_content_id    INT UNSIGNED      ,
	target_content_id    INT UNSIGNED      ,
	CONSTRAINT fk_content_link_relationship_content_1 FOREIGN KEY ( origin_content_id ) REFERENCES cosn.content( content_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_content_link_relationship_content_2 FOREIGN KEY ( target_content_id ) REFERENCES cosn.content( content_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_content_link_relationship_content ON cosn.content_link_relationship ( origin_content_id );

CREATE INDEX fk_content_link_relationship_content_0 ON cosn.content_link_relationship ( target_content_id );

CREATE  TABLE cosn.content_member_permission ( 
	content_member_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED      ,
	authorized_member_id INT UNSIGNED      ,
	content_permission_type ENUM('read','edit','comment','share','modify-permission','moderate','link')       ,
	CONSTRAINT fk_content_permissions_members FOREIGN KEY ( authorized_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_content_member_permission_content FOREIGN KEY ( target_content_id ) REFERENCES cosn.content( content_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_content_permissions_content ON cosn.content_member_permission ( target_content_id );

CREATE INDEX fk_content_permissions_members ON cosn.content_member_permission ( authorized_member_id );

CREATE  TABLE cosn.content_moderation_warning ( 
	content_moderation_warning_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED   NOT NULL   ,
	owner_member_id      INT UNSIGNED   NOT NULL   ,
	moderator_member_id  INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_content_moderation_warning_content FOREIGN KEY ( target_content_id ) REFERENCES cosn.content( content_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_content_moderation_warning_content_0 FOREIGN KEY ( owner_member_id ) REFERENCES cosn.content( creator_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_content_moderation_warning_members FOREIGN KEY ( moderator_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_content_moderation_warning_content ON cosn.content_moderation_warning ( target_content_id );

CREATE INDEX fk_content_moderation_warning_content_0 ON cosn.content_moderation_warning ( owner_member_id );

CREATE INDEX fk_content_moderation_warning_members ON cosn.content_moderation_warning ( moderator_member_id );

CREATE  TABLE cosn.content_public_permissions ( 
	content_public_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED      ,
	content_public_permission_type ENUM('read','comment','share','link')       ,
	CONSTRAINT fk_content_public_permissions_content FOREIGN KEY ( target_content_id ) REFERENCES cosn.content( content_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_content_public_permissions_content ON cosn.content_public_permissions ( target_content_id );

CREATE  TABLE cosn.gift_registry ( 
	gift_registry_id     INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	organizer_member_id  INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_gift_registry_members FOREIGN KEY ( organizer_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_gift_registry_members ON cosn.gift_registry ( organizer_member_id );

CREATE  TABLE cosn.gift_registry_ideas ( 
	gift_registry_ideas_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_gift_registry_id INT UNSIGNED   NOT NULL   ,
	idea_owner_id        INT UNSIGNED   NOT NULL   ,
	gift_idea_description VARCHAR(200)    NOT NULL   ,
	CONSTRAINT fk_gift_registry_ideas_gift_registry_0 FOREIGN KEY ( target_gift_registry_id ) REFERENCES cosn.gift_registry( gift_registry_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_ideas_members FOREIGN KEY ( idea_owner_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_gift_registry_ideas_gift_registry ON cosn.gift_registry_ideas ( target_gift_registry_id );

CREATE INDEX fk_gift_registry_ideas_gift_registry_participants ON cosn.gift_registry_ideas ( idea_owner_id );

CREATE  TABLE cosn.gift_registry_participants ( 
	gift_registry_participants_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	participant_member_id INT UNSIGNED   NOT NULL   ,
	target_gift_registry_id INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_gift_registry_participants_members FOREIGN KEY ( participant_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_participants_gift_registry FOREIGN KEY ( target_gift_registry_id ) REFERENCES cosn.gift_registry( gift_registry_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_gift_registry_participants_gift_registry ON cosn.gift_registry_participants ( target_gift_registry_id );

CREATE INDEX fk_gift_registry_participants_members ON cosn.gift_registry_participants ( participant_member_id );

CREATE  TABLE cosn.groups ( 
	group_id             INT UNSIGNED   NOT NULL   PRIMARY KEY,
	group_name           VARCHAR(100)    NOT NULL   ,
	owner_id             INT UNSIGNED      ,
	description          TEXT       ,
	creation_date        DATE  DEFAULT current_timestamp()     ,
	cathegory            VARCHAR(100)       ,
	CONSTRAINT fk_groups_members FOREIGN KEY ( owner_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE INDEX fk_groups_members ON cosn.groups ( owner_id );

CREATE  TABLE cosn.member_messages ( 
	member_message_id    INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	origin_member_id     INT UNSIGNED   NOT NULL   ,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	message_content      TEXT       ,
	CONSTRAINT fk_member_messages_members FOREIGN KEY ( origin_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_member_messages_members_0 FOREIGN KEY ( target_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_member_messages_members ON cosn.member_messages ( origin_member_id );

CREATE INDEX fk_member_messages_members_0 ON cosn.member_messages ( target_member_id );

CREATE  TABLE cosn.member_privilege_change_request ( 
	member_privilege_change_request_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	requested_privilege_level ENUM('senior')       ,
	CONSTRAINT fk_member_privilege_change_request_members FOREIGN KEY ( target_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_member_privilege_change_request_members ON cosn.member_privilege_change_request ( target_member_id );

CREATE  TABLE cosn.member_relationships ( 
	relationship_id      INT UNSIGNED   NOT NULL   PRIMARY KEY,
	origin_member_id     INT UNSIGNED   NOT NULL   ,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	member_relationship_type ENUM('friend','family','colleague','blocked')    NOT NULL   ,
	member_relationship_status ENUM('requested','approved','rejected')       ,
	CONSTRAINT fk_member_relationships_members FOREIGN KEY ( origin_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_member_relationships_members_0 FOREIGN KEY ( target_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_member_relationships_members ON cosn.member_relationships ( origin_member_id );

CREATE INDEX fk_member_relationships_members_0 ON cosn.member_relationships ( target_member_id );

CREATE  TABLE cosn.content_group_permissions ( 
	content_group_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED      ,
	target_group_id      INT UNSIGNED   NOT NULL   ,
	content_group_permission_type ENUM('read','comment','share','link')       ,
	CONSTRAINT fk_content_group_permissions_content FOREIGN KEY ( target_content_id ) REFERENCES cosn.content( content_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_content_group_permissions_groups FOREIGN KEY ( target_group_id ) REFERENCES cosn.groups( group_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_content_group_permissions_content ON cosn.content_group_permissions ( target_content_id );

CREATE INDEX fk_content_group_permissions_groups ON cosn.content_group_permissions ( target_group_id );

CREATE  TABLE cosn.group_event ( 
	group_event_id       INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_id      INT UNSIGNED   NOT NULL   ,
	event_organizer_member_id INT UNSIGNED   NOT NULL   ,
	event_name           VARCHAR(100)    NOT NULL   ,
	CONSTRAINT fk_group_event_groups FOREIGN KEY ( target_group_id ) REFERENCES cosn.groups( group_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_group_event_members FOREIGN KEY ( event_organizer_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_group_event_groups ON cosn.group_event ( target_group_id );

CREATE INDEX fk_group_event_members ON cosn.group_event ( event_organizer_member_id );

CREATE  TABLE cosn.group_event_options ( 
	group_event_options_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_event_id INT UNSIGNED   NOT NULL   ,
	option_owner_member_id INT UNSIGNED   NOT NULL   ,
	option_description   VARCHAR(100)    NOT NULL   ,
	CONSTRAINT fk_group_event_options_group_event FOREIGN KEY ( target_group_event_id ) REFERENCES cosn.group_event( group_event_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_group_event_options_members FOREIGN KEY ( option_owner_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_group_event_options_group_event ON cosn.group_event_options ( target_group_event_id );

CREATE INDEX fk_group_event_options_members ON cosn.group_event_options ( option_owner_member_id );

CREATE  TABLE cosn.group_members ( 
	group_membership_id  INT UNSIGNED   NOT NULL   PRIMARY KEY,
	participant_member_id INT UNSIGNED   NOT NULL   ,
	joined_group_id      INT UNSIGNED   NOT NULL   ,
	date_joined          DATE    NOT NULL   ,
	role_of_member       ENUM('member','owner')  DEFAULT 'member'     ,
	CONSTRAINT fk_group_members_members FOREIGN KEY ( participant_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_group_members_groups FOREIGN KEY ( joined_group_id ) REFERENCES cosn.groups( group_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_group_members_members ON cosn.group_members ( participant_member_id );

CREATE INDEX fk_group_members_groups ON cosn.group_members ( joined_group_id );

CREATE  TABLE cosn.group_vote_plebiscite ( 
	group_vote_plebiscite_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_member_id     INT UNSIGNED      ,
	organizer_member_id  INT UNSIGNED      ,
	target_group_id      INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_group_vote_plebiscite_groups FOREIGN KEY ( target_group_id ) REFERENCES cosn.groups( group_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_group_vote_plebiscite_members FOREIGN KEY ( target_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_group_vote_plebiscite_members_0 FOREIGN KEY ( organizer_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_group_vote_plebiscite_groups ON cosn.group_vote_plebiscite ( target_group_id );

CREATE INDEX fk_group_vote_plebiscite_members ON cosn.group_vote_plebiscite ( target_member_id );

CREATE INDEX fk_group_vote_plebiscite_members_0 ON cosn.group_vote_plebiscite ( organizer_member_id );

CREATE  TABLE cosn.group_vote_plebiscite_results ( 
	group_vote_plebiscite_results_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_vote_plebiscite_id INT UNSIGNED   NOT NULL   ,
	voter_member_id      INT UNSIGNED   NOT NULL   ,
	voting_decision      BOOLEAN    NOT NULL   ,
	CONSTRAINT fk_group_vote_plebiscite_results_members FOREIGN KEY ( voter_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_group_vote_plebiscite_results_group_vote_plebiscite FOREIGN KEY ( target_group_vote_plebiscite_id ) REFERENCES cosn.group_vote_plebiscite( group_vote_plebiscite_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_group_vote_plebiscite_results_members ON cosn.group_vote_plebiscite_results ( voter_member_id );

CREATE INDEX fk_group_vote_plebiscite_results_group_vote_plebiscite ON cosn.group_vote_plebiscite_results ( target_group_vote_plebiscite_id );

CREATE  TABLE cosn.group_event_option_vote ( 
	group_event_option_vote_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_event_option_id INT UNSIGNED   NOT NULL   ,
	option_voter_member_id INT UNSIGNED   NOT NULL   ,
	option_voting_decision BOOLEAN       ,
	CONSTRAINT fk_group_event_option_vote_group_event_options FOREIGN KEY ( target_group_event_option_id ) REFERENCES cosn.group_event_options( group_event_options_id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_group_event_option_vote_members FOREIGN KEY ( option_voter_member_id ) REFERENCES cosn.members( member_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_group_event_option_vote_group_event_options ON cosn.group_event_option_vote ( target_group_event_option_id );

CREATE INDEX fk_group_event_option_vote_members ON cosn.group_event_option_vote ( option_voter_member_id );

ALTER TABLE cosn.members COMMENT 'contains the info for every member of COSN';

ALTER TABLE cosn.members MODIFY member_id INT UNSIGNED NOT NULL  AUTO_INCREMENT  COMMENT 'member_id = 1 is "private" system member
member_id = 2 is "public" system member';

ALTER TABLE cosn.members MODIFY password VARCHAR(50)  NOT NULL   COMMENT 'password of the user

Mandatory (not null), for enabling the correct system functionality.';

ALTER TABLE cosn.members MODIFY email VARCHAR(50)  NOT NULL   COMMENT 'email of the user

Mandatory (not null), for identity verification purpose.';

ALTER TABLE cosn.members MODIFY first_name VARCHAR(100)  NOT NULL   COMMENT 'Mandatory (not null), for identity verification purpose.';

ALTER TABLE cosn.members MODIFY last_name VARCHAR(100)  NOT NULL   COMMENT 'Mandatory (not null), for identity verification purpose.';

ALTER TABLE cosn.members MODIFY address VARCHAR(100)     COMMENT 'address of the user.
Mandatory (not null), for identity verification purpose.';

ALTER TABLE cosn.members MODIFY date_of_birth DATE     COMMENT 'date of birth of the user.
Mandatory (not null), for identity verification purpose.';

ALTER TABLE cosn.members MODIFY privilege_level ENUM('administrator','senior','junior')  NOT NULL DEFAULT 'junior'  COMMENT 'privilege status of a group member (administrator, senior, junior)

Mandatory (not null), for enabling the correct system functionality.';

ALTER TABLE cosn.members MODIFY pseudonym VARCHAR(50)     COMMENT 'name for internal interactions';

ALTER TABLE cosn.members MODIFY `status` ENUM('active','inactive','suspended')  NOT NULL DEFAULT 'active'  COMMENT 'the ''system'' status is used for internal backend representation of "public" and "private" members';

ALTER TABLE cosn.members MODIFY corporation_flag BOOLEAN  NOT NULL   COMMENT 'Defines whether the member is a corporation (corporation_flag = true) or an actual person (corporation_flag = false).';

ALTER TABLE cosn.personal_info_permissions COMMENT 'Contains the mapping for ''member-specific'' permissions (visibility) of private information.

Allows a particular piece of personal information, for a particular member, to be rendered visible to a particular set of other members. The set of other members can be constructed by the applicaton in the "shape" of a family, friends or groups, by simply inserting the needed member_id to the authorized_member_id.';

ALTER TABLE cosn.personal_info_permissions MODIFY owner_member_id INT UNSIGNED NOT NULL   COMMENT 'the membeer_id of the owner of this personal information';

ALTER TABLE cosn.personal_info_permissions MODIFY authorized_member_id INT UNSIGNED NOT NULL   COMMENT 'the member_id that is authorized to view this particular personal information, of a particular member

authorized_member_id = 1, it is visible to the "private" system member
authorized_member_id = 2, it is visible to the "public" system member';

ALTER TABLE cosn.content COMMENT 'contains the content created by members';

ALTER TABLE cosn.content MODIFY creator_id INT UNSIGNED NOT NULL   COMMENT 'member who created this particular piece of content';

ALTER TABLE cosn.content MODIFY content_type ENUM('text','image','video')  NOT NULL   COMMENT 'what kind of content was produced';

ALTER TABLE cosn.content MODIFY content_data TEXT  NOT NULL   COMMENT 'text or URL link to the data of the content';

ALTER TABLE cosn.content MODIFY content_creation_date DATE  NOT NULL DEFAULT curdate()  COMMENT 'when was the content created';

ALTER TABLE cosn.content MODIFY content_title VARCHAR(100)     COMMENT 'title of the post of the content';

ALTER TABLE cosn.content MODIFY moderation_status ENUM('pending', 'approved', 'rejected')   DEFAULT 'pending'  COMMENT 'status of the piece of content in terms of moderation';

ALTER TABLE cosn.content_comment COMMENT 'table containing comments on content from members';

ALTER TABLE cosn.content_comment MODIFY commenter_member_id INT UNSIGNED    COMMENT 'the member who made the content';

ALTER TABLE cosn.content_comment MODIFY comment_text VARCHAR(250)     COMMENT 'the actual text of the comment on a certain piece of content';

ALTER TABLE cosn.content_comment MODIFY target_content_id INT UNSIGNED    COMMENT 'the content which is the target of the comment';

ALTER TABLE cosn.content_comment MODIFY datetime_comment DATETIME     COMMENT 'the datetime of when the comment was created';

ALTER TABLE cosn.content_link_relationship COMMENT 'Describes the way that content s linked between each other such as a comment to a post';

ALTER TABLE cosn.content_link_relationship MODIFY origin_content_id INT UNSIGNED    COMMENT 'ContendID of the piece of content to which another piece of content is linked';

ALTER TABLE cosn.content_link_relationship MODIFY target_content_id INT UNSIGNED    COMMENT 'Identifies the piece of content that is linked to the origin contentID';

ALTER TABLE cosn.content_member_permission COMMENT 'allows for setting the granular permissions to a particular piece of content, targeted at a particular member';

ALTER TABLE cosn.content_member_permission MODIFY target_content_id INT UNSIGNED    COMMENT 'the specific piece of conent, defined by content_id on which a particular member has a certain permission';

ALTER TABLE cosn.content_member_permission MODIFY authorized_member_id INT UNSIGNED    COMMENT 'which member_id has a specific permission to do something with a specific content_id

authorized_member_id = 1 is "private" system member
authorized_member_id = 2 is "public" system member';

ALTER TABLE cosn.content_member_permission MODIFY content_permission_type ENUM('read','edit','comment','share','modify-permission','moderate','link')     COMMENT 'the type of permission that the authorized_member_id has on this particular piece of content
can be
''read'',''edit'',''comment'',''share'',''modify-permission'',''moderate'',''link''';

ALTER TABLE cosn.content_moderation_warning COMMENT 'Contains the moderation warning for content posted by a member (if the content was "flagged" by a moderator';

ALTER TABLE cosn.content_moderation_warning MODIFY target_content_id INT UNSIGNED NOT NULL   COMMENT 'defines the particular piece of content that has been flagged by a moderator';

ALTER TABLE cosn.content_moderation_warning MODIFY owner_member_id INT UNSIGNED NOT NULL   COMMENT 'member responsible for posting a flagged content';

ALTER TABLE cosn.content_moderation_warning MODIFY moderator_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a moderator who flagged the particular piece of content';

ALTER TABLE cosn.content_public_permissions COMMENT 'Defines which content is public';

ALTER TABLE cosn.content_public_permissions MODIFY content_public_permission_id INT UNSIGNED NOT NULL  AUTO_INCREMENT  COMMENT 'The synthetic PK (surrogate key)';

ALTER TABLE cosn.content_public_permissions MODIFY target_content_id INT UNSIGNED    COMMENT 'the specific piece of conent, defined by content_id on which the public has certain permission';

ALTER TABLE cosn.content_public_permissions MODIFY content_public_permission_type ENUM('read','comment','share','link')     COMMENT 'the type of permission that the public has on this particular piece of content
can be
''read'',''comment'',''share'',''link''';

ALTER TABLE cosn.gift_registry COMMENT 'Describes gift registry entity that different members can attach gift ideas to.';

ALTER TABLE cosn.gift_registry MODIFY organizer_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a particular gift registry organizer';

ALTER TABLE cosn.gift_registry_ideas COMMENT 'describes gift ideas for a particular registry';

ALTER TABLE cosn.gift_registry_ideas MODIFY target_gift_registry_id INT UNSIGNED NOT NULL   COMMENT 'describes which particular gift registry a gift idea applies to';

ALTER TABLE cosn.gift_registry_ideas MODIFY idea_owner_id INT UNSIGNED NOT NULL   COMMENT 'ID of a member proposing a particullar gift';

ALTER TABLE cosn.gift_registry_ideas MODIFY gift_idea_description VARCHAR(200)  NOT NULL   COMMENT 'description of a proposed gift';

ALTER TABLE cosn.gift_registry_participants COMMENT 'Contains the participants of a particular gift registry';

ALTER TABLE cosn.gift_registry_participants MODIFY participant_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a participant of the registry';

ALTER TABLE cosn.gift_registry_participants MODIFY target_gift_registry_id INT UNSIGNED NOT NULL   COMMENT 'describes a particular gift registry that a member is part of';

ALTER TABLE cosn.groups COMMENT 'Contains the information about the groups, such as their description, who created them, etc';

ALTER TABLE cosn.groups MODIFY owner_id INT UNSIGNED    COMMENT 'ID of the member who created a particular group';

ALTER TABLE cosn.groups MODIFY description TEXT     COMMENT 'Description of the group, their interests, etc';

ALTER TABLE cosn.groups MODIFY creation_date DATE   DEFAULT current_timestamp()  COMMENT 'Date when group was created';

ALTER TABLE cosn.groups MODIFY cathegory VARCHAR(100)     COMMENT 'defines the different cathegories';

ALTER TABLE cosn.member_messages COMMENT 'table containing the messages that members send between each others';

ALTER TABLE cosn.member_messages MODIFY origin_member_id INT UNSIGNED NOT NULL   COMMENT 'the member_id from who the message is sent FROM';

ALTER TABLE cosn.member_messages MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'member_id that receives the message';

ALTER TABLE cosn.member_messages MODIFY message_content TEXT     COMMENT 'the actual content of the message';

ALTER TABLE cosn.member_privilege_change_request COMMENT 'Contains the privilege change request of a member, for example - a junior to senior member';

ALTER TABLE cosn.member_privilege_change_request MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'The ID of the member who is requesting the change';

ALTER TABLE cosn.member_privilege_change_request MODIFY requested_privilege_level ENUM('senior')     COMMENT 'desired privilege level for the tardet_member_id';

ALTER TABLE cosn.member_relationships COMMENT 'describes the relationships of the members, specifically if they are friends, family, colleagues or blocked';

ALTER TABLE cosn.member_relationships MODIFY origin_member_id INT UNSIGNED NOT NULL   COMMENT 'the member from whom the relationship originates';

ALTER TABLE cosn.member_relationships MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'the member to whom the origin_member is connected';

ALTER TABLE cosn.member_relationships MODIFY member_relationship_type ENUM('friend','family','colleague','blocked')  NOT NULL   COMMENT 'The type of relationship can be ''friend, ''family'', ''colleague'' or ''blocked''';

ALTER TABLE cosn.member_relationships MODIFY member_relationship_status ENUM('requested','approved','rejected')     COMMENT 'used to represent the evolution of the relationship from, specifically from a friend/family/colleage request to an actual confirmed relationship';

ALTER TABLE cosn.content_group_permissions COMMENT 'table describing which groups can access to what content';

ALTER TABLE cosn.content_group_permissions MODIFY target_content_id INT UNSIGNED    COMMENT 'the specific piece of conent, defined by content_id on which a particular group has certain permission';

ALTER TABLE cosn.content_group_permissions MODIFY target_group_id INT UNSIGNED NOT NULL   COMMENT 'the particular group which has a certain permission on a specific content';

ALTER TABLE cosn.content_group_permissions MODIFY content_group_permission_type ENUM('read','comment','share','link')     COMMENT 'the type of permission that a particular group has on a certain piece of content';

ALTER TABLE cosn.group_event COMMENT 'contains the events organized for particular groups';

ALTER TABLE cosn.group_event MODIFY target_group_id INT UNSIGNED NOT NULL   COMMENT 'Group for which a particular event is organized';

ALTER TABLE cosn.group_event MODIFY event_organizer_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a member who is organizing a particular event';

ALTER TABLE cosn.group_event MODIFY event_name VARCHAR(100)  NOT NULL   COMMENT 'Name of the particular event being organized';

ALTER TABLE cosn.group_event_options COMMENT 'describes the proposed options for a particular group event';

ALTER TABLE cosn.group_event_options MODIFY target_group_event_id INT UNSIGNED NOT NULL   COMMENT 'describes a particular group event ID on which a particular time/place/date option applies';

ALTER TABLE cosn.group_event_options MODIFY option_owner_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of the member proposing a particular option of date/time/place for the event';

ALTER TABLE cosn.group_event_options MODIFY option_description VARCHAR(100)  NOT NULL   COMMENT 'describes the details of the proposed option for date/time/place
Ex: "Alaska, 2025-01-01, 07:00"';

ALTER TABLE cosn.group_members COMMENT 'Mapping between members and groups, each row telling us which member belongs to which group';

ALTER TABLE cosn.group_members MODIFY participant_member_id INT UNSIGNED NOT NULL   COMMENT 'the member_id of the participant of this group';

ALTER TABLE cosn.group_members MODIFY joined_group_id INT UNSIGNED NOT NULL   COMMENT 'the ID of a particular group, that a particular member has joined';

ALTER TABLE cosn.group_members MODIFY date_joined DATE  NOT NULL   COMMENT 'the date when a particular member has joned a particular group';

ALTER TABLE cosn.group_members MODIFY role_of_member ENUM('member','owner')   DEFAULT 'member'  COMMENT 'the role of a particular member who joined a particular group, can be either ''owner'' or ''member''';

ALTER TABLE cosn.group_vote_plebiscite COMMENT 'Contains the plebiscite organized to oust a non-person (corporate) member.';

ALTER TABLE cosn.group_vote_plebiscite MODIFY target_member_id INT UNSIGNED    COMMENT 'ID of a member being ousted';

ALTER TABLE cosn.group_vote_plebiscite MODIFY organizer_member_id INT UNSIGNED    COMMENT 'ID of the organizer of the plebiscite';

ALTER TABLE cosn.group_vote_plebiscite MODIFY target_group_id INT UNSIGNED NOT NULL   COMMENT 'Group from which the non-person member is being ousted.';

ALTER TABLE cosn.group_vote_plebiscite_results COMMENT 'Results of the plebiscite';

ALTER TABLE cosn.group_vote_plebiscite_results MODIFY target_group_vote_plebiscite_id INT UNSIGNED NOT NULL   COMMENT 'ID of the plebiscite.';

ALTER TABLE cosn.group_vote_plebiscite_results MODIFY voter_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of the voting member on this particular plebiscite';

ALTER TABLE cosn.group_vote_plebiscite_results MODIFY voting_decision BOOLEAN  NOT NULL   COMMENT 'describes the decision of a particular member in the plebiscite vote (voting decision = true, meaning in favour of ousting)';

ALTER TABLE cosn.group_event_option_vote COMMENT 'contains the voting results of a particular member for a particular event option proposed';

ALTER TABLE cosn.group_event_option_vote MODIFY target_group_event_option_id INT UNSIGNED NOT NULL   COMMENT 'ID of the option of proposed date/time/place for a particular event';

ALTER TABLE cosn.group_event_option_vote MODIFY option_voter_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of the member who is voting on a particular option';

ALTER TABLE cosn.group_event_option_vote MODIFY option_voting_decision BOOLEAN     COMMENT 'describes the decision of a particular member regarding the proposed date/time/place for an event (true = support for the option)';

