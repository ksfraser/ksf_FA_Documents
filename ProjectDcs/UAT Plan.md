# ksf_FA_Documents UAT Plan

## Document Information
- **Module**: ksf_FA_Documents (Document Management)
- **Version**: 2.0.0
- **Date**: 2026-06-28
- **Status**: Updated
- **Author**: KSFII Development Team

## 1. Introduction

### 1.1 Purpose
This UAT Plan defines the user acceptance test cases for the ksf_FA_Documents module. These tests verify that the module meets business requirements from an end-user perspective, including the new multi-entity linking, ACL, and shared attachment features.

### 1.2 Scope
- Document upload and management
- Version control
- Multi-entity linking
- Access control (owner + group)
- Shared attachment service via ksf_FA_Attachments
- Legacy document attachment backward compatibility
- Expiry tracking
- Acknowledgment workflow
- CRM/HRM/Task integration

### 1.3 Test Environment
- **Platform**: FrontAccounting 2.4.x
- **Browser**: Chrome/Firefox latest
- **PHP**: 8.0+
- **Database**: MySQL 5.7+
- **Dependencies**: ksf_FA_Attachments 2.0.0+ (installed and active)

### 1.4 Stakeholders
- HR Managers
- Document Administrators
- Employees
- System Administrators
- CRM Users

## 2. UAT Test Cases

### 2.1 Document Upload (UAT-DM)

#### UAT-DM-001: Upload New Document
**Objective**: Verify user can upload a new document

**Test Scenario**:
1. Navigate to Documents > Upload
2. Click Browse and select a PDF file
3. Enter title: "Employee Handbook 2026"
4. Select type: "Handbook"
5. Optionally set expiry date
6. Click Upload

**Expected Result**: Document uploaded successfully and appears in document list

**Acceptance Criteria**:
- [ ] File uploaded without error
- [ ] Title displays correctly
- [ ] Type assigned correctly
- [ ] Document appears in list
- [ ] Version shows as 1.0
- [ ] Owner auto-set to current user

---

#### UAT-DM-002: Set Document Expiry Date
**Objective**: Verify user can set document expiry date

**Test Scenario**:
1. Edit a document
2. Set expiry date to one year from today
3. Save document

**Expected Result**: Expiry date stored and displayed

**Acceptance Criteria**:
- [ ] Expiry date saved to database
- [ ] Expiry date displays in document view
- [ ] Document appears in expiring list

---

#### UAT-DM-003: Search Documents
**Objective**: Verify user can search for documents

**Test Scenario**:
1. Navigate to All Documents
2. Enter search term in title search
3. Click Search

**Expected Result**: Search returns matching documents (ACL-filtered)

**Acceptance Criteria**:
- [ ] Search returns relevant results
- [ ] No false positives
- [ ] Results display correctly
- [ ] Results respect ACL (user sees only accessible documents)

---

### 2.2 Multi-Entity Linking (UAT-ML) (NEW)

#### UAT-ML-001: Link Document to Multiple Entities
**Objective**: Verify document can be linked to multiple entities simultaneously

**Test Scenario**:
1. Upload a company policy document titled "Data Protection Policy"
2. On the document view page, click "Link Entities"
3. Select entity type "Training Course" and pick course ID 101
4. Add another link: entity type "Customer" and pick customer ID 42
5. Add another link: entity type "Project" and pick project ID 7
6. Save all links

**Expected Result**: Document linked to training course, customer, and project simultaneously

**Acceptance Criteria**:
- [ ] All three links visible on document view page
- [ ] Querying by training course 101 returns the policy document
- [ ] Querying by customer 42 returns the policy document
- [ ] Querying by project 7 returns the policy document
- [ ] Duplicate link attempt is rejected

---

#### UAT-ML-002: Remove Entity Link
**Objective**: Verify link can be removed independently

**Test Scenario**:
1. Open document with 3 entity links
2. Remove the customer link
3. Save

**Expected Result**: Customer link removed; training course and project links remain

**Acceptance Criteria**:
- [ ] Customer link no longer shown
- [ ] Other two links remain intact
- [ ] Document itself unaffected

---

### 2.3 Access Control (UAT-ACL) (NEW)

#### UAT-ACL-001: Owner Access
**Objective**: Verify document owner has full access

**Test Scenario**:
1. User A uploads a document
2. User A can view, edit, and manage the document
3. User B (different department) tries to view the document

**Expected Result**: User A has full access; User B is denied

**Acceptance Criteria**:
- [ ] User A sees the document in list
- [ ] User A can edit all fields
- [ ] User B does not see the document in list
- [ ] User B gets "Access Denied" if they navigate directly

---

#### UAT-ACL-002: Group Access
**Objective**: Verify group-based access works

**Test Scenario**:
1. Admin creates document with group_id = "HR Group"
2. User X (in HR Group) logs in
3. User X can view the document
4. User X tries to edit the document (without DOC_MANAGE)
5. User Y (in HR Group, with DOC_MANAGE) edits the document

**Expected Result**: Group viewing works; editing requires DOC_MANAGE

**Acceptance Criteria**:
- [ ] User X views document successfully
- [ ] User X cannot edit document (edit button hidden or disabled)
- [ ] User Y can edit document
- [ ] User Z (not in HR Group) cannot see document

---

#### UAT-ACL-003: Admin Sets Owner and Group
**Objective**: Verify admin can reassign owner and group

**Test Scenario**:
1. Admin logs in
2. Opens document owned by User A, group = HR Group
3. Changes owner to User C
4. Changes group to "Finance Group"
5. Saves

**Expected Result**: Owner and group updated

**Acceptance Criteria**:
- [ ] Owner changed to User C
- [ ] Group changed to Finance Group
- [ ] User C can now access the document
- [ ] User A no longer has special owner privileges

---

### 2.4 Shared Attachment Service (UAT-AT) (NEW)

#### UAT-AT-001: Upload Attachment via Shared Service
**Objective**: Verify attachment upload uses ksf_FA_Attachments

**Test Scenario**:
1. Open a document
2. Click "Attach File"
3. Select a PDF file
4. Upload

**Expected Result**: File stored via ksf_FA_Attachments; record in fa_attachments table

**Acceptance Criteria**:
- [ ] Attachment appears in document view
- [ ] Database record exists in fa_attachments with source_type='document'
- [ ] source_id matches the document ID
- [ ] File exists in ksf_FA_Attachments storage directory
- [ ] Can download the attachment

---

#### UAT-AT-002: Legacy Attachment Still Visible
**Objective**: Verify existing attachments from v1.x remain accessible

**Test Scenario**:
1. Open a document that was migrated from v1.x (has legacy fa_document_attachments records)
2. View attachments list

**Expected Result**: Legacy attachments appear alongside new attachments

**Acceptance Criteria**:
- [ ] Legacy attachments listed
- [ ] Legacy attachments downloadable
- [ ] Cannot upload new attachments to legacy table (goes to fa_attachments)

---

### 2.5 Version Control (UAT-VC)

#### UAT-VC-001: Create New Version
**Objective**: Verify user can upload new version of existing document

**Test Scenario**:
1. Open existing document
2. Click Upload New Version
3. Select updated file
4. Version automatically increments

**Expected Result**: New version created, old version preserved

**Acceptance Criteria**:
- [ ] Version number increments
- [ ] Old version still accessible
- [ ] Version history shows both versions
- [ ] Each version shows its own attachments

---

#### UAT-VC-002: View Version History
**Objective**: Verify user can view version history

**Test Scenario**:
1. Open document with multiple versions
2. Click Version History
3. View list of versions

**Expected Result**: All versions displayed with metadata

**Acceptance Criteria**:
- [ ] All versions listed
- [ ] Version dates displayed
- [ ] Version authors displayed
- [ ] Can download any version

---

### 2.6 Expiry Management (UAT-EM)

#### UAT-EM-001: View Expiring Documents
**Objective**: Verify user sees expiring documents

**Test Scenario**:
1. Navigate to dashboard
2. View expiring documents widget

**Expected Result**: Documents expiring within configured period displayed

**Acceptance Criteria**:
- [ ] Expiring documents listed
- [ ] Days until expiry shown
- [ ] Links to documents work

---

#### UAT-EM-002: Expired Document Alert
**Objective**: Verify expired documents are highlighted

**Test Scenario**:
1. Set document expiry date in the past
2. View document list

**Expected Result**: Expired document highlighted or flagged

**Acceptance Criteria**:
- [ ] Expired document visually distinct
- [ ] Status shown as expired
- [ ] Can update expiry to extend

---

### 2.7 Acknowledgment Workflow (UAT-AW)

#### UAT-AW-001: Require Acknowledgment
**Objective**: Verify document can require acknowledgment

**Test Scenario**:
1. Upload new policy document
2. Check "Requires Acknowledgment"
3. Select employee group

**Expected Result**: Acknowledgment request created for employees

**Acceptance Criteria**:
- [ ] Flag saved to database
- [ ] Acknowledgment tasks created
- [ ] Employees can see pending document

---

#### UAT-AW-002: Employee Acknowledges Document
**Objective**: Verify employee can acknowledge document

**Test Scenario**:
1. Employee logs in
2. Views My Documents
3. Opens pending policy document
4. Clicks "I Acknowledge"
5. Confirms acknowledgment

**Expected Result**: Acknowledgment recorded

**Acceptance Criteria**:
- [ ] Acknowledge button available
- [ ] Confirmation dialog appears
- [ ] Acknowledgment timestamp stored
- [ ] Status changed to Acknowledged

---

#### UAT-AW-003: View My Pending Documents
**Objective**: Verify employee sees pending acknowledgments

**Test Scenario**:
1. Employee logs in
2. Views My Documents section
3. Counts pending items

**Expected Result**: All pending acknowledgments displayed

**Acceptance Criteria**:
- [ ] Pending documents listed
- [ ] Due dates shown
- [ ] Links to documents work

---

#### UAT-AW-004: View Acknowledgment Status Report
**Objective**: Verify manager can view acknowledgment compliance

**Test Scenario**:
1. Navigate to Reports > Acknowledgments
2. Select document
3. View status breakdown

**Expected Result**: Compliance status displayed

**Acceptance Criteria**:
- [ ] Total employees shown
- [ ] Acknowledged count shown
- [ ] Pending count shown
- [ ] Percentage displayed

---

### 2.8 Document Types (UAT-DT)

#### UAT-DT-001: Create Document Type
**Objective**: Verify admin can create document type

**Test Scenario**:
1. Navigate to Settings > Document Types
2. Click Add New
3. Enter type name and description
4. Check "Requires Acknowledgment"
5. Save

**Expected Result**: New type created

**Acceptance Criteria**:
- [ ] Type saved to database
- [ ] Type appears in dropdown
- [ ] Acknowledgment setting stored

---

#### UAT-DT-002: Configure Default Acknowledgment
**Objective**: Verify acknowledgment can be required by type

**Test Scenario**:
1. Edit document type
2. Check "Requires Acknowledgment"
3. Save
4. Upload document of this type

**Expected Result**: New documents require acknowledgment by default

**Acceptance Criteria**:
- [ ] Default applied to new documents
- [ ] Can override per document

---

### 2.9 Integration (UAT-IN)

#### UAT-IN-001: CRM Entity Link
**Objective**: Verify document can be linked to CRM customer

**Test Scenario**:
1. Open a customer in CRM
2. Navigate to customer's documents section
3. Link an existing document to the customer
4. Verify link appears

**Expected Result**: Document linked to customer

**Acceptance Criteria**:
- [ ] Document appears in customer profile
- [ ] Link persists through updates
- [ ] Customer ID stored in fa_document_links

---

#### UAT-IN-002: HRM Employee Link
**Objective**: Verify document can be linked to employee

**Test Scenario**:
1. View employee in HRM
2. Click Add Document
3. Select uploaded document

**Expected Result**: Document linked to employee

**Acceptance Criteria**:
- [ ] Document appears in employee profile
- [ ] Link persists through updates

---

#### UAT-IN-003: Task Created on Upload
**Objective**: Verify acknowledgment creates task

**Test Scenario**:
1. Upload document requiring acknowledgment
2. Assign to employee
3. View Tasks module

**Expected Result**: Task created for acknowledgment

**Acceptance Criteria**:
- [ ] Task visible in Tasks
- [ ] Task linked to document
- [ ] Completion updates acknowledgment

---

### 2.10 Reports (UAT-RP)

#### UAT-RP-001: Document List Report
**Objective**: Verify report generates correctly

**Test Scenario**:
1. Navigate to Reports > Documents
2. Filter by type
3. Generate report

**Expected Result**: Report displays correctly

**Acceptance Criteria**:
- [ ] All documents of type listed
- [ ] Filters applied correctly
- [ ] Can export to CSV

---

#### UAT-RP-002: Expiry Report
**Objective**: Verify expiry report works

**Test Scenario**:
1. Navigate to Reports > Expiring
2. Set days ahead to 30
3. Generate report

**Expected Result**: Documents expiring in 30 days listed

**Acceptance Criteria**:
- [ ] Correct documents included
- [ ] Expiry dates accurate
- [ ] Export works

---

### 2.11 Permissions (UAT-AC)

#### UAT-AC-001: View-Only Access
**Objective**: Verify user with DOC_VIEW can only view

**Test Scenario**:
1. Login as user with DOC_VIEW only
2. Try to upload document
3. Try to delete document
4. View document

**Expected Result**: Only view allowed

**Acceptance Criteria**:
- [ ] Upload button hidden
- [ ] Delete button hidden
- [ ] View works

---

#### UAT-AC-002: Full Admin Access
**Objective**: Verify admin has full access

**Test Scenario**:
1. Login as admin
2. Access all functions

**Expected Result**: All functions accessible

**Acceptance Criteria**:
- [ ] Can upload
- [ ] Can delete
- [ ] Can manage types
- [ ] Can view all reports
- [ ] Can change document owner/group

---

### 2.12 Dashboard (UAT-DB)

#### UAT-DB-001: Dashboard Widget
**Objective**: Verify dashboard displays correctly

**Test Scenario**:
1. Login as HR
2. View dashboard

**Expected Result**: Document statistics displayed

**Acceptance Criteria**:
- [ ] Total documents count (ACL-aware)
- [ ] Pending acknowledgments count
- [ ] Expiring soon count
- [ ] Quick links work

---

#### UAT-DB-002: Quick Actions
**Objective**: Verify quick action buttons work

**Test Scenario**:
1. View dashboard
2. Click Upload New Document

**Expected Result**: Upload page opens

**Acceptance Criteria**:
- [ ] Button navigates correctly
- [ ] All quick actions work

---

## 3. Integration Testing

### 3.1 FrontAccounting Integration
- [ ] Module installs correctly
- [ ] Menu items appear
- [ ] Permissions work with FA roles
- [ ] Database tables created (fa_documents, fa_document_links, etc.)

### 3.2 ksf_FA_Attachments Integration (NEW)
- [ ] Upload attachment creates record in fa_attachments
- [ ] source_type = 'document' and source_id = document.id
- [ ] Legacy fa_document_attachments still readable
- [ ] Module deactivates gracefully if ksf_FA_Attachments is missing

### 3.3 CRM Integration
- [ ] Documents link to customers via fa_document_links
- [ ] Customer view shows documents
- [ ] CRM profile displays correctly

### 3.4 HRM Integration
- [ ] Documents link to employees
- [ ] Employee view shows documents
- [ ] HRM profile displays correctly

### 3.5 Task System Integration
- [ ] Tasks created for acknowledgments
- [ ] Task completion updates status
- [ ] Tasks linked to documents via fa_document_links

## 4. Performance Testing

### 4.1 Response Times
- [ ] Document list loads under 2 seconds
- [ ] Search completes under 2 seconds
- [ ] File upload completes under 10 seconds
- [ ] Entity link query under 1 second

### 4.2 Large Data
- [ ] Handles 1000+ documents
- [ ] Handles 10,000+ acknowledgments
- [ ] Handles 5,000+ entity links
- [ ] Pagination works correctly

## 5. Security Testing

### 5.1 Access Control
- [ ] Unauthorized users blocked
- [ ] Cross-user access prevented
- [ ] Permission checks enforced
- [ ] ACL checks enforced (owner, group, admin bypass)

### 5.2 Input Validation
- [ ] Invalid file types rejected
- [ ] SQL injection prevented
- [ ] XSS attacks blocked

## 6. Sign-Off Criteria

### 6.1 Functional Criteria
All UAT test cases must pass:
- [ ] Document upload and management
- [ ] Version control
- [ ] Multi-entity linking
- [ ] Access control (owner + group ACL)
- [ ] Shared attachment service (ksf_FA_Attachments)
- [ ] Legacy attachment backward compatibility
- [ ] Expiry tracking
- [ ] Acknowledgment workflow
- [ ] Integration features

### 6.2 Non-Functional Criteria
- [ ] Performance requirements met
- [ ] Security requirements met
- [ ] Browser compatibility verified

### 6.3 Integration Criteria
- [ ] FA module integration verified
- [ ] ksf_FA_Attachments integration verified
- [ ] CRM integration verified
- [ ] HRM integration verified
- [ ] Task integration verified

## 7. Test Results Summary

| Test Area | Total Tests | Passed | Failed | Pass Rate |
|-----------|------------|--------|--------|-----------|
| Document Management | 3 | | | |
| Multi-Entity Linking | 2 | | | |
| Access Control | 3 | | | |
| Shared Attachments | 2 | | | |
| Version Control | 2 | | | |
| Expiry Management | 2 | | | |
| Acknowledgment | 4 | | | |
| Document Types | 2 | | | |
| Integration | 3 | | | |
| Reports | 2 | | | |
| Permissions | 2 | | | |
| Dashboard | 2 | | | |
| **Total** | **29** | | | **95%+** |

## 8. Defects Found

| ID | Description | Severity | Status |
|----|-------------|----------|--------|
| | | | |

## 9. UAT Sign-Off

### 9.1 Test Results
- **Total Test Cases**: 29
- **Passed**:
- **Failed**:
- **Pass Rate**:

### 9.2 Sign-Off

| Role | Name | Signature | Date |
|------|------|----------|------|
| Project Manager | | | |
| QA Lead | | | |
| Business Owner | | | |
| Technical Lead | | | |

---

*Document Version: 2.0.0*
*Last Updated: 2026-06-28*
