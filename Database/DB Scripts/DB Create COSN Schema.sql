CREATE SCHEMA kpc353_2;

CREATE  TABLE kpc353_2.members ( 
	member_id            INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	username             VARCHAR(100)    NOT NULL   ,
	password             VARCHAR(50)    NOT NULL   ,
	email                VARCHAR(50)    NOT NULL   ,
	first_name           VARCHAR(100)    NOT NULL   ,
	last_name            VARCHAR(100)    NOT NULL   ,
	address              VARCHAR(100)       ,
	date_of_birth        DATE       ,
	privilege_level      ENUM('administrator','senior','junior')  DEFAULT 'junior'  NOT NULL   ,
	`status`             ENUM('active','inactive','suspended')  DEFAULT 'active'  NOT NULL   ,
	corporation_flag     BOOLEAN  DEFAULT false  NOT NULL   
 );

CREATE  TABLE kpc353_2.personal_info_permissions ( 
	personal_info_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	owner_member_id      INT UNSIGNED   NOT NULL   ,
	personal_info_type   ENUM('first_name','last_name','date_of_birth','address','email')    NOT NULL   ,
	authorized_member_id INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_personal_info_visibility_members FOREIGN KEY ( owner_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_personal_info_visibility_members_0 FOREIGN KEY ( authorized_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_personal_info_visibility_member ON kpc353_2.personal_info_permissions ( owner_member_id );

CREATE INDEX fk_personal_info_visibility_member_0 ON kpc353_2.personal_info_permissions ( authorized_member_id );

CREATE  TABLE kpc353_2.personal_info_public_permissions ( 
	personal_info_public_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	owner_member_id      INT UNSIGNED   NOT NULL   ,
	personal_info_type   ENUM('first_name','last_name','date_of_birth','address','email')       ,
	CONSTRAINT fk_personal_info_public_permissions_members FOREIGN KEY ( owner_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_personal_info_public_permissions_members ON kpc353_2.personal_info_public_permissions ( owner_member_id );

CREATE  TABLE kpc353_2.content ( 
	content_id           INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	creator_id           INT UNSIGNED   NOT NULL   ,
	content_type         ENUM('text','image','video')    NOT NULL   ,
	content_data         TEXT    NOT NULL   ,
	content_creation_date DATE  DEFAULT curdate()  NOT NULL   ,
	content_title        VARCHAR(100)       ,
	moderation_status    ENUM('pending', 'approved', 'rejected')  DEFAULT 'pending'     ,
	content_deleted_flag BOOLEAN  DEFAULT false  NOT NULL   ,
	CONSTRAINT fk_content_members FOREIGN KEY ( creator_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_content_members ON kpc353_2.content ( creator_id );

CREATE  TABLE kpc353_2.content_comment ( 
	content_comment_id   INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	commenter_member_id  INT UNSIGNED   NOT NULL   ,
	comment_text         VARCHAR(250)       ,
	target_content_id    INT UNSIGNED   NOT NULL   ,
	datetime_comment     DATETIME       ,
	CONSTRAINT fk_content_comment_members FOREIGN KEY ( commenter_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_content_comment_content FOREIGN KEY ( target_content_id ) REFERENCES kpc353_2.content( content_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE  TABLE kpc353_2.content_link_relationship ( 
	content_link_rel_id  INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	origin_content_id    INT UNSIGNED   NOT NULL   ,
	target_content_id    INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_content_link_relationship_content_1 FOREIGN KEY ( origin_content_id ) REFERENCES kpc353_2.content( content_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_content_link_relationship_content_2 FOREIGN KEY ( target_content_id ) REFERENCES kpc353_2.content( content_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_content_link_relationship_content ON kpc353_2.content_link_relationship ( origin_content_id );

CREATE INDEX fk_content_link_relationship_content_0 ON kpc353_2.content_link_relationship ( target_content_id );

CREATE  TABLE kpc353_2.content_member_permission ( 
	content_member_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED   NOT NULL   ,
	authorized_member_id INT UNSIGNED   NOT NULL   ,
	content_permission_type ENUM('read','edit','comment','share','modify-permission','moderate','link')       ,
	CONSTRAINT fk_content_permissions_members FOREIGN KEY ( authorized_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_content_member_permission_content FOREIGN KEY ( target_content_id ) REFERENCES kpc353_2.content( content_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_content_permissions_content ON kpc353_2.content_member_permission ( target_content_id );

CREATE INDEX fk_content_permissions_members ON kpc353_2.content_member_permission ( authorized_member_id );

CREATE  TABLE kpc353_2.content_moderation_warning ( 
	content_moderation_warning_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED   NOT NULL   ,
	owner_member_id      INT UNSIGNED   NOT NULL   ,
	moderator_member_id  INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_content_moderation_warning_content FOREIGN KEY ( target_content_id ) REFERENCES kpc353_2.content( content_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_content_moderation_warning_content_0 FOREIGN KEY ( owner_member_id ) REFERENCES kpc353_2.content( creator_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_content_moderation_warning_members FOREIGN KEY ( moderator_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_content_moderation_warning_content ON kpc353_2.content_moderation_warning ( target_content_id );

CREATE INDEX fk_content_moderation_warning_content_0 ON kpc353_2.content_moderation_warning ( owner_member_id );

CREATE INDEX fk_content_moderation_warning_members ON kpc353_2.content_moderation_warning ( moderator_member_id );

CREATE  TABLE kpc353_2.content_public_permissions ( 
	content_public_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED   NOT NULL   ,
	content_public_permission_type ENUM('read','comment','share','link')       ,
	CONSTRAINT fk_content_public_permissions_content FOREIGN KEY ( target_content_id ) REFERENCES kpc353_2.content( content_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_content_public_permissions_content ON kpc353_2.content_public_permissions ( target_content_id );

CREATE  TABLE kpc353_2.gift_registry ( 
	gift_registry_id     INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	organizer_member_id  INT UNSIGNED   NOT NULL   ,
	gift_registry_description VARCHAR(100)       ,
	gift_registry_name   VARCHAR(100)       ,
	CONSTRAINT fk_gift_registry_members FOREIGN KEY ( organizer_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_gift_registry_members ON kpc353_2.gift_registry ( organizer_member_id );

CREATE  TABLE kpc353_2.gift_registry_ideas ( 
	gift_registry_ideas_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_gift_registry_id INT UNSIGNED   NOT NULL   ,
	idea_owner_id        INT UNSIGNED   NOT NULL   ,
	gift_idea_description VARCHAR(200)    NOT NULL   ,
	CONSTRAINT fk_gift_registry_ideas_gift_registry_0 FOREIGN KEY ( target_gift_registry_id ) REFERENCES kpc353_2.gift_registry( gift_registry_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_ideas_members FOREIGN KEY ( idea_owner_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_gift_registry_ideas_gift_registry ON kpc353_2.gift_registry_ideas ( target_gift_registry_id );

CREATE INDEX fk_gift_registry_ideas_gift_registry_participants ON kpc353_2.gift_registry_ideas ( idea_owner_id );

CREATE  TABLE kpc353_2.gift_registry_participants ( 
	gift_registry_participants_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	participant_member_id INT UNSIGNED   NOT NULL   ,
	target_gift_registry_id INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_gift_registry_participants_members FOREIGN KEY ( participant_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_participants_gift_registry FOREIGN KEY ( target_gift_registry_id ) REFERENCES kpc353_2.gift_registry( gift_registry_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_gift_registry_participants_gift_registry ON kpc353_2.gift_registry_participants ( target_gift_registry_id );

CREATE INDEX fk_gift_registry_participants_members ON kpc353_2.gift_registry_participants ( participant_member_id );

CREATE  TABLE kpc353_2.gift_registry_permissions ( 
	gift_registry_permissions_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_gift_registry_id INT UNSIGNED   NOT NULL   ,
	authorized_member_id INT UNSIGNED   NOT NULL   ,
	gift_registry_permission_type ENUM('view', 'edit', 'add-item')       ,
	CONSTRAINT fk_gift_registry_permissions_gift_registry FOREIGN KEY ( target_gift_registry_id ) REFERENCES kpc353_2.gift_registry( gift_registry_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_permissions_members FOREIGN KEY ( authorized_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE  TABLE kpc353_2.groups ( 
	group_id             INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	group_name           VARCHAR(100)    NOT NULL   ,
	owner_id             INT UNSIGNED   NOT NULL   ,
	description          TEXT       ,
	creation_date        DATE  DEFAULT current_timestamp()     ,
	category             VARCHAR(100)       ,
	CONSTRAINT fk_groups_members FOREIGN KEY ( owner_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_groups_members ON kpc353_2.groups ( owner_id );

CREATE  TABLE kpc353_2.member_messages ( 
	member_message_id    INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	origin_member_id     INT UNSIGNED   NOT NULL   ,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	message_content      TEXT       ,
	message_datetime     DATETIME  DEFAULT CURRENT_TIMESTAMP     ,
	CONSTRAINT fk_member_messages_members FOREIGN KEY ( origin_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_member_messages_members_0 FOREIGN KEY ( target_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_member_messages_members ON kpc353_2.member_messages ( origin_member_id );

CREATE INDEX fk_member_messages_members_0 ON kpc353_2.member_messages ( target_member_id );

CREATE  TABLE kpc353_2.member_privilege_change_request ( 
	member_privilege_change_request_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	requested_privilege_level ENUM('senior')       ,
	CONSTRAINT fk_member_privilege_change_request_members FOREIGN KEY ( target_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_member_privilege_change_request_members ON kpc353_2.member_privilege_change_request ( target_member_id );

CREATE  TABLE kpc353_2.member_relationships ( 
	relationship_id      INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	origin_member_id     INT UNSIGNED   NOT NULL   ,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	member_relationship_type ENUM('friend','family','colleague','blocked')    NOT NULL   ,
	member_relationship_status ENUM('requested','approved','rejected','blocked')       ,
	CONSTRAINT fk_member_relationships_members FOREIGN KEY ( origin_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_member_relationships_members_0 FOREIGN KEY ( target_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_member_relationships_members ON kpc353_2.member_relationships ( origin_member_id );

CREATE INDEX fk_member_relationships_members_0 ON kpc353_2.member_relationships ( target_member_id );

CREATE  TABLE kpc353_2.content_group_permissions ( 
	content_group_permission_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_content_id    INT UNSIGNED   NOT NULL   ,
	target_group_id      INT UNSIGNED   NOT NULL   ,
	content_group_permission_type ENUM('read','comment','share','link')       ,
	CONSTRAINT fk_content_group_permissions_content FOREIGN KEY ( target_content_id ) REFERENCES kpc353_2.content( content_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_content_group_permissions_groups FOREIGN KEY ( target_group_id ) REFERENCES kpc353_2.groups( group_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_content_group_permissions_content ON kpc353_2.content_group_permissions ( target_content_id );

CREATE INDEX fk_content_group_permissions_groups ON kpc353_2.content_group_permissions ( target_group_id );

CREATE  TABLE kpc353_2.gift_registry_gifts ( 
	gift_id              INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_gift_registry_id INT UNSIGNED   NOT NULL   ,
	gift_registry_idea_id INT UNSIGNED   NOT NULL   ,
	sender_member_id     INT UNSIGNED   NOT NULL   ,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	gift_status          ENUM('pending','sent','received')  DEFAULT 'pending'  NOT NULL   ,
	gift_date            DATETIME  DEFAULT CURRENT_TIMESTAMP  NOT NULL   ,
	CONSTRAINT fk_gift_registry_gifts_gift_registry FOREIGN KEY ( target_gift_registry_id ) REFERENCES kpc353_2.gift_registry( gift_registry_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_gifts_gift_registry_ideas FOREIGN KEY ( gift_registry_idea_id ) REFERENCES kpc353_2.gift_registry_ideas( gift_registry_ideas_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_gifts_members FOREIGN KEY ( sender_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_gift_registry_gifts_members_0 FOREIGN KEY ( target_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE  TABLE kpc353_2.group_event ( 
	group_event_id       INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_id      INT UNSIGNED   NOT NULL   ,
	event_organizer_member_id INT UNSIGNED   NOT NULL   ,
	event_name           VARCHAR(100)    NOT NULL   ,
	CONSTRAINT fk_group_event_groups FOREIGN KEY ( target_group_id ) REFERENCES kpc353_2.groups( group_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_group_event_members FOREIGN KEY ( event_organizer_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_group_event_groups ON kpc353_2.group_event ( target_group_id );

CREATE INDEX fk_group_event_members ON kpc353_2.group_event ( event_organizer_member_id );

CREATE  TABLE kpc353_2.group_event_options ( 
	group_event_options_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_event_id INT UNSIGNED   NOT NULL   ,
	option_owner_member_id INT UNSIGNED   NOT NULL   ,
	option_description   VARCHAR(100)    NOT NULL   ,
	CONSTRAINT fk_group_event_options_group_event FOREIGN KEY ( target_group_event_id ) REFERENCES kpc353_2.group_event( group_event_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_group_event_options_members FOREIGN KEY ( option_owner_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_group_event_options_group_event ON kpc353_2.group_event_options ( target_group_event_id );

CREATE INDEX fk_group_event_options_members ON kpc353_2.group_event_options ( option_owner_member_id );

CREATE  TABLE kpc353_2.group_members ( 
	group_membership_id  INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	participant_member_id INT UNSIGNED   NOT NULL   ,
	joined_group_id      INT UNSIGNED   NOT NULL   ,
	date_joined          DATE  DEFAULT CURRENT_DATE  NOT NULL   ,
	group_member_status  ENUM('member','admin','requested','ban')  DEFAULT 'member'     ,
	CONSTRAINT fk_group_members_members FOREIGN KEY ( participant_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_group_members_groups FOREIGN KEY ( joined_group_id ) REFERENCES kpc353_2.groups( group_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_group_members_members ON kpc353_2.group_members ( participant_member_id );

CREATE INDEX fk_group_members_groups ON kpc353_2.group_members ( joined_group_id );

CREATE  TABLE kpc353_2.group_vote_plebiscite ( 
	group_vote_plebiscite_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_member_id     INT UNSIGNED   NOT NULL   ,
	organizer_member_id  INT UNSIGNED   NOT NULL   ,
	target_group_id      INT UNSIGNED   NOT NULL   ,
	CONSTRAINT fk_group_vote_plebiscite_groups FOREIGN KEY ( target_group_id ) REFERENCES kpc353_2.groups( group_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_group_vote_plebiscite_members FOREIGN KEY ( target_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_group_vote_plebiscite_members_0 FOREIGN KEY ( organizer_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_group_vote_plebiscite_groups ON kpc353_2.group_vote_plebiscite ( target_group_id );

CREATE INDEX fk_group_vote_plebiscite_members ON kpc353_2.group_vote_plebiscite ( target_member_id );

CREATE INDEX fk_group_vote_plebiscite_members_0 ON kpc353_2.group_vote_plebiscite ( organizer_member_id );

CREATE  TABLE kpc353_2.group_vote_plebiscite_results ( 
	group_vote_plebiscite_results_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_vote_plebiscite_id INT UNSIGNED   NOT NULL   ,
	voter_member_id      INT UNSIGNED   NOT NULL   ,
	voting_decision      BOOLEAN    NOT NULL   ,
	CONSTRAINT fk_group_vote_plebiscite_results_members FOREIGN KEY ( voter_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_group_vote_plebiscite_results_group_vote_plebiscite FOREIGN KEY ( target_group_vote_plebiscite_id ) REFERENCES kpc353_2.group_vote_plebiscite( group_vote_plebiscite_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_group_vote_plebiscite_results_members ON kpc353_2.group_vote_plebiscite_results ( voter_member_id );

CREATE INDEX fk_group_vote_plebiscite_results_group_vote_plebiscite ON kpc353_2.group_vote_plebiscite_results ( target_group_vote_plebiscite_id );

CREATE  TABLE kpc353_2.group_event_option_vote ( 
	group_event_option_vote_id INT UNSIGNED   NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	target_group_event_option_id INT UNSIGNED   NOT NULL   ,
	option_voter_member_id INT UNSIGNED   NOT NULL   ,
	option_voting_decision BOOLEAN       ,
	CONSTRAINT fk_group_event_option_vote_group_event_options FOREIGN KEY ( target_group_event_option_id ) REFERENCES kpc353_2.group_event_options( group_event_options_id ) ON DELETE CASCADE ON UPDATE NO ACTION,
	CONSTRAINT fk_group_event_option_vote_members FOREIGN KEY ( option_voter_member_id ) REFERENCES kpc353_2.members( member_id ) ON DELETE CASCADE ON UPDATE NO ACTION
 );

CREATE INDEX fk_group_event_option_vote_group_event_options ON kpc353_2.group_event_option_vote ( target_group_event_option_id );

CREATE INDEX fk_group_event_option_vote_members ON kpc353_2.group_event_option_vote ( option_voter_member_id );

ALTER TABLE kpc353_2.members COMMENT 'contains the info for every member of COSN';

ALTER TABLE kpc353_2.members MODIFY member_id INT UNSIGNED NOT NULL  AUTO_INCREMENT  COMMENT 'member_id = 1 is "private" system member
member_id = 2 is "public" system member';

ALTER TABLE kpc353_2.members MODIFY username VARCHAR(100)  NOT NULL   COMMENT 'public facing user''s name (pseudonym)';

ALTER TABLE kpc353_2.members MODIFY password VARCHAR(50)  NOT NULL   COMMENT 'password of the user

Mandatory (not null), for enabling the correct system functionality.';

ALTER TABLE kpc353_2.members MODIFY email VARCHAR(50)  NOT NULL   COMMENT 'email of the user

Mandatory (not null), for identity verification purpose.';

ALTER TABLE kpc353_2.members MODIFY first_name VARCHAR(100)  NOT NULL   COMMENT 'Mandatory (not null), for identity verification purpose.';

ALTER TABLE kpc353_2.members MODIFY last_name VARCHAR(100)  NOT NULL   COMMENT 'Mandatory (not null), for identity verification purpose.';

ALTER TABLE kpc353_2.members MODIFY address VARCHAR(100)     COMMENT 'address of the user.
Mandatory (not null), for identity verification purpose.';

ALTER TABLE kpc353_2.members MODIFY date_of_birth DATE     COMMENT 'date of birth of the user.
Mandatory (not null), for identity verification purpose.';

ALTER TABLE kpc353_2.members MODIFY privilege_level ENUM('administrator','senior','junior')  NOT NULL DEFAULT 'junior'  COMMENT 'privilege status of a group member (administrator, senior, junior)

Mandatory (not null), for enabling the correct system functionality.';

ALTER TABLE kpc353_2.members MODIFY `status` ENUM('active','inactive','suspended')  NOT NULL DEFAULT 'active'  COMMENT 'the ''system'' status is used for internal backend representation of "public" and "private" members';

ALTER TABLE kpc353_2.members MODIFY corporation_flag BOOLEAN  NOT NULL DEFAULT false  COMMENT 'Defines whether the member is a corporation (corporation_flag = true) or an actual person (corporation_flag = false).';

ALTER TABLE kpc353_2.personal_info_permissions COMMENT 'Contains the mapping for ''member-specific'' permissions (visibility) of private information.

Allows a particular piece of personal information, for a particular member, to be rendered visible to a particular set of other members. The set of other members can be constructed by the applicaton in the "shape" of a family, friends or groups, by simply inserting the needed member_id to the authorized_member_id.';

ALTER TABLE kpc353_2.personal_info_permissions MODIFY owner_member_id INT UNSIGNED NOT NULL   COMMENT 'the membeer_id of the owner of this personal information';

ALTER TABLE kpc353_2.personal_info_permissions MODIFY authorized_member_id INT UNSIGNED NOT NULL   COMMENT 'the member_id that is authorized to view this particular personal information, of a particular member

authorized_member_id = 1, it is visible to the "private" system member
authorized_member_id = 2, it is visible to the "public" system member';

ALTER TABLE kpc353_2.content COMMENT 'contains the content created by members';

ALTER TABLE kpc353_2.content MODIFY creator_id INT UNSIGNED NOT NULL   COMMENT 'member who created this particular piece of content';

ALTER TABLE kpc353_2.content MODIFY content_type ENUM('text','image','video')  NOT NULL   COMMENT 'what kind of content was produced';

ALTER TABLE kpc353_2.content MODIFY content_data TEXT  NOT NULL   COMMENT 'text or URL link to the data of the content';

ALTER TABLE kpc353_2.content MODIFY content_creation_date DATE  NOT NULL DEFAULT curdate()  COMMENT 'when was the content created';

ALTER TABLE kpc353_2.content MODIFY content_title VARCHAR(100)     COMMENT 'title of the post of the content';

ALTER TABLE kpc353_2.content MODIFY moderation_status ENUM('pending', 'approved', 'rejected')   DEFAULT 'pending'  COMMENT 'status of the piece of content in terms of moderation';

ALTER TABLE kpc353_2.content MODIFY content_deleted_flag BOOLEAN  NOT NULL DEFAULT false  COMMENT 'used to "delete" content by users';

ALTER TABLE kpc353_2.content_comment COMMENT 'table containing comments on content from members';

ALTER TABLE kpc353_2.content_comment MODIFY commenter_member_id INT UNSIGNED NOT NULL   COMMENT 'the member who made the content';

ALTER TABLE kpc353_2.content_comment MODIFY comment_text VARCHAR(250)     COMMENT 'the actual text of the comment on a certain piece of content';

ALTER TABLE kpc353_2.content_comment MODIFY target_content_id INT UNSIGNED NOT NULL   COMMENT 'the content which is the target of the comment';

ALTER TABLE kpc353_2.content_comment MODIFY datetime_comment DATETIME     COMMENT 'the datetime of when the comment was created';

ALTER TABLE kpc353_2.content_link_relationship COMMENT 'Describes the way that content s linked between each other such as a comment to a post';

ALTER TABLE kpc353_2.content_link_relationship MODIFY origin_content_id INT UNSIGNED NOT NULL   COMMENT 'ContendID of the piece of content to which another piece of content is linked';

ALTER TABLE kpc353_2.content_link_relationship MODIFY target_content_id INT UNSIGNED NOT NULL   COMMENT 'Identifies the piece of content that is linked to the origin contentID';

ALTER TABLE kpc353_2.content_member_permission COMMENT 'allows for setting the granular permissions to a particular piece of content, targeted at a particular member';

ALTER TABLE kpc353_2.content_member_permission MODIFY target_content_id INT UNSIGNED NOT NULL   COMMENT 'the specific piece of conent, defined by content_id on which a particular member has a certain permission';

ALTER TABLE kpc353_2.content_member_permission MODIFY authorized_member_id INT UNSIGNED NOT NULL   COMMENT 'which member_id has a specific permission to do something with a specific content_id

authorized_member_id = 1 is "private" system member
authorized_member_id = 2 is "public" system member';

ALTER TABLE kpc353_2.content_member_permission MODIFY content_permission_type ENUM('read','edit','comment','share','modify-permission','moderate','link')     COMMENT 'the type of permission that the authorized_member_id has on this particular piece of content
can be
''read'',''edit'',''comment'',''share'',''modify-permission'',''moderate'',''link''';

ALTER TABLE kpc353_2.content_moderation_warning COMMENT 'Contains the moderation warning for content posted by a member (if the content was "flagged" by a moderator';

ALTER TABLE kpc353_2.content_moderation_warning MODIFY target_content_id INT UNSIGNED NOT NULL   COMMENT 'defines the particular piece of content that has been flagged by a moderator';

ALTER TABLE kpc353_2.content_moderation_warning MODIFY owner_member_id INT UNSIGNED NOT NULL   COMMENT 'member responsible for posting a flagged content';

ALTER TABLE kpc353_2.content_moderation_warning MODIFY moderator_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a moderator who flagged the particular piece of content';

ALTER TABLE kpc353_2.content_public_permissions COMMENT 'Defines which content is public';

ALTER TABLE kpc353_2.content_public_permissions MODIFY content_public_permission_id INT UNSIGNED NOT NULL  AUTO_INCREMENT  COMMENT 'The synthetic PK (surrogate key)';

ALTER TABLE kpc353_2.content_public_permissions MODIFY target_content_id INT UNSIGNED NOT NULL   COMMENT 'the specific piece of conent, defined by content_id on which the public has certain permission';

ALTER TABLE kpc353_2.content_public_permissions MODIFY content_public_permission_type ENUM('read','comment','share','link')     COMMENT 'the type of permission that the public has on this particular piece of content
can be
''read'',''comment'',''share'',''link''';

ALTER TABLE kpc353_2.gift_registry COMMENT 'Describes gift registry entity that different members can attach gift ideas to.';

ALTER TABLE kpc353_2.gift_registry MODIFY organizer_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a particular gift registry organizer';

ALTER TABLE kpc353_2.gift_registry MODIFY gift_registry_description VARCHAR(100)     COMMENT 'Description for the particular gift registry';

ALTER TABLE kpc353_2.gift_registry MODIFY gift_registry_name VARCHAR(100)     COMMENT 'name of the particular registry';

ALTER TABLE kpc353_2.gift_registry_ideas COMMENT 'describes gift ideas for a particular registry';

ALTER TABLE kpc353_2.gift_registry_ideas MODIFY target_gift_registry_id INT UNSIGNED NOT NULL   COMMENT 'describes which particular gift registry a gift idea applies to';

ALTER TABLE kpc353_2.gift_registry_ideas MODIFY idea_owner_id INT UNSIGNED NOT NULL   COMMENT 'ID of a member proposing a particullar gift';

ALTER TABLE kpc353_2.gift_registry_ideas MODIFY gift_idea_description VARCHAR(200)  NOT NULL   COMMENT 'description of a proposed gift';

ALTER TABLE kpc353_2.gift_registry_participants COMMENT 'Contains the participants of a particular gift registry';

ALTER TABLE kpc353_2.gift_registry_participants MODIFY participant_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a participant of the registry';

ALTER TABLE kpc353_2.gift_registry_participants MODIFY target_gift_registry_id INT UNSIGNED NOT NULL   COMMENT 'describes a particular gift registry that a member is part of';

ALTER TABLE kpc353_2.gift_registry_permissions COMMENT 'contains the permissions that members have on a gift registry';

ALTER TABLE kpc353_2.gift_registry_permissions MODIFY target_gift_registry_id INT UNSIGNED NOT NULL   COMMENT 'The target ID of the gift registry on which a particular members pemission is defined';

ALTER TABLE kpc353_2.gift_registry_permissions MODIFY authorized_member_id INT UNSIGNED NOT NULL   COMMENT 'the ID of the member that has a certain permission on a certain gift registry';

ALTER TABLE kpc353_2.gift_registry_permissions MODIFY gift_registry_permission_type ENUM('view', 'edit', 'add-item')     COMMENT 'the type of permission that a certain member has on a certain gift registry

can be ''view'', ''edit'', ''add-item''';

ALTER TABLE kpc353_2.groups COMMENT 'Contains the information about the groups, such as their description, who created them, etc';

ALTER TABLE kpc353_2.groups MODIFY owner_id INT UNSIGNED NOT NULL   COMMENT 'ID of the member who created a particular group';

ALTER TABLE kpc353_2.groups MODIFY description TEXT     COMMENT 'Description of the group, their interests, etc';

ALTER TABLE kpc353_2.groups MODIFY creation_date DATE   DEFAULT current_timestamp()  COMMENT 'Date when group was created';

ALTER TABLE kpc353_2.groups MODIFY category VARCHAR(100)     COMMENT 'defines the different categories';

ALTER TABLE kpc353_2.member_messages COMMENT 'table containing the messages that members send between each others';

ALTER TABLE kpc353_2.member_messages MODIFY origin_member_id INT UNSIGNED NOT NULL   COMMENT 'the member_id from who the message is sent FROM';

ALTER TABLE kpc353_2.member_messages MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'member_id that receives the message';

ALTER TABLE kpc353_2.member_messages MODIFY message_content TEXT     COMMENT 'the actual content of the message';

ALTER TABLE kpc353_2.member_messages MODIFY message_datetime DATETIME   DEFAULT CURRENT_TIMESTAMP  COMMENT 'the datetime of when the message was sent out';

ALTER TABLE kpc353_2.member_privilege_change_request COMMENT 'Contains the privilege change request of a member, for example - a junior to senior member';

ALTER TABLE kpc353_2.member_privilege_change_request MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'The ID of the member who is requesting the change';

ALTER TABLE kpc353_2.member_privilege_change_request MODIFY requested_privilege_level ENUM('senior')     COMMENT 'desired privilege level for the tardet_member_id';

ALTER TABLE kpc353_2.member_relationships COMMENT 'describes the relationships of the members, specifically if they are friends, family, colleagues or blocked';

ALTER TABLE kpc353_2.member_relationships MODIFY origin_member_id INT UNSIGNED NOT NULL   COMMENT 'the member from whom the relationship originates, for example the origin_member_id has REQUESTED a target_member_id to be friends';

ALTER TABLE kpc353_2.member_relationships MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'the member to whom the origin_member is connected';

ALTER TABLE kpc353_2.member_relationships MODIFY member_relationship_type ENUM('friend','family','colleague','blocked')  NOT NULL   COMMENT 'The type of relationship can be ''friend, ''family'', ''colleague'' or ''blocked''';

ALTER TABLE kpc353_2.member_relationships MODIFY member_relationship_status ENUM('requested','approved','rejected','blocked')     COMMENT 'used to represent the evolution of the relationship from, specifically from a friend/family/colleage request to an actual confirmed relationship';

ALTER TABLE kpc353_2.content_group_permissions COMMENT 'table describing which groups can access to what content';

ALTER TABLE kpc353_2.content_group_permissions MODIFY target_content_id INT UNSIGNED NOT NULL   COMMENT 'the specific piece of conent, defined by content_id on which a particular group has certain permission';

ALTER TABLE kpc353_2.content_group_permissions MODIFY target_group_id INT UNSIGNED NOT NULL   COMMENT 'the particular group which has a certain permission on a specific content';

ALTER TABLE kpc353_2.content_group_permissions MODIFY content_group_permission_type ENUM('read','comment','share','link')     COMMENT 'the type of permission that a particular group has on a certain piece of content';

ALTER TABLE kpc353_2.gift_registry_gifts COMMENT 'contains gifts sent from gift registry';

ALTER TABLE kpc353_2.gift_registry_gifts MODIFY target_gift_registry_id INT UNSIGNED NOT NULL   COMMENT 'the gift registry from which the gift is taken';

ALTER TABLE kpc353_2.gift_registry_gifts MODIFY gift_registry_idea_id INT UNSIGNED NOT NULL   COMMENT 'target gift idea from a specific gift registry';

ALTER TABLE kpc353_2.gift_registry_gifts MODIFY sender_member_id INT UNSIGNED NOT NULL   COMMENT 'the member who is sending the gift';

ALTER TABLE kpc353_2.gift_registry_gifts MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'the member who will receive the gift';

ALTER TABLE kpc353_2.gift_registry_gifts MODIFY gift_status ENUM('pending','sent','received')  NOT NULL DEFAULT 'pending'  COMMENT 'the status of the gift transaction';

ALTER TABLE kpc353_2.gift_registry_gifts MODIFY gift_date DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP  COMMENT 'the datetime when the gift was sent out';

ALTER TABLE kpc353_2.group_event COMMENT 'contains the events organized for particular groups';

ALTER TABLE kpc353_2.group_event MODIFY target_group_id INT UNSIGNED NOT NULL   COMMENT 'Group for which a particular event is organized';

ALTER TABLE kpc353_2.group_event MODIFY event_organizer_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a member who is organizing a particular event';

ALTER TABLE kpc353_2.group_event MODIFY event_name VARCHAR(100)  NOT NULL   COMMENT 'Name of the particular event being organized';

ALTER TABLE kpc353_2.group_event_options COMMENT 'describes the proposed options for a particular group event';

ALTER TABLE kpc353_2.group_event_options MODIFY target_group_event_id INT UNSIGNED NOT NULL   COMMENT 'describes a particular group event ID on which a particular time/place/date option applies';

ALTER TABLE kpc353_2.group_event_options MODIFY option_owner_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of the member proposing a particular option of date/time/place for the event';

ALTER TABLE kpc353_2.group_event_options MODIFY option_description VARCHAR(100)  NOT NULL   COMMENT 'describes the details of the proposed option for date/time/place
Ex: "Alaska, 2025-01-01, 07:00"';

ALTER TABLE kpc353_2.group_members COMMENT 'Mapping between members and groups, each row telling us which member belongs to which group';

ALTER TABLE kpc353_2.group_members MODIFY participant_member_id INT UNSIGNED NOT NULL   COMMENT 'the member_id of the participant of this group';

ALTER TABLE kpc353_2.group_members MODIFY joined_group_id INT UNSIGNED NOT NULL   COMMENT 'the ID of a particular group, that a particular member has joined';

ALTER TABLE kpc353_2.group_members MODIFY date_joined DATE  NOT NULL DEFAULT CURRENT_DATE  COMMENT 'the date when a particular member has joned a particular group';

ALTER TABLE kpc353_2.group_members MODIFY group_member_status ENUM('member','admin','requested','ban')   DEFAULT 'member'  COMMENT 'the status of a particular member in a particular group
can be either ''admin'', ''member'', ''requested, ''ban''';

ALTER TABLE kpc353_2.group_vote_plebiscite COMMENT 'Contains the plebiscite organized to oust a non-person (corporate) member.';

ALTER TABLE kpc353_2.group_vote_plebiscite MODIFY target_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of a member being ousted';

ALTER TABLE kpc353_2.group_vote_plebiscite MODIFY organizer_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of the organizer of the plebiscite';

ALTER TABLE kpc353_2.group_vote_plebiscite MODIFY target_group_id INT UNSIGNED NOT NULL   COMMENT 'Group from which the non-person member is being ousted.';

ALTER TABLE kpc353_2.group_vote_plebiscite_results COMMENT 'Results of the plebiscite';

ALTER TABLE kpc353_2.group_vote_plebiscite_results MODIFY target_group_vote_plebiscite_id INT UNSIGNED NOT NULL   COMMENT 'ID of the plebiscite.';

ALTER TABLE kpc353_2.group_vote_plebiscite_results MODIFY voter_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of the voting member on this particular plebiscite';

ALTER TABLE kpc353_2.group_vote_plebiscite_results MODIFY voting_decision BOOLEAN  NOT NULL   COMMENT 'describes the decision of a particular member in the plebiscite vote (voting decision = true, meaning in favour of ousting)';

ALTER TABLE kpc353_2.group_event_option_vote COMMENT 'contains the voting results of a particular member for a particular event option proposed';

ALTER TABLE kpc353_2.group_event_option_vote MODIFY target_group_event_option_id INT UNSIGNED NOT NULL   COMMENT 'ID of the option of proposed date/time/place for a particular event';

ALTER TABLE kpc353_2.group_event_option_vote MODIFY option_voter_member_id INT UNSIGNED NOT NULL   COMMENT 'ID of the member who is voting on a particular option';

ALTER TABLE kpc353_2.group_event_option_vote MODIFY option_voting_decision BOOLEAN     COMMENT 'describes the decision of a particular member regarding the proposed date/time/place for an event (true = support for the option)';

