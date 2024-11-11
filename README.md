# COMP353_Project -- Original focused problem statement
Your objective is to design a relational database system for a “realistic”
Private Online Community Social Network System (COSN) sketched out below.

Use MySQL Database Management System to develop the Private Online Community Social
Network System - COSN. One of the objectiveis to first flesh out the requirement of the system
bearing in mind the minimum as sketched below. The application would include a collection of
tables and services hosted by COSN. Hence COSN would enable members to access a local
community based server to share information and ideas. It provides services for people sharing
interests, activities, and backgrounds among themselves. The COSN system allow its members
to create a profile, to create a list of other members with whom to share contents, and to view
and add comments and contents - if enabled by the owner member of the web page. It also
allows members to interact among each other via self-contained messaging system. The
objective is the sharing of news, pictures, posts, activities, events, interests with members in the
community. Also, it allows members to create groups that share common interests or affiliations
and hold discussions in forums. 

Requirements Specification
You are required to develop a database system that will store at least the information about the
following entities and relationships:
-Details on members: ID, password, other personal information such as address, internal
interaction pseudonym etc. Members have family, friends and colleagues, privilege and status.
A member can specify what part of his/her personal information is public and what part is
accessible to which members of his/her group or is private.
A new person in the community can become a member by entering his details and validate it by
entering the required details such as the name and appropriate ID of an existing member or by
being introduced to the system by an existing member. Only public information is visible to
other non-members. Privilege can be either administrator, senior or junior. A member with an
administrator privilege has the full power on all services such as creation, deletion, editing of all
members and groups. The administrator could also post public items (accessible to the world). A
member can post new items and specify which of his groups can access the post and who in
each group can comment on it or add content to the post. An item could also be accessible to any
other members.

A member with a senior privilege can create groups and manipulate groups created by him/her.
The group is owned by the member who created it. Also, a member with a senior privilege can
add new members to the COSN. A member can add a list of members and specify them as family
members, friend members or colleague members. Status of a member can be either active,
inactive or suspended. An active member can have access to all the functionalities of a member.
An inactive member will not be visible to other members. A suspended member will not be able
to login to the COSN system until his/her status is changed.
All new members start by default as junior members. Only a member with an administrator
privilege can change the privilege of another member. The system by default has one member
with username admin and password admin created initially(Both of these must be changed after
the first login). Only members with administrator privileges can change the status of other
members to suspended or reset it to active or inactive. A member with junior privilege can edit
his/her profile, create a posting and communicate with other members. Also a junior member can
post to groups that he/she is a member of only. A junior member can request to become a senior
member. Each member can only have one profile including one email address.
When installed on a operational system with functioning email server, the system could send out
messages to indicate new contents to the members of the associated group. However, since there
is a restriction of sending emails by AITS (No email messages are allowed to be sent out of the
system), emails have to be simulated by a pop-up window and internal and sent email boxes.

Guidelines
Flesh out these guideline for you implementation.
-Details about groups: Records of information on each group, owner of the group, list of
members belonging to the group. Owner of the group can add new members or remove members
from the group. Members of a group can share a forum of information such as photos, videos and
posts. Adding any member to a group requires the knowledge of the persons email address 2
, first
name and DOB. For a business member, the DOB is the date of incorporation of the business.
-Each member has a home page which has an index of his/her contents as well as the contents
of each of the groups where he is an owner or member. The owner has a feature to view and
manage the permissions to any content; the permissions can be changed only by the owner.
-Detail on contents and the permissions: Each content added by a member can have a profile
which indicates who can do what with it. Content can be classified as view only, view and
comment or view and add or link to other contents.
- Non-person entities
, local businesses, local organization etc. could become members as long as
they behave in a civil manner. The person members could always organize a plebiscite of person
members to oust an non-person member and delete its contents.
-The administrators include the contents moderators. Each new contents would be reviewed by
a moderator before it could be posted. Any uncivil contents is black-listed (not posted) and the
member posting it is warned about the non-conformity of the posted contents. A ‘real person.’
member is suspended for a duration of time once the number of warnings exceeds 3. A business
person is fined after the second warning. If the number of suspensions or fines exceeds 3, the
member is suspended for at least a year.
- The membership is free for a ‘real-person’ whereas for a business member there is a fee based
on the number of postings made by it. 

The system should support at least the following functionalities through its interface:
1. Create/Delete/Edit/Display a member.
2. Create/Delete/Edit/Display a group.
3. Create/Delete/Edit/Display list of friends for a member.
4. Member request to be a friend of other member or join a group.
5. Member's ability to block another member or to be withdrawn from a group.
6. Member's ability to post texts, images or videos as well as to view posts by other members
and comment on them.
7. Members can either post or view posts of only groups that they belong to.
8. Member's main page shows the best and latest posts from their groups and friends.
9. Members can send a private message to their friends.
10. Report of groups or members by specific category such as interest, age, profession, region,
etc.
11. Ability to organize an event for the group by voting on date/time/place from a set posted and/
or alternates suggested by one of the group members
12. Registry and/or Gift exchange ideas among a family (secret Santa) or a group.

 
## Requirements Analysis

### Project Description
 - **(transaction)**: create member profile
 - **(transaction)**: create a list of other members with whom to share contact, and to view and add comments and contents (if enabled by the owner member of webpage) 
 - **(transaction)**: message other members (objective: share news, pictures, posts, activities, events, interests)
 - **(transaction)**: members can create groups for interests and affiliations

### Requirements Specifications
 - **(schema definition)**: Member entity: ID, password, "other personal information such as address, internal interaction pseudonim"
 - **(schema definition)**: Members have family, friends and colleagues, privilege and status
 - **(schema definition)**: Member can specify which part of personal information is public, private or accessible to which members of their group
 - **(transaction)**: A new person in the community can become a member by entering his details and validate it by
entering the required details such as the name and appropriate ID of an existing member or by
being introduced to the system by an existing member.
- **(schema definition)**: Privilege can be either administrator, senior or junior
- **(transaction)**: A member with an administrator privilege has the full power on all services such as creation, deletion, editing of all members and groups
- **(transaction)**: The administrator can post public items (accessible to the world). 
- **(transaction)**: A member can post new items and specify which of his groups can access the post and who in each group can comment on it or add content to the post. An item could also be accessible to any other members. 
- **(transaction)**: A member with a senior privilege can create groups and manipulate groups created by him/her.
- **(transaction)**: A member with a senior privilege can add new members to the COSN.
- **(schema definition)**: The group is owned by the member who created it. 
- **(transaction)**: A member can add a list of members and specify them as family members, friend members or colleague members.
- **(schema definition)**: Status of a member can be either active, inactive or suspended.
- **(transaction)**: An active member can have access to all the functionalities of a member.
- **(transaction)**: An inactive member will not be visible to other members.
- **(transaction)**: A suspended member will not be able to login to the COSN system until his/her status is changed. 
- **(transaction)**: All new members start by default as junior members.
- **(transaction)**: Only a member with an administrator privilege can change the privilege of another member. 
- **(transaction)**: The system by default has one member with username admin and password admin created initially(Both of these must be changed after the first login). 
- **(transaction)**: Only members with administrator privileges can change the status of other members to suspended or reset it to active or inactive.
- **(transaction)**: A junior member can edit his/her profile, create a posting and communicate with other members.
- **(transaction)**: A junior member can post to groups that he/she is a member of only
- **(transaction)**: A junior member can request to become a senior member
- **(transaction)**: Each member can only have one profile including one email address.

### Guidelines
- **(schema definition)**: Group details -- records of information on each group, owner of the group, list of members belonging to the group.  
- **(transaction)**: Owner of the group can add new members or remove members from the group. 
- **(transaction)**: Members of a group can share a forum of information such as photos, videos and posts.
- **(transaction)**: Adding any member to a group requires the knowledge of the persons email address, first name and DOB.
- **(schema definition)**: For a business member, the DOB is the date of incorporation of the business.
- **(functionality)**: Each member has a home page which has an index of his/her contents as well as the contents of each of the groups where he is an owner or member. 
- **(functionality)**: The owner has a feature to view and manage the permissions to any content; the permissions can be changed only by the owner.
- **(schema definition)**: Each content added by a member can have a profile which indicates who can do what with it
- **(schema definition)**:  Content can be classified as view only, view and comment or view and add or link to other contents. 
- **(schema definition)**: Non-person entities, local businesses, local organization etc. could become members 
- **(transaction)**: The person members could always organize a plebiscite of person members to oust an non-person member and delete its contents.
- **(schema definition)**: The administrators include the contents moderators.
- **(transaction)**: Moderation: Each new contents would be reviewed by a moderator before it could be posted. 
- **(transaction)**: Moderation: Any uncivil contents is black-listed (not posted) and the member posting it is warned about the non-conformity of the posted contents. 
- **(transaction)**: Moderation: A ‘real person.’ member is suspended for a duration of time once the number of warnings exceeds 3. A business person is fined after the second warning. If the number of suspensions or fines exceeds 3, the member is suspended for at least a year.
- **(transaction)**: The membership is free for a ‘real-person’ whereas for a business member there is a fee based on the number of postings made by it. 

### Minimum functionalities

1. Create/Delete/Edit/Display a member.
2. Create/Delete/Edit/Display a group.
3. Create/Delete/Edit/Display list of friends for a member.
4. Member request to be a friend of other member or join a group.
5. Member's ability to block another member or to be withdrawn from a group.
6. Member's ability to post texts, images or videos as well as to view posts by other members
and comment on them.
7. Members can either post or view posts of only groups that they belong to.
8. Member's main page shows the best and latest posts from their groups and friends.
9. Members can send a private message to their friends.
10. Report of groups or members by specific category such as interest, age, profession, region,
etc.
11. Ability to organize an event for the group by voting on date/time/place from a set posted and/
or alternates suggested by one of the group members
12. Registry and/or Gift exchange ideas among a family (secret Santa) or a group.


# Entity schema high-level layout



# Further analysis

> **Q:** Based on these requirements, is it clear whether a member can have friends and family members outside of a group, or they all must be part of a group to have a relationship?

**A:** Based on the requirements, a member in this COSN system can indeed have friends, family members, and colleagues listed individually and independently of groups. The ability to add members to a list as "family," "friends," or "colleagues" implies that these relationships are not strictly tied to group membership. Members can categorize others within these relationships, and while groups facilitate interactions among specific interests or affiliations, they are not a requirement for personal relationships outside of the group structure.

Thus, members can have friends and family outside of any group membership, though interactions within groups will be limited to members who are also part of that group.

>**Q:** Explain the difference between joining the COSN system (new member) vs joining a group with COSN (existing member)

**A:** 

-->Joining the COSN System (New Member):

For a new person to join COSN, they need an introduction from an existing member, which can be done by providing details (such as the name and ID) of a member who is already part of the COSN. This is essentially a form of invitation or sponsorship that allows the new person to complete the registration process and become a member of COSN.

-->Joining a Group within COSN (Existing Member):

Once someone is a COSN member, they can request to join specific groups within the platform. This process is managed independently by the group owner or administrator, who can approve or decline the request based on group rules or criteria.

In summary, being introduced to the system by an existing member applies only to new members joining COSN. Once someone is an existing COSN member, they can independently request to join various groups as described.

>**Q:** Explain the requirement on the visibility of the personal information "to which members of his/her group" -- does mean ability to render the personal information visible to only friends and family OR does it mean the the ability to render the personal information visible to only certain members of of certain group, based on the choice of the owner of the personal information?

**A:** The requirement about visibility of personal information "to which members of his/her group" means that each member can control the visibility of their personal information on a fine-grained, group-specific basis. Specifically, it allows the member to decide which parts of their personal information can be seen by members of certain groups they belong to. This goes beyond simply categorizing visibility to all "friends" or all "family" — instead, it allows for more selective sharing, where the member can choose:

Which specific groups can access each part of their personal information.
Who within those groups (such as certain members or roles) may have permission to view different parts of their profile.
For example, a member might allow their family group to view their home address but restrict access to it from their professional group. Alternatively, they might make their work history visible to colleagues in one specific project group while keeping it hidden from another group they belong to.

In short, the visibility control is designed to give members the flexibility to share different parts of their personal information with specific groups or individuals within those groups based on their preferences.