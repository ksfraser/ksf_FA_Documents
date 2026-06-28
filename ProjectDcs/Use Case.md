# Use Cases - ksf_FA_Documents

## Document Information
- **Module**: ksf_FA_Documents
- **Version**: 2.0.0
- **Date**: 2026-06-28
- **Status**: Updated

---

## 1. Use Cases

### UC-FA-DOC-001: Access Documents

**Actor**: FA User (any authenticated user with DOC_VIEW or higher)

**Preconditions**:
- User is logged into FrontAccounting
- User has at least DOC_VIEW permission

**Steps**:
1. User navigates to Documents module
2. System displays document list filtered by ACL (user sees own documents + group documents + any documents they have DOC_VIEW_ALL for)
3. User can search/filter the list
4. User clicks a document to view details
5. System verifies ACL (user is owner, in same group, or has DOC_VIEW_ALL)
6. System displays document metadata, attachments, entity links, version history

**Postconditions**:
- Document list displayed with ACL filtering
- View access logged

**Alternate Flows**:
- If user has no DOC_VIEW/DOC_VIEW_ALL, empty list is shown
- If ACL check fails, "Access Denied" message displayed

**Cross-Reference**: FR-DM-003, FR-DM-010, BR-DOC-001, BR-DOC-004

---

### UC-FA-DOC-002: Upload Document

**Actor**: HR Admin / Document Author (user with DOC_UPLOAD or DOC_MANAGE)

**Preconditions**:
- User is logged into FrontAccounting
- User has DOC_UPLOAD or DOC_MANAGE permission

**Steps**:
1. User navigates to Documents > Upload
2. User selects file and enters metadata (title, type, expiry date, acknowledgment flag)
3. System auto-sets owner = current user, group_id = current user's group
4. User optionally links document to entities (customers, projects, tasks, employees)
5. User optionally uploads attachments via ksf_FA_Attachments
6. User submits form
7. System validates input, creates document record in fa_documents
8. System creates entity link records in fa_document_links
9. System processes attachment through AttachmentOperationsTrait -> fa_attachments
10. System logs the upload

**Postconditions**:
- Document created with metadata
- Entity links saved
- Attachments stored in fa_attachments
- Audit log entry created

**Alternate Flows**:
- Invalid file type: error message, document not created
- Missing required fields: validation error displayed
- ksf_FA_Attachments unavailable: attachment upload fails gracefully, document still created

**Cross-Reference**: FR-DM-001, FR-DM-008, FR-DM-011, BR-DOC-001, BR-DOC-003, BR-DOC-005

---

### UC-FA-DOC-003: Link Document to Multiple Entities (NEW)

**Actor**: Document Editor / Admin (user with DOC_MANAGE or DOC_ADMIN)

**Preconditions**:
- Document exists
- Target entities exist in their respective modules
- User has edit access to the document (owner, group member with DOC_MANAGE, or admin)

**Steps**:
1. User opens document view page
2. User clicks "Link Entities" or "Edit Links"
3. System displays current entity links
4. User selects entity type from dropdown (customer, project, task, employee, training_course, quote, supplier)
5. User searches/picks the specific entity by name or ID
6. User clicks "Add Link"
7. System validates combination is not a duplicate
8. System inserts record into fa_document_links
9. User repeats steps 4-8 for additional entities
10. User clicks "Save" or "Done"
11. System persists all changes

**Postconditions**:
- fa_document_links records created for each new link
- Link activity logged to audit trail

**Alternate Flows**:
- Duplicate link attempt: system shows warning, ignores duplicate
- Entity not found: system shows error, does not create link
- Unlink: user clicks remove icon next to existing link, system deletes fa_document_links record

**Cross-Reference**: FR-DM-008, FR-DM-009, BR-DOC-003

---

### UC-FA-DOC-004: Set Document Owner/Group Permissions (NEW)

**Actor**: Document Administrator (user with DOC_ADMIN)

**Preconditions**:
- Document exists
- User has DOC_ADMIN permission

**Steps**:
1. Admin opens document view page
2. Admin clicks "Permissions" or "Edit ACL"
3. System displays current owner (name) and group (name)
4. Admin searches for and selects a new owner from FA user list
5. Admin selects a new group from FA security group list
6. Admin clicks "Save"
7. System validates that selected owner and group exist
8. System updates fa_documents.owner and fa_documents.group_id
9. System logs the permission change with old and new values

**Postconditions**:
- Document owner changed
- Document group changed
- Audit log records the change

**Alternate Flows**:
- Non-admin user tries to access: "Access Denied" error
- Owner or group not found: validation error, no update performed

**Cross-Reference**: FR-DM-010, BR-DOC-004

---

### UC-FA-DOC-005: Upload Attachment via Shared Attachment Service (NEW)

**Actor**: Document Editor (user with DOC_MANAGE or edit access through ACL)

**Preconditions**:
- Document exists
- User has edit access (owner, group member with DOC_MANAGE, or admin)
- ksf_FA_Attachments module is installed and active

**Steps**:
1. User opens document view page
2. User clicks "Attachments" tab or section
3. System displays current attachments (merged from fa_attachments and legacy fa_document_attachments)
4. User clicks "Upload Attachment"
5. User selects a file from local machine
6. User optionally enters a description
7. User clicks "Upload"
8. System validates file type and size
9. System calls AttachmentOperationsTrait::uploadAttachment()
10. ksf_FA_Attachments stores the file and creates a record in fa_attachments with source_type='document', source_id=document.id
11. System refreshes the attachment list

**Postconditions**:
- File stored in ksf_FA_Attachments managed directory
- Record created in fa_attachments table
- Attachment visible in document's attachment list
- File can be downloaded from document view

**Alternate Flows**:
- Invalid file type: error message, no record created
- File exceeds size limit: error message, no record created
- ksf_FA_Attachments not available: error message, upload blocked
- Legacy attachment viewing: old attachments from fa_document_attachments show in list but are read-only

**Cross-Reference**: FR-DM-011, FR-DM-012, FR-DM-013, BR-DOC-005

---

## 2. Use Case Summary

| UC ID | Name | Actor | Priority | FR IDs |
|-------|------|-------|----------|--------|
| UC-FA-DOC-001 | Access Documents | FA User | High | FR-DM-003, FR-DM-010 |
| UC-FA-DOC-002 | Upload Document | HR Admin | High | FR-DM-001, FR-DM-008, FR-DM-011 |
| UC-FA-DOC-003 | Link Document to Multiple Entities | Document Editor | High | FR-DM-008, FR-DM-009 |
| UC-FA-DOC-004 | Set Document Owner/Group Permissions | Doc Admin | High | FR-DM-010 |
| UC-FA-DOC-005 | Upload Attachment via Shared Service | Document Editor | High | FR-DM-011, FR-DM-012, FR-DM-013 |

---

*Document Version: 2.0.0*
*Last Updated: 2026-06-28*
