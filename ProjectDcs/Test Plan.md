# ksf_FA_Documents Test Plan

## Document Information
- **Module**: ksf_FA_Documents (Document Management)
- **Version**: 2.0.0
- **Date**: 2026-06-28
- **Status**: Updated
- **Author**: KSFII Development Team

## 1. Introduction

### 1.1 Purpose
This test plan defines the testing strategy and approach for the ksf_FA_Documents module, covering all functional requirements including multi-entity linking, ACL enforcement, shared attachment integration, versioning, expiry tracking, and acknowledgment workflows.

### 1.2 Scope
- Unit testing of core functions
- Integration testing with FA framework
- UI component testing
- Document workflow testing
- Multi-entity linking testing
- ACL enforcement testing
- Shared attachment integration testing
- Legacy backward compatibility testing
- Acknowledgment tracking testing
- Database operation testing

### 1.3 Test Environment
- **PHP Version**: 8.0+
- **Database**: MySQL 5.7+ / MariaDB 10.0+
- **FA Version**: 2.4.0+
- **Testing Framework**: PHPUnit
- **Dependencies**: ksf_FA_Attachments 2.0.0+ (must be installed and active)

## 2. Testing Strategy

### 2.1 Test Levels

#### Unit Testing
Testing individual functions and methods in isolation:
- Document entity tests
- ACL gate function tests
- Link repository tests
- AttachmentOperationsTrait unit tests
- Version management tests

#### Integration Testing
Testing the module's interaction with FA core and ksf_FA_Attachments:
- Database CRUD operations
- ksf_FA_Attachments service integration
- FA permission system interaction
- Multi-entity link resolution

#### System Testing
End-to-end testing of complete workflows:
- Document upload -> link to entities -> ACL check -> attachment upload -> acknowledgment
- Legacy data migration verification

### 2.2 Test Types

| Type | Description | Coverage |
|------|-------------|----------|
| Functional | Verify features work as specified | 100% |
| Regression | Ensure no existing features broken | 100% |
| Performance | Verify acceptable response times | Selected |
| Security | Verify input validation and ACL | Critical paths |

## 3. Test Cases by Module

### 3.1 Document Management (TC-DM)

#### TC-DM-001: Upload Document
**Preconditions**:
- User has DOC_UPLOAD permission
- File format is valid

**Test Steps**:
1. Navigate to Upload page
2. Select document file
3. Enter title, type
4. Set expiry date (optional)
5. Check "requires acknowledgment" if needed
6. Submit form

**Expected Result**: Document uploaded and saved with owner = current user, group_id = current user's group

**Priority**: High

---

#### TC-DM-002: Create Document with Metadata
**Preconditions**: User has DOC_MANAGE permission

**Test Steps**:
1. Create document with all metadata fields including owner and group_id
2. Verify stored in database
3. Verify retrieval
4. Verify owner and group_id set correctly

**Expected Result**: Document created with all metadata

**Priority**: High

---

#### TC-DM-003: Update Document
**Preconditions**: Document exists, user is owner or has DOC_MANAGE

**Test Steps**:
1. Update document title
2. Update document type
3. Update expiry date
4. Update owner (admin only)
5. Update group_id (admin only)

**Expected Result**: All fields updated correctly; ACL check passed

**Priority**: High

---

#### TC-DM-004: Delete Document
**Preconditions**: Document exists, user has DOC_ADMIN

**Test Steps**:
1. Delete document (soft delete)
2. Verify status changed to Archived
3. Verify version history retained
4. Verify entity links preserved in fa_document_links

**Expected Result**: Document archived, not permanently deleted, links preserved

**Priority**: High

---

#### TC-DM-005: Search Documents
**Preconditions**: Multiple documents exist

**Test Steps**:
1. Search by title
2. Filter by type
3. Filter by status
4. Filter by date range
5. Filter by owner
6. Filter by linked entity

**Expected Result**: Correctly filtered results (ACL-filtered)

**Priority**: High

---

#### TC-DM-006: Document Type CRUD
**Preconditions**: User has DOC_ADMIN permission

**Test Steps**:
1. Create new document type
2. List document types
3. Update document type
4. Delete document type

**Expected Result**: All operations succeed

**Priority**: High

---

### 3.2 Multi-Entity Linking (TC-ML) (NEW)

#### TC-ML-001: Link Document to Multiple Entities
**Preconditions**: Document exists; customer, project, and task records exist

**Test Steps**:
1. Link document to customer (entity_type='customer', entity_id=1)
2. Link same document to project (entity_type='project', entity_id=5)
3. Link same document to task (entity_type='task', entity_id=10)
4. Verify three records in fa_document_links
5. Verify duplicate link attempt is rejected

**Expected Result**: Document linked to all three entities; duplicate rejected

**Priority**: High

**Cross-Reference**: FR-DM-008, BR-DOC-003

---

#### TC-ML-002: Query Documents by Entity
**Preconditions**: Multiple documents linked to various entities

**Test Steps**:
1. Get all entities for document A
2. Get all documents linked to customer 1
3. Get all documents linked to project 5
4. Verify an entity with no links returns empty array

**Expected Result**: Correct entities and documents returned

**Priority**: High

**Cross-Reference**: FR-DM-008, BR-DOC-003

---

#### TC-ML-003: Unlink Document from Entity
**Preconditions**: Document linked to customer and project

**Test Steps**:
1. Unlink document from customer
2. Verify only one link remains (project)
3. Verify unlink of non-existent link returns false
4. Delete document; verify all links cascade-deleted

**Expected Result**: Links removed correctly

**Priority**: High

**Cross-Reference**: FR-DM-009, BR-DOC-003

---

### 3.3 Access Control (TC-ACL) (NEW)

#### TC-ACL-001: Owner Access
**Preconditions**: Document with owner = user A

**Test Steps**:
1. User A views document (should succeed)
2. User A edits document (should succeed)
3. User B (not owner, different group) views document (should fail)

**Expected Result**: Owner has full view/edit; other users denied

**Priority**: High

**Cross-Reference**: FR-DM-010, BR-DOC-004

---

#### TC-ACL-002: Group Access
**Preconditions**: Document with group_id = group G, owner = user A

**Test Steps**:
1. User B (in group G, no DOC_MANAGE) views document (should succeed)
2. User B edits document (should fail)
3. User C (in group G, with DOC_MANAGE) edits document (should succeed)
4. User D (not in group G, not owner) views document (should fail)

**Expected Result**: Group members can view; only DOC_MANAGE group members can edit

**Priority**: High

**Cross-Reference**: FR-DM-010, BR-DOC-004

---

#### TC-ACL-003: Admin Bypass
**Preconditions**: Document with owner = user A, group_id = group G

**Test Steps**:
1. Admin user (DOC_ADMIN) views document (should succeed)
2. Admin edits document (should succeed)
3. Admin changes owner to user B
4. Admin changes group_id to group H
5. Verify changes persisted

**Expected Result**: Admin bypasses all ACL checks

**Priority**: High

**Cross-Reference**: FR-DM-010, BR-DOC-004

---

### 3.4 Attachment Integration (TC-AT) (NEW)

#### TC-AT-001: Upload Attachment via Shared Service
**Preconditions**: Document exists; ksf_FA_Attachments module is active

**Test Steps**:
1. Upload a file as attachment to document
2. Verify record created in fa_attachments with source_type='document'
3. Verify source_id = document.id
4. Verify file stored in ksf_FA_Attachments directory
5. Verify attachment metadata (filename, size, mime_type) stored correctly

**Expected Result**: Attachment created in shared fa_attachments table

**Priority**: High

**Cross-Reference**: FR-DM-011, BR-DOC-005

---

#### TC-AT-002: List Attachments
**Preconditions**: Document has attachments in both fa_attachments (new) and fa_document_attachments (legacy)

**Test Steps**:
1. List attachments for document
2. Verify new attachment from fa_attachments appears
3. Verify legacy attachment from fa_document_attachments appears
4. Verify combined list is merged correctly

**Expected Result**: All attachments (new + legacy) listed

**Priority**: High

**Cross-Reference**: FR-DM-012, BR-DOC-005

---

#### TC-AT-003: Legacy Attachment Read-Only
**Preconditions**: Document with legacy attachment in fa_document_attachments

**Test Steps**:
1. Read legacy attachment metadata
2. Attempt to write to fa_document_attachments (should be blocked)
3. Verify new uploads always go to fa_attachments

**Expected Result**: Legacy data readable; new writes go to fa_attachments

**Priority**: Medium

**Cross-Reference**: FR-DM-013, BR-DOC-005

---

### 3.5 Version Control (TC-VC)

#### TC-VC-001: Create New Version
**Preconditions**: Document exists, user has edit access

**Test Steps**:
1. Upload new version of existing document
2. Verify version incremented
3. Verify previous version retained
4. Verify previous version's attachments remain accessible

**Expected Result**: New version created, old preserved

**Priority**: High

---

#### TC-VC-002: View Version History
**Preconditions**: Document has multiple versions

**Test Steps**:
1. View document
2. Click version history
3. Verify all versions listed

**Expected Result**: All versions displayed with metadata

**Priority**: Medium

---

#### TC-VC-003: Download Previous Version
**Preconditions**: Document has multiple versions

**Test Steps**:
1. Select previous version
2. Download
3. Verify content matches version

**Expected Result**: Correct version downloaded

**Priority**: Medium

---

### 3.6 Expiry Tracking (TC-ET)

#### TC-ET-001: Set Expiry Date
**Preconditions**: Document exists

**Test Steps**:
1. Edit document
2. Set expiry date
3. Save
4. Query expiring documents

**Expected Result**: Expiry date stored, query returns document

**Priority**: High

---

#### TC-ET-002: Query Expiring Documents
**Preconditions**: Documents with various expiry dates

**Test Steps**:
1. Query documents expiring in 30 days
2. Verify correct documents returned
3. Check date calculation

**Expected Result**: Correct documents based on expiry

**Priority**: High

---

#### TC-ET-003: Expired Document Alert
**Preconditions**: Document with past expiry date

**Test Steps**:
1. View dashboard
2. Check expired document alerts

**Expected Result**: Expired documents displayed

**Priority**: Medium

---

### 3.7 Acknowledgment Workflow (TC-AW)

#### TC-AW-001: Assign Acknowledgment
**Preconditions**: Document with requires_acknowledgment = true

**Test Steps**:
1. Upload document requiring acknowledgment
2. Assign to employee group
3. Set deadline
4. Query pending acknowledgments

**Expected Result**: Acknowledgments created for employees

**Priority**: High

---

#### TC-AW-002: Employee Acknowledges Document
**Preconditions**:
- Employee has pending acknowledgment
- Employee has DOC_ACKNOWLEDGE permission

**Test Steps**:
1. Employee logs in
2. Views My Documents
3. Opens pending document
4. Clicks Acknowledge button
5. Verify acknowledgment recorded

**Expected Result**: Acknowledgment stored with timestamp

**Priority**: High

---

#### TC-AW-003: View Acknowledgment Status
**Preconditions**: Document assigned to multiple employees

**Test Steps**:
1. Navigate to document acknowledgments
2. View status breakdown
3. Verify counts match

**Expected Result**: Correct status counts

**Priority**: High

---

#### TC-AW-004: Acknowledgment Report
**Preconditions**: Multiple acknowledgments exist

**Test Steps**:
1. Generate acknowledgment report
2. Filter by document
3. Filter by employee
4. Export report

**Expected Result**: Report generated with correct data

**Priority**: Medium

---

### 3.8 Integration (TC-IN)

#### TC-IN-001: Entity Link Integration (CRM/HRM)
**Preconditions**: CRM and/or HRM module installed

**Test Steps**:
1. Link document to a customer record
2. Link document to an employee record
3. Query documents by customer ID
4. Query documents by employee ID

**Expected Result**: Document linked and retrievable by entity

**Priority**: High

---

#### TC-IN-002: Task Integration
**Preconditions**: Task module installed

**Test Steps**:
1. Upload document requiring acknowledgment
2. Verify tasks created
3. Complete acknowledgment
4. Verify task completed

**Expected Result**: Tasks created and tracked

**Priority**: Medium

---

#### TC-IN-003: Notification Integration
**Preconditions**: Notification module installed

**Test Steps**:
1. Upload document requiring acknowledgment
2. Verify notification sent
3. Verify expiration reminder sent

**Expected Result**: Notifications sent correctly

**Priority**: Medium

---

### 3.9 Permissions (TC-AC)

#### TC-AC-001: Permission Enforcement
**Preconditions**: Multiple users with different roles

**Test Steps**:
1. Test with DOC_VIEW only
2. Test with DOC_MANAGE
3. Test with DOC_UPLOAD
4. Test unauthorized access (no DOC_VIEW)

**Expected Result**: Access correctly enforced

**Priority**: High

---

#### TC-AC-002: Menu Visibility
**Preconditions**: User with specific permissions

**Test Steps**:
1. Login as admin
2. Verify all menu items visible
3. Login as regular user
4. Verify limited menu items

**Expected Result**: Menu correctly filtered

**Priority**: High

---

### 3.10 Security (TC-SC)

#### TC-SC-001: File Type Validation
**Preconditions**: None

**Test Steps**:
1. Upload invalid file type
2. Upload executable script
3. Upload oversized file

**Expected Result**: Invalid uploads rejected

**Priority**: High

---

#### TC-SC-002: SQL Injection Prevention
**Preconditions**: None

**Test Steps**:
1. Inject SQL in document title
2. Inject SQL in search
3. Inject SQL in file name

**Expected Result**: SQL rejected/encoded

**Priority**: High

---

#### TC-SC-003: XSS Prevention
**Preconditions**: None

**Test Steps**:
1. Include script tag in document content
2. Include script tag in title

**Expected Result**: Script escaped in output

**Priority**: High

---

#### TC-SC-004: Audit Logging
**Preconditions**: User with any permission

**Test Steps**:
1. Upload document (verify log entry)
2. View document (verify log entry)
3. Edit document (verify log entry)
4. Link document to entity (verify log entry)
5. ACL denial (verify log entry)

**Expected Result**: All actions logged

**Priority**: High

---

## 4. Performance Tests

### 4.1 Large Dataset Tests
- Test with 1,000 documents
- Test with 10,000 versions
- Test with 1,000 acknowledgments
- Test with 5,000 entity links
- Test with 500 attachments (via fa_attachments)

### 4.2 Response Time Requirements
- Document list (100 records): < 2 seconds
- File upload (5MB): < 10 seconds
- Search results: < 2 seconds
- Entity link query: < 1 second
- ACL check: < 100ms

## 5. Test Data Management

### 5.1 Test Data Requirements
- Sample documents (minimum 20)
- Multiple document types
- Various version counts
- Multiple acknowledgment statuses
- Sample entities (customers, projects, tasks, employees)
- Mix of new (fa_attachments) and legacy (fa_document_attachments) attachments

### 5.2 Test Data Cleanup
- Use database transactions for rollback
- Clean up after test suites
- Reset auto-increment counters

## 6. Test Execution

### 6.1 Test Run Matrix

| Environment | Browser | FA Version | PHP Version |
|-------------|---------|------------|-------------|
| Local | Chrome | 2.4.x | 8.1 |
| Dev | Firefox | 2.4.x | 8.0 |
| CI | Headless | 2.4.x | 8.1 |

### 6.2 Test Schedule
- Unit tests: Every commit
- Integration tests: Daily
- Full regression: Before release

### 6.3 Pass/Fail Criteria
- Unit tests: 100% pass required
- Integration tests: 95% pass required
- Critical functional tests: 100% pass required

## 7. Defect Reporting

### 7.1 Severity Levels
- **Critical**: System crash, data loss
- **High**: Core feature not working
- **Medium**: Feature partially working
- **Low**: Cosmetic issue

### 7.2 Priority Levels
- **P0**: Must fix before release
- **P1**: Should fix before release
- **P2**: Can fix in next release
- **P3**: Backlog

## 8. Risk Assessment

### 8.1 High-Risk Areas
| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| File upload failures | Medium | High | Extensive test cases |
| Performance with large data | High | Medium | Query optimization |
| FA version compatibility | Low | High | Version checking |
| ksf_FA_Attachments dependency issues | Medium | High | Service contract testing |
| ACL bypass | Low | Critical | Comprehensive ACL test suite |
| Data migration errors (v1 -> v2) | Medium | High | Migration test + rollback plan |

### 8.2 Mitigation Strategies
- Comprehensive test coverage
- Regular compatibility testing
- Performance profiling
- Integration test suite
- ACL penetration testing

## 9. Test Deliverables

- [x] Test Plan Document
- [x] Test Cases (this document)
- [x] Test Scripts (tests/ directory)
- [x] Test Data Setup
- [x] Test Results Reports
- [x] Defect Reports

---

*Document Version: 2.0.0*
*Last Updated: 2026-06-28*
