# ksf_FA_Documents Functional Requirements

## Document Information
- **Module**: ksf_FA_Documents (Document Management)
- **Version**: 2.0.0
- **Date**: 2026-06-28
- **Status**: Updated
- **Author**: KSFII Development Team

## 1. Overview

### 1.1 Purpose
This document defines the functional requirements for the ksf_FA_Documents module, which provides document management capabilities for FrontAccounting, enabling storage, versioning, multi-entity linking, access control, tracking, and shared attachment management.

### 1.2 Scope
The Documents module provides:
- Document storage and retrieval
- Version control
- Multi-entity linking (links to customers, projects, tasks, employees, etc.)
- Access control (owner + group based ACL)
- Expiry date tracking and reminders
- Employee acknowledgment workflow
- Shared attachment service via ksf_FA_Attachments
- Integration with CRM, HRM, and task systems

---

## 2. Document Management

### 2.1 Document Upload (FR-DM-001)
**Requirement**: The system shall allow users to upload documents with metadata.

**Fields**:
- `title` - Document title
- `type` - Document type (Policy, Contract, Form, Training, Handbook, Other)
- `filename` - Uploaded filename
- `content` - Document content (stored or file path)
- `version` - Version string
- `status` - Status (Active, Archived, Superseded)
- `expiry_date` - Expiry date (optional)
- `requires_acknowledgment` - Flag for required acknowledgment
- `owner` - Document owner (integer, FK to fa_users, auto-set to current user)
- `group_id` - Security group (integer, FK to FA security groups, auto-set to current user's group)

**Priority**: High

**Cross-Reference**: BR-DOC-001, UC-FA-DOC-002

---

### 2.2 Document Types (FR-DM-002)
**Requirement**: The system shall support configurable document types.

**Default Types**:
- Policy - Company policies and procedures
- Contract - Employment contracts
- Form - Tax forms, benefits forms
- Training - Training materials and certifications
- Handbook - Employee handbooks
- Other - Miscellaneous documents

**Priority**: High

**Cross-Reference**: BR-DOC-001

---

### 2.3 Document Retrieval (FR-DM-003)
**Requirement**: The system shall allow users to search and retrieve documents.

**Features**:
- Search by title
- Filter by type
- Filter by status
- Filter by date range
- Filter by expiry status
- Filter by owner
- Filter by linked entity

**Priority**: High

**Cross-Reference**: BR-DOC-001

---

### 2.4 Document Status (FR-DM-004)
**Requirement**: The system shall track document status.

**Statuses**:
- Active - Current valid document
- Archived - Old version archived
- Superseded - Replaced by newer version

**Priority**: High

**Cross-Reference**: BR-DOC-001

---

### 2.5 Document Update (FR-DM-005)
**Requirement**: The system shall allow users to update document metadata.

**Features**:
- Update title, type, description
- Update expiry date
- Change owner (admin only)
- Change group_id (admin only)
- Update status
- Save revision history

**Priority**: High

**Cross-Reference**: BR-DOC-001, BR-DOC-004

---

### 2.6 Document Deletion (FR-DM-006)
**Requirement**: The system shall support soft-deletion (archival) of documents.

**Features**:
- Set status to Archived
- Preserve version history
- Preserve attachments
- Preserve entity links

**Priority**: Medium

**Cross-Reference**: BR-DOC-001

---

### 2.7 Note vs Document Distinction (FR-DM-007)
**Requirement**: The system shall distinguish between Notes (no versioning, no acknowledgment) and Documents (full workflow).

**Features**:
- Notes have simplified UI
- Notes skip version tracking
- Notes cannot require acknowledgment
- Notes can still link to entities

**Priority**: Medium

**Cross-Reference**: BR-DOC-008

---

## 3. Multi-Entity Linking

### 3.1 Link Document to Entities (FR-DM-008) (NEW)
**Requirement**: The system shall allow a document to be linked to multiple entities across different FA modules via the `fa_document_links` table.

**Features**:
- Link a document to any entity by type + id
- Supported entity types: customer, project, task, employee, training_course, quote, supplier
- Link many entities to one document
- Link one entity to many documents
- Prevent duplicate links (unique constraint on doc_id + entity_type + entity_id)
- Display linked entities on the document view page

**Priority**: High

**Cross-Reference**: BR-DOC-003, UC-FA-DOC-003

---

### 3.2 Unlink Document from Entity (FR-DM-009) (NEW)
**Requirement**: The system shall allow removal of a document-entity link without affecting the document or the entity.

**Features**:
- Remove a single link
- Remove all links from a document (bulk)
- Verify link exists before removal
- Cascade removal on document deletion

**Priority**: High

**Cross-Reference**: BR-DOC-003, UC-FA-DOC-003

---

## 4. Access Control

### 4.1 ACL Enforcement (FR-DM-010) (NEW)
**Requirement**: The system shall enforce document-level access control based on owner and group_id.

**Access Levels**:
| Level | View | Edit | Admin |
|-------|------|------|-------|
| Document Owner | Yes | Yes | No |
| Same Group | Yes (default) | With DOC_MANAGE | No |
| Other Users | With DOC_VIEW_ALL | No | No |
| Admin (DOC_ADMIN) | Yes | Yes | Yes |

**Features**:
- Owner is auto-set to current user on document creation
- Group is auto-set to current user's group on creation
- Admins can reassign owner and group_id
- All document queries are ACL-filtered
- ACL-denied operations raise DocumentsAclException

**Priority**: High

**Cross-Reference**: BR-DOC-004, UC-FA-DOC-004

---

## 5. Version Control

### 5.1 Version Creation (FR-VC-001)
**Requirement**: The system shall maintain version history for documents.

**Features**:
- Create new version on upload
- Store version metadata
- Link versions to document
- View version history

**Priority**: High

**Cross-Reference**: BR-DOC-002

---

### 5.2 Version Comparison (FR-VC-002)
**Requirement**: The system shall allow comparison between versions.

**Features**:
- List all versions
- Download any version
- View version date and author
- Version-level attachments via fa_attachments

**Priority**: Medium

**Cross-Reference**: BR-DOC-002

---

## 6. Expiry Tracking

### 6.1 Expiry Date Management (FR-ET-001)
**Requirement**: The system shall track document expiry dates.

**Features**:
- Set expiry date on upload
- Update expiry date on renewal
- Query expiring documents
- Calculate days until expiry

**Priority**: High

**Cross-Reference**: BR-DOC-007

---

### 6.2 Expiry Reminders (FR-ET-002)
**Requirement**: The system shall send expiry reminders.

**Features**:
- Configurable reminder intervals
- Email notifications
- Dashboard alerts
- Escalation rules

**Priority**: High

**Cross-Reference**: BR-DOC-007

---

## 7. Acknowledgment Workflow

### 7.1 Acknowledgment Assignment (FR-AW-001)
**Requirement**: The system shall assign acknowledgment tasks to employees.

**Features**:
- Assign to all employees
- Assign to specific employee groups
- Set acknowledgment deadline
- Track assignment status

**Priority**: High

**Cross-Reference**: BR-DOC-006

---

### 7.2 Acknowledgment Recording (FR-AW-002)
**Requirement**: The system shall record document acknowledgments.

**Features**:
- Employee acknowledges document
- Store acknowledgment timestamp
- Store acknowledgment IP address
- Generate acknowledgment report

**Priority**: High

**Cross-Reference**: BR-DOC-006

---

### 7.3 Acknowledgment Status (FR-AW-003)
**Requirement**: The system shall track acknowledgment status.

**Statuses**:
- Pending - Not yet acknowledged
- Acknowledged - Successfully acknowledged
- Expired - Acknowledgment deadline passed
- Waived - Acknowledgment requirement waived

**Priority**: High

**Cross-Reference**: BR-DOC-006

---

### 7.4 Pending Documents (FR-AW-004)
**Requirement**: The system shall display pending acknowledgments.

**Features**:
- Dashboard widget
- My Documents section
- Count of pending documents
- Due date display

**Priority**: High

**Cross-Reference**: BR-DOC-006

---

## 8. Attachment Management

### 8.1 Upload Attachment via Shared Service (FR-DM-011) (NEW)
**Requirement**: The system shall upload document attachments through the ksf_FA_Attachments shared service, storing records in the `fa_attachments` table with `source_type='document'`.

**Features**:
- File upload delegates to AttachmentOperationsTrait
- Record created in fa_attachments linked to document
- File stored in ksf_FA_Attachments managed directory
- MIME type detection
- File size validation
- Each document version can have its own attachments

**Priority**: High

**Cross-Reference**: BR-DOC-005, UC-FA-DOC-005

---

### 8.2 List Attachments from Shared Service (FR-DM-012) (NEW)
**Requirement**: The system shall list all attachments for a document, merging results from both the new `fa_attachments` table and the legacy `fa_document_attachments` table.

**Features**:
- Query fa_attachments WHERE source_type='document' AND source_id=?
- Query legacy fa_document_attachments WHERE document_id=?
- Merge and return combined list
- Display attachment metadata (filename, size, type, date, version)

**Priority**: High

**Cross-Reference**: BR-DOC-005

---

### 8.3 Legacy Attachment Backward Compatibility (FR-DM-013)
**Requirement**: The system shall maintain read-only access to attachments stored in the legacy `fa_document_attachments` table.

**Features**:
- Legacy attachments are readable
- Legacy attachments are not writable (new uploads go to fa_attachments)
- Legacy data is not migrated to fa_attachments
- Combined attachment list includes both sources

**Priority**: Medium

**Cross-Reference**: BR-DOC-005

---

## 9. Integration Features

### 9.1 CRM Integration (FR-IN-001)
**Requirement**: The system shall integrate with CRM module via entity links.

**Features**:
- Link documents to customers (via fa_document_links)
- Retrieve customer document history
- Customer document access via CRM profile

**Priority**: High

**Cross-Reference**: BR-DOC-003

---

### 9.2 HRM Integration (FR-IN-002)
**Requirement**: The system shall integrate with HRM module.

**Features**:
- Link documents to employee records (via fa_document_links)
- Retrieve employee document history
- Employee document access via HRM

**Priority**: High

**Cross-Reference**: BR-DOC-003

---

### 9.3 Task System Integration (FR-IN-003)
**Requirement**: The system shall integrate with task system.

**Features**:
- Create acknowledgment tasks
- Track task completion
- Link tasks to documents via fa_document_links
- Task time tracking

**Priority**: Medium

**Cross-Reference**: BR-DOC-003

---

### 9.4 Notification Integration (FR-IN-004)
**Requirement**: The system shall send notifications.

**Features**:
- New document notifications
- Reminder notifications
- Expiry notifications
- Acknowledgment confirmations

**Priority**: High

**Cross-Reference**: BR-DOC-007

---

## 10. Reporting

### 10.1 Document Reports (FR-RP-001)
**Requirement**: The system shall provide document reports.

**Reports**:
- Document list by type
- Expiring documents report
- Acknowledgment status report
- Version history report
- Documents by entity report

**Priority**: Medium

---

### 10.2 Acknowledgment Reports (FR-RP-002)
**Requirement**: The system shall provide acknowledgment reports.

**Features**:
- Compliance status by document
- Compliance status by employee
- Overdue acknowledgments
- Completion trends

**Priority**: High

---

## 11. Security

### 11.1 Permission Constants (FR-SC-001)
**Requirement**: The system shall enforce RBAC permissions.

**Permission Constants**:
- `DOC_VIEW` - View documents
- `DOC_VIEW_ALL` - View all documents (bypass owner/group)
- `DOC_MANAGE` - Manage documents
- `DOC_UPLOAD` - Upload documents
- `DOC_ACKNOWLEDGE` - Acknowledge documents
- `DOC_ADMIN` - Full administrative access (bypasses all ACL)

**Priority**: High

**Cross-Reference**: BR-DOC-004

---

### 11.2 Audit Logging (FR-SC-002)
**Requirement**: The system shall log all document activities.

**Logged Events**:
- Document uploaded
- Document viewed
- Document updated
- Document deleted
- Document linked/unlinked
- Owner/group changed
- Acknowledgment recorded
- ACL denial

**Priority**: High

---

## 12. Non-Functional Requirements

### 12.1 Performance
- Document list load: < 2 seconds
- Document upload: < 10 seconds for files up to 10MB
- Search results: < 2 seconds
- Link operations: < 1 second

### 12.2 Security
- SQL injection prevention
- XSS prevention
- File type validation
- Maximum file size limits
- ACL checks on every document access

### 12.3 Compatibility
- FrontAccounting 2.4.0+
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.0+
- ksf_FA_Attachments 2.0.0+

### 12.4 Storage
- Support for common document formats (PDF, DOC, DOCX, TXT)
- File storage via ksf_FA_Attachments managed directory
- Database metadata storage

---

## 13. Appendix: Default Values

### Document Types
| Type | Description | Requires Acknowledgment |
|------|-------------|-------------------------|
| Policy | Company policies | Yes |
| Contract | Employment contracts | Yes |
| Form | Tax/benefits forms | Yes |
| Training | Training materials | Yes |
| Handbook | Employee handbooks | Yes |
| Other | Miscellaneous | No |

### Document Statuses
| Status | Description |
|--------|-------------|
| Active | Current valid document |
| Archived | Old version archived |
| Superseded | Replaced by newer version |

### Acknowledgment Statuses
| Status | Description |
|--------|-------------|
| Pending | Not yet acknowledged |
| Acknowledged | Successfully acknowledged |
| Expired | Deadline passed |
| Waived | Requirement waived |

### ACL Levels
| Level | Owner | Group | Other |
|-------|-------|-------|-------|
| View | Yes | Yes | With DOC_VIEW_ALL |
| Edit | Yes | With DOC_MANAGE | No |
| Admin | DOC_ADMIN | DOC_ADMIN | DOC_ADMIN |

---

*Document Version: 2.0.0*
*Last Updated: 2026-06-28*
