# FA_Documents Test Plan

## Document Information
- **Module**: FA_Documents (Document Management)
- **Version**: 1.0.0
- **Date**: 2024-04-26
- **Status**: Implemented
- **Author**: KSFII Development Team

## 1. Introduction

### 1.1 Purpose
This test plan defines the testing strategy and approach for the FA_Documents module, ensuring all functional requirements are met and the module functions correctly within the FrontAccounting framework.

### 1.2 Scope
- Unit testing of core functions
- Integration testing with FA framework
- UI component testing
- Document workflow testing
- Acknowledgment tracking testing
- Database operation testing

### 1.3 Test Environment
- **PHP Version**: 8.0+
- **Database**: MySQL 5.7+ / MariaDB 10.0+
- **FA Version**: 2.4.0+
- **Testing Framework**: PHPUnit

## 2. Testing Strategy

### 2.1 Test Levels

#### Unit Testing
Testing individual functions and methods in isolation.

#### Integration Testing
Testing the module's interaction with FA core components.

#### System Testing
End-to-end testing of complete workflows.

### 2.2 Test Types

| Type | Description | Coverage |
|------|-------------|----------|
| Functional | Verify features work as specified | 100% |
| Regression | Ensure no existing features broken | 100% |
| Performance | Verify acceptable response times | Selected |
| Security | Verify input validation | Critical paths |

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

**Expected Result**: Document uploaded and saved

**Priority**: High

#### TC-DM-002: Create Document with Metadata
**Preconditions**: User has DOC_MANAGE permission

**Test Steps**:
1. Create document with all metadata fields
2. Verify stored in database
3. Verify retrieval

**Expected Result**: Document created with all metadata

**Priority**: High

#### TC-DM-003: Update Document
**Preconditions**: Document exists

**Test Steps**:
1. Update document title
2. Update document type
3. Update expiry date

**Expected Result**: All fields updated correctly

**Priority**: High

#### TC-DM-004: Delete Document
**Preconditions**: Document exists

**Test Steps**:
1. Delete document (soft delete)
2. Verify status changed to Archived
3. Verify version history retained

**Expected Result**: Document archived, not permanently deleted

**Priority**: High

#### TC-DM-005: Search Documents
**Preconditions**: Multiple documents exist

**Test Steps**:
1. Search by title
2. Filter by type
3. Filter by status
4. Filter by date range

**Expected Result**: Correctly filtered results

**Priority**: High

#### TC-DM-006: Document Type CRUD
**Preconditions**: User has DOC_ADMIN permission

**Test Steps**:
1. Create new document type
2. List document types
3. Update document type
4. Delete document type

**Expected Result**: All operations succeed

**Priority**: High

### 3.2 Version Control (TC-VC)

#### TC-VC-001: Create New Version
**Preconditions**: Document exists

**Test Steps**:
1. Upload new version of existing document
2. Verify version incremented
3. Verify previous version retained

**Expected Result**: New version created, old preserved

**Priority**: High

#### TC-VC-002: View Version History
**Preconditions**: Document has multiple versions

**Test Steps**:
1. View document
2. Click version history
3. Verify all versions listed

**Expected Result**: All versions displayed with metadata

**Priority**: Medium

#### TC-VC-003: Download Previous Version
**Preconditions**: Document has multiple versions

**Test Steps**:
1. Select previous version
2. Download
3. Verify content matches version

**Expected Result**: Correct version downloaded

**Priority**: Medium

### 3.3 Expiry Tracking (TC-ET)

#### TC-ET-001: Set Expiry Date
**Preconditions**: Document exists

**Test Steps**:
1. Edit document
2. Set expiry date
3. Save
4. Query expiring documents

**Expected Result**: Expiry date stored, query returns document

**Priority**: High

#### TC-ET-002: Query Expiring Documents
**Preconditions**: Documents with various expiry dates

**Test Steps**:
1. Query documents expiring in 30 days
2. Verify correct documents returned
3. Check date calculation

**Expected Result**: Correct documents based on expiry

**Priority**: High

#### TC-ET-003: Expired Document Alert
**Preconditions**: Document with past expiry date

**Test Steps**:
1. View dashboard
2. Check expired document alerts

**Expected Result**: Expired documents displayed

**Priority**: Medium

### 3.4 Acknowledgment Workflow (TC-AW)

#### TC-AW-001: Assign Acknowledgment
**Preconditions**: Document with requires_acknowledgment = true

**Test Steps**:
1. Upload document requiring acknowledgment
2. Assign to employee group
3. Set deadline
4. Query pending acknowledgments

**Expected Result**: Acknowledgments created for employees

**Priority**: High

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

#### TC-AW-003: View Acknowledgment Status
**Preconditions**: Document assigned to multiple employees

**Test Steps**:
1. Navigate to document acknowledgments
2. View status breakdown
3. Verify counts match

**Expected Result**: Correct status counts

**Priority**: High

#### TC-AW-004: Acknowledgment Report
**Preconditions**: Multiple acknowledgments exist

**Test Steps**:
1. Generate acknowledgment report
2. Filter by document
3. Filter by employee
4. Export report

**Expected Result**: Report generated with correct data

**Priority**: Medium

### 3.5 Integration (TC-IN)

#### TC-IN-001: HRM Integration
**Preconditions**: HRM module installed

**Test Steps**:
1. Link document to employee
2. View employee profile
3. Verify document link displayed

**Expected Result**: Document linked and displayed

**Priority**: High

#### TC-IN-002: Task Integration
**Preconditions**: Task module installed

**Test Steps**:
1. Upload document requiring acknowledgment
2. Verify tasks created
3. Complete acknowledgment
4. Verify task completed

**Expected Result**: Tasks created and tracked

**Priority**: Medium

#### TC-IN-003: Notification Integration
**Preconditions**: Notification module installed

**Test Steps**:
1. Upload document requiring acknowledgment
2. Verify notification sent
3. Verify expiration reminder sent

**Expected Result**: Notifications sent correctly

**Priority**: Medium

### 3.6 Permissions (TC-AC)

#### TC-AC-001: Permission Enforcement
**Preconditions**: Multiple users with different roles

**Test Steps**:
1. Test with DOC_VIEW only
2. Test with DOC_MANAGE
3. Test with DOC_UPLOAD
4. Test unauthorized access

**Expected Result**: Access correctly enforced

**Priority**: High

#### TC-AC-002: Menu Visibility
**Preconditions**: User with specific permissions

**Test Steps**:
1. Login as admin
2. Verify all menu items visible
3. Login as regular user
4. Verify limited menu items

**Expected Result**: Menu correctly filtered

**Priority**: High

### 3.7 Security (TC-SC)

#### TC-SC-001: File Type Validation
**Preconditions**: None

**Test Steps**:
1. Upload invalid file type
2. Upload executable script
3. Upload oversized file

**Expected Result**: Invalid uploads rejected

**Priority**: High

#### TC-SC-002: SQL Injection Prevention
**Preconditions**: None

**Test Steps**:
1. Inject SQL in document title
2. Inject SQL in search
3. Inject SQL in file name

**Expected Result**: SQL rejected/encoded

**Priority**: High

#### TC-SC-003: XSS Prevention
**Preconditions**: None

**Test Steps**:
1. Include script tag in document content
2. Include script tag in title

**Expected Result**: Script escaped in output

**Priority**: High

## 4. Performance Tests

### 4.1 Large Dataset Tests
- Test with 1,000 documents
- Test with 10,000 versions
- Test with 1,000 acknowledgments

### 4.2 Response Time Requirements
- Document list (100 records): < 2 seconds
- File upload (5MB): < 10 seconds
- Search results: < 2 seconds

## 5. Test Data Management

### 5.1 Test Data Requirements
- Sample documents (minimum 20)
- Multiple document types
- Various version counts
- Multiple acknowledgment statuses

### 5.2 Test Data Cleanup
- Use database transactions for rollback
- Clean up after test suites
- Reset auto-increment counters

## 6. Test Execution

### 6.1 Test Run Matrix

| Environment | Browser | FA Version | PHP Version |
|-------------|---------|------------|--------------|
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
- **Low**: cosmetic issue

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
| Integration failures | Medium | Medium | Mock integration tests |

### 8.2 Mitigation Strategies
- Comprehensive test coverage
- Regular compatibility testing
- Performance profiling
- Integration test suite

## 9. Test Deliverables

- [x] Test Plan Document
- [x] Test Cases (this document)
- [x] Test Scripts (tests/ directory)
- [x] Test Data Setup
- [x] Test Results Reports
- [x] Defect Reports

---
*Document Version: 1.0.0*
*Last Updated: 2024-04-26*
