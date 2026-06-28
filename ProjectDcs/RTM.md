# RTM - ksf_FA_Documents

## Document Information
- **Module**: ksf_FA_Documents
- **Version**: 2.0.0
- **Date**: 2026-06-28
- **Status**: Updated

---

## 1. Requirements Traceability Matrix

| FR ID | Description | Priority | BR ID | UR ID | UC ID | Test Case |
|-------|-------------|----------|-------|-------|-------|-----------|
| FR-DM-001 | Document upload with metadata | High | BR-DOC-001 | UR-DOC-001 | UC-FA-DOC-002 | TC-DM-001, TC-DM-002 |
| FR-DM-002 | Configurable document types | High | BR-DOC-001 | UR-DOC-002 | UC-FA-DOC-002 | TC-DM-006 |
| FR-DM-003 | Document search and retrieval | High | BR-DOC-001 | UR-DOC-003 | UC-FA-DOC-001 | TC-DM-005 |
| FR-DM-004 | Document status tracking | High | BR-DOC-001 | UR-DOC-004 | UC-FA-DOC-001 | TC-DM-003 |
| FR-DM-005 | Document metadata update | High | BR-DOC-001, BR-DOC-004 | UR-DOC-005 | UC-FA-DOC-002 | TC-DM-003 |
| FR-DM-006 | Soft-deletion (archive) | Medium | BR-DOC-001 | UR-DOC-006 | UC-FA-DOC-002 | TC-DM-004 |
| FR-DM-007 | Note vs Document distinction | Medium | BR-DOC-008 | UR-DOC-007 | UC-FA-DOC-002 | TC-DM-002 |
| FR-DM-008 | Link document to multiple entities | High | BR-DOC-003 | UR-DOC-008 | UC-FA-DOC-003 | TC-ML-001, TC-ML-002 |
| FR-DM-009 | Unlink document from entity | High | BR-DOC-003 | UR-DOC-009 | UC-FA-DOC-003 | TC-ML-003 |
| FR-DM-010 | ACL enforcement (owner/group) | High | BR-DOC-004 | UR-DOC-010 | UC-FA-DOC-004 | TC-ACL-001, TC-ACL-002, TC-ACL-003 |
| FR-DM-011 | Upload attachment via shared service | High | BR-DOC-005 | UR-DOC-011 | UC-FA-DOC-005 | TC-AT-001 |
| FR-DM-012 | List attachments from shared service | High | BR-DOC-005 | UR-DOC-012 | UC-FA-DOC-005 | TC-AT-002 |
| FR-DM-013 | Legacy attachment backward compat | Medium | BR-DOC-005 | UR-DOC-013 | UC-FA-DOC-005 | TC-AT-003 |
| FR-VC-001 | Version creation | High | BR-DOC-002 | UR-DOC-014 | UC-FA-DOC-002 | TC-VC-001 |
| FR-VC-002 | Version comparison | Medium | BR-DOC-002 | UR-DOC-015 | UC-FA-DOC-002 | TC-VC-002, TC-VC-003 |
| FR-ET-001 | Expiry date management | High | BR-DOC-007 | UR-DOC-016 | UC-FA-DOC-002 | TC-ET-001, TC-ET-002 |
| FR-ET-002 | Expiry reminders | High | BR-DOC-007 | UR-DOC-017 | UC-FA-DOC-002 | TC-ET-003 |
| FR-AW-001 | Acknowledgment assignment | High | BR-DOC-006 | UR-DOC-018 | UC-FA-DOC-002 | TC-AW-001 |
| FR-AW-002 | Acknowledgment recording | High | BR-DOC-006 | UR-DOC-019 | UC-FA-DOC-002 | TC-AW-002 |
| FR-AW-003 | Acknowledgment status | High | BR-DOC-006 | UR-DOC-020 | UC-FA-DOC-002 | TC-AW-003 |
| FR-AW-004 | Pending documents display | High | BR-DOC-006 | UR-DOC-021 | UC-FA-DOC-002 | TC-AW-004 |
| FR-IN-001 | CRM integration via entity links | High | BR-DOC-003 | UR-DOC-022 | UC-FA-DOC-003 | TC-IN-001 |
| FR-IN-002 | HRM integration via entity links | High | BR-DOC-003 | UR-DOC-023 | UC-FA-DOC-003 | TC-IN-001 |
| FR-IN-003 | Task system integration | Medium | BR-DOC-003 | UR-DOC-024 | UC-FA-DOC-003 | TC-IN-002 |
| FR-IN-004 | Notification integration | High | BR-DOC-007 | UR-DOC-025 | UC-FA-DOC-002 | TC-IN-003 |
| FR-RP-001 | Document reports | Medium | BR-DOC-001 | UR-DOC-026 | UC-FA-DOC-001 | TC-RP-001 |
| FR-RP-002 | Acknowledgment reports | High | BR-DOC-006 | UR-DOC-027 | UC-FA-DOC-001 | TC-RP-002 |
| FR-SC-001 | Permission constants and RBAC | High | BR-DOC-004 | UR-DOC-028 | UC-FA-DOC-004 | TC-AC-001, TC-AC-002 |
| FR-SC-002 | Audit logging | High | All | UR-DOC-029 | UC-FA-DOC-001 | TC-SC-004 |

---

## 2. Coverage Summary

| Area | Total FRs | Tested | Coverage |
|------|-----------|--------|----------|
| Document Management | 7 | 7 | 100% |
| Multi-Entity Linking | 2 | 2 | 100% |
| Access Control | 1 | 1 | 100% |
| Version Control | 2 | 2 | 100% |
| Expiry Tracking | 2 | 2 | 100% |
| Acknowledgment Workflow | 4 | 4 | 100% |
| Attachment Management | 3 | 3 | 100% |
| Integration | 4 | 4 | 100% |
| Reporting | 2 | 2 | 100% |
| Security | 2 | 2 | 100% |
| **Total** | **29** | **29** | **100%** |

---

## 3. Test Case Mappings for New FRs

| New FR | Test Case | Description |
|--------|-----------|-------------|
| FR-DM-008 | TC-ML-001 | Link document to customer, project, and task simultaneously |
| FR-DM-008 | TC-ML-002 | Retrieve all links for a document; retrieve documents for an entity |
| FR-DM-009 | TC-ML-003 | Remove a single link; verify other links remain |
| FR-DM-010 | TC-ACL-001 | Owner can view and edit own document |
| FR-DM-010 | TC-ACL-002 | Group member can view; group member with DOC_MANAGE can edit |
| FR-DM-010 | TC-ACL-003 | User outside owner/group denied access |
| FR-DM-011 | TC-AT-001 | Upload attachment via ksf_FA_Attachments; verify fa_attachments record |
| FR-DM-012 | TC-AT-002 | List attachments; verify merged list from fa_attachments + legacy |
| FR-DM-013 | TC-AT-003 | Access legacy fa_document_attachments data (read-only) |

---

*Document Version: 2.0.0*
*Last Updated: 2026-06-28*
