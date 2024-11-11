# COSN Database Schema and Transaction Organization

## Member

### Schema Definitions
- **Member Entity Attributes**:
  - ID
  - Password
  - Personal Information:
    - Address
    - Internal Interaction Pseudonym
    - Email Address (each member can only have one)
    - Date of Birth (for business members, this is the date of incorporation)
  - Relationships:
    - Family Members
    - Friends
    - Colleagues
  - Privilege Level:
    - Administrator
    - Senior
    - Junior
  - Status:
    - Active
    - Inactive
    - Suspended
  - Visibility Settings:
    - Specifies which parts of personal information are public, private, or accessible to specific group members

### Transaction Statements
- **Account Management**:
  - Create a new member profile by entering personal details and validating through an existing member's ID or introduction.
  - Edit personal profile information.
  - The system initializes with a default admin account (`username: admin`, `password: admin`), which must be changed after the first login.
- **Privileges and Status**:
  - All new members start as junior members by default.
  - A junior member can request an upgrade to senior member.
  - Only administrators can change the privilege level of other members.
  - Only administrators can change the status of members to active, inactive, or suspended.
  - Active members have full access to member functionalities.
  - Inactive members are not visible to other members.
  - Suspended members cannot log in until their status is changed.
- **Relationships**:
  - Add other members as family, friends, or colleagues.
- **Communication and Content**:
  - Junior members can:
    - Edit their profiles.
    - Create posts.
    - Communicate with other members.
    - Post to groups they belong to.
- **Non-Person Entities**:
  - Businesses and organizations can become members.
  - Membership for real persons is free; business members pay fees based on the number of posts.
- **Moderation and Discipline**:
  - Members receive warnings for uncivil content.
  - Real-person members are suspended after exceeding three warnings.
  - Business members are fined after the second warning and suspended for at least a year if they exceed three suspensions or fines.
- **Member Removal**:
  - Person members can organize a plebiscite to oust a non-person member and delete its content.

---

## Group

### Schema Definitions
- **Group Entity Attributes**:
  - Group ID
  - Group Name
  - Owner (Member who created the group)
  - List of Members
  - Group Description

### Transaction Statements
- **Group Management**:
  - Senior members can create new groups and manage existing ones they own.
  - Owners can add or remove members from their groups.
  - Adding a member to a group requires their email, first name, and date of birth.
- **Content Sharing**:
  - Members can share photos, videos, and posts within the group.
  - Members can post content to groups they belong to.
- **Permissions**:
  - Owners can set permissions on group content, specifying who can view, comment, or add content.

---

## Content/Post

### Schema Definitions
- **Content Attributes**:
  - Content ID
  - Creator (Member who added the content)
  - Associated Group(s)
  - Content Type (text, image, video)
  - Permissions Profile:
    - View Only
    - View and Comment
    - View and Add or Link to Other Content
  - Moderation Status (Pending, Approved, Black-listed)

### Transaction Statements
- **Content Creation and Sharing**:
  - Members can create posts and specify access permissions.
  - Administrators can post public items accessible to all.
- **Moderation**:
  - All new content is reviewed by moderators before posting.
  - Uncivil content is black-listed and not posted.
- **Interaction**:
  - Members can comment on and add to content if permissions allow.

---

## Privilege

### Schema Definitions
- **Privilege Levels**:
  - Administrator
  - Senior Member
  - Junior Member

### Transaction Statements
- **Privilege Management**:
  - Administrators have full control over all services, including creating, deleting, and editing members and groups.
  - Senior members can create and manage their own groups and add new members to the COSN.
  - Junior members can request to become senior members.
  - Only administrators can change members' privilege levels.

---

## Status

### Schema Definitions
- **Member Status Options**:
  - Active
  - Inactive
  - Suspended

### Transaction Statements
- **Status Management**:
  - Active members have full access to features.
  - Inactive members are hidden from other members.
  - Suspended members cannot log in.
  - Only administrators can change a member's status.

---

## Administrator

### Schema Definitions
- **Administrator Role**:
  - Includes content moderators.
  - Has the highest privilege level.

### Transaction Statements
- **Administrative Actions**:
  - Change privilege levels and statuses of members.
  - Create, delete, and edit any member or group.
  - Post public items accessible to everyone.
  - Manage system settings.

---

## Moderator

### Schema Definitions
- **Moderator Role**:
  - A subset of administrators focused on content moderation.

### Transaction Statements
- **Content Moderation**:
  - Review all new content before it is posted.
  - Black-list uncivil content.
  - Issue warnings to members for non-conforming content.
- **Disciplinary Actions**:
  - Suspend members after excessive warnings.
  - Fine business members for violations.

---

## Non-Person Entities

### Schema Definitions
- **Entity Attributes**:
  - Same as member attributes, with DOB as date of incorporation.
  - Designated as business members.

### Transaction Statements
- **Membership and Fees**:
  - Business members pay fees based on posting activity.
- **Discipline**:
  - Subject to fines and suspensions similar to real-person members.
- **Removal Process**:
  - Can be ousted through a plebiscite organized by person members.

---

## Friends, Family, Colleagues

### Schema Definitions
- **Relationship Types**:
  - Family Member
  - Friend
  - Colleague

### Transaction Statements
- **Relationship Management**:
  - Members can categorize their connections.
  - Can create and manage lists of friends, family, and colleagues.

---

## Permissions

### Schema Definitions
- **Permission Settings**:
  - Visibility of personal information (public, private, group-specific).
  - Content access levels (view, comment, add).

### Transaction Statements
- **Managing Permissions**:
  - Members specify access levels for their personal info and content.
  - Only content owners can change permissions.

---

## Home Page

### Schema Definitions
- **Home Page Features**:
  - Index of member's content.
  - Access to group content where the member is involved.

### Transaction Statements
- **Customization**:
  - Members can view and manage their home page.
- **Content Display**:
  - Shows best and latest posts from groups and friends.

---

## Messages

### Transaction Statements
- **Private Messaging**:
  - Members can send private messages to friends.
- **Communication**:
  - Share news, pictures, posts, activities, events, and interests.

---

## Warnings and Fines

### Schema Definitions
- **Disciplinary Records**:
  - Track number of warnings and fines per member.

### Transaction Statements
- **Enforcement Actions**:
  - Issue warnings for uncivil content.
  - Suspend real-person members after three warnings.
  - Fine business members after the second warning.
  - Suspend members for at least a year after exceeding suspension/fine limits.

---

## Content Permissions

### Schema Definitions
- **Permission Profiles for Content**:
  - View Only
  - View and Comment
  - View and Add or Link

### Transaction Statements
- **Setting Permissions**:
  - Content creators set access and interaction levels for each post.
  - Owners manage and change permissions as needed.
