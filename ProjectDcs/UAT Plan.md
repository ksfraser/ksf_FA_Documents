# FA_Documents UAT Plan

## Document Information
- **Module**: FA_Documents (Document Management)
- **Version**: 1.0.0
- **Date**: 2024-04-26
- **Status**: Implemented
- **Author**: KSFII Development Team

## 1. Introduction

### 1.1 Purpose
This UAT Plan defines the user acceptance test cases for the FA_Documents module. These tests verify that the module meets business requirements from an end-user perspective.

### 1.2 Scope
- Document upload and management
- Version control
- Expiry tracking
- Acknowledgment workflow
- HRM integration
- Task system integration

### 1.3 Test Environment
- **Platform**: FrontAccounting 2.4.x
- **Browser**: Chrome/Firefox latest
- **PHP**: 8.0+
- **Database**: MySQL 5.7+

### 1.4 Stakeholders
- HR Managers
- Document Administrators
- Employees
- System Administrators

## 2. UAT Test Cases

### 2.1 Document Upload (UAT-DM)

#### UAT-DM-001: Upload New Document
**Objective**: Verify user can upload a new document

**Test Scenario**:
1. Navigate to Documents > Upload
2. Click Browse and select a PDF file
3. Enter title: "Employee Handbook 2024"
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

**Expected Result**: Search returns matching documents

**Acceptance Criteria**:
- [ ] Search returns relevant results
- [ ] No false positives
- [ ] Results display correctly

---

### 2.2 Version Control (UAT-VC)

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

### 2.3 Expiry Management (UAT-EM)

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

### 2.4 Acknowledgment Workflow (UAT-AW)

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

### 2.5 Document Types (UAT-DT)

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

### 2.6 Integration (UAT-IN)

#### UAT-IN-001: HRM Document Link
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

#### UAT-IN-002: Task Created on Upload
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

### 2.7 Reports (UAT-RP)

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

### 2.8 Permissions (UAT-AC)

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

---

### 2.9 Dashboard (UAT-DB)

#### UAT-DB-001: Dashboard Widget
**Objective**: Verify dashboard displays correctly

**Test Scenario**:
1. Login as HR
2. View dashboard

**Expected Result**: Document statistics displayed

**Acceptance Criteria**:
- [ ] Total documents count
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
- [ ] Database tables created

### 3.2 HRM Integration
- [ ] Documents link to employees
- [ ] Employee view shows documents
- [ ] HRM profile displays correctly

### 3.3 Task System Integration
- [ ] Tasks created for acknowledgments
- [ ] Task completion updates status
- [ ] Task times tracked

## 4. Performance Testing

### 4.1 Response Times
- [ ] Document list loads under 2 seconds
- [ ] Search completes under 2 seconds
- [ ] File upload completes under 10 seconds

### 4.2 Large Data
- [ ] Handles 1000+ documents
- [ ] Handles 10,000+ acknowledgments
- [ ] Pagination works correctly

## 5. Security Testing

### 5.1 Access Control
- [ ] Unauthorized users blocked
- [ ] Cross-user access prevented
- [ ] Permission checks enforced

### 5.2 Input Validation
- [ ] Invalid file types rejected
- [ ] SQL injection prevented
- [ ] XSS attacks blocked

## 6. Sign-Off Criteria

### 6.1 Functional Criteria
All UAT test cases must pass:
- [ ] Document upload and management
- [ ] Version control
- [ ] Expiry tracking
- [ ] Acknowledgment workflow
- [ ] Integration features

### 6.2 Non-Functional Criteria
- [ ] Performance requirements met
- [ ] Security requirements met
- [ ] Browser compatibility verified

### 6.3 Integration Criteria
- [ ] FA module integration verified
- [ ] HRM integration verified
- [ ] Task integration verified

## 7. Test Results Summary

| Test Area | Total Tests | Passed | Failed | Pass Rate |
|-----------|------------|--------|--------|-----------|
| Document Management | 6 | | | |
| Version Control | 3 | | | |
| Expiry Management | 2 | | | |
| Acknowledgment | 4 | | | |
| Document Types | 2 | | | |
| Integration | 2 | | | |
| Reports | 2 | | | |
| Permissions | 2 | | | |
| Dashboard | 2 | | | |
| **Total** | **25** | | | **95%+** |

## 8. Defects Found

| ID | Description | Severity | Status |
|----|------------|----------|--------|
| | | | |

## 9. UAT Sign-Off

### 9.1 Test Results
- **Total Test Cases**: 25
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
*Document Version: 1.0.0*
*Last Updated: 2024-04-26*
