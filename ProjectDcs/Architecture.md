# ksf_FA_Documents Technical Architecture

## Document Information
- **Module**: ksf_FA_Documents (Document Management)
- **Version**: 2.0.0
- **Date**: 2026-06-28
- **Status**: Updated
- **Author**: KSFII Development Team

## 1. Architecture Overview

### 1.1 Design Principles
The ksf_FA_Documents module follows these architectural principles:

1. **Modularity**: Clean separation between UI, business logic, and data layers
2. **Extensibility**: Hooks and events for integration with other modules
3. **Compatibility**: WebERP-style functions for FA integration
4. **Type Safety**: PHP 8.0+ features with type declarations
5. **Reusability**: Core library from ksf_Documents
6. **Multi-Entity Linking**: Documents link to many entity types simultaneously via a dedicated link table
7. **ACL Enforcement**: Owner/group-based access control atop FA RBAC

### 1.2 Technology Stack
- **PHP**: 8.0+ with strict typing
- **Database**: MySQL 5.7+ / MariaDB 10.0+
- **Frontend**: Bootstrap 5.x (via FA)
- **Core Library**: ksf_Documents (document entity)
- **Integration**: PSR-14 Event Dispatcher
- **Attachments**: ksf_FA_Attachments (shared service)

### 1.3 Module Dependencies
```
ksf_FA_Documents
  ├── ksf_FA_Attachments (shared attachment service)
  ├── ksf_Documents (core business logic)
  └── FrontAccounting 2.4+
```

## 2. Directory Structure

```
ksf_FA_Documents/
├── FA_Documents_Module.php       # Module registration & hooks
├── hooks.php                     # Install/activate/deactivate hooks
├── README.md                     # Module documentation
├── includes/
│   ├── documents_db.inc          # Document CRUD database functions
│   ├── links_db.inc              # Multi-entity link database functions
│   ├── documents_ui.inc          # UI components
│   └── AttachmentOperationsTrait.php  # Shared attachment operations trait
├── pages/
│   ├── index.php                 # Document list
│   ├── view.php                  # Document viewer
│   ├── upload.php                # Document upload
│   ├── acknowledgments.php       # Acknowledgment management
│   ├── reports.php               # Document reports
│   └── settings.php              # Module settings
├── sql/
│   ├── install.sql               # Database schema
│   └── migrate_v1_to_v2.sql      # Migration: entity columns -> fa_document_links
├── src/                          # Additional source files
├── tests/                        # Unit and integration tests
└── ProjectDcs/                   # Documentation
    ├── Architecture.md
    ├── Business Requirements.md
    ├── Functional Requirements.md
    ├── RTM.md
    ├── Test Plan.md
    ├── UAT Plan.md
    └── Use Case.md
```

## 3. Module Components

### 3.1 FA_Documents_Module.php
Main module class providing:
- Module metadata
- Permission definitions
- Menu items
- Lifecycle hooks (install, activate, deactivate, uninstall)

**Key Functions**:
```php
function fa_documents_get_module_info()    // Returns module metadata
function fa_documents_install()            // Creates database tables
function fa_documents_activate()           // Activates module
function fa_documents_deactivate()         // Deactivates module
function fa_documents_uninstall()          // Cleanup on uninstall
function fa_documents_get_menu_items()     // Returns navigation menu
```

### 3.2 hooks.php
Handles module lifecycle operations:
- Database installation (creates fa_documents, fa_document_links, preserves legacy fa_document_attachments)
- Permission registration
- Menu registration
- Hook registration
- Migration detection (v1->v2 schema upgrade)

### 3.3 documents_db.inc
Database abstraction layer with functions for:
- Document CRUD operations (with ACL-aware filtering)
- Version management (fa_document_versions)
- Acknowledgment tracking
- Expiry management
- ACL-aware queries (WHERE owner = ? OR group_id = ?)

### 3.4 links_db.inc (NEW)
Database functions for multi-entity linking:
- `link_document_to_entity(int $doc_id, string $entity_type, int $entity_id): void`
- `unlink_document_from_entity(int $doc_id, string $entity_type, int $entity_id): void`
- `get_entities_for_document(int $doc_id): array`
- `get_documents_for_entity(string $entity_type, int $entity_id): array`
- `replace_document_links(int $doc_id, array $links): void`

### 3.5 documents_ui.inc
UI helper functions:
- Form input generators
- Display components
- Dashboard widgets
- Document list display
- Acknowledgment forms
- Link editor UI (multi-entity picker)

### 3.6 AttachmentOperationsTrait.php (NEW)
Trait consumed by DocumentService providing:
- `uploadAttachment(Document $doc, array $file): Attachment`
- `getAttachments(Document $doc): array`
- `deleteAttachment(Document $doc, int $attachmentId): void`

All operations delegate to ksf_FA_Attachments with `source_type = 'document'` and `source_id = $doc->getId()`.

## 4. Database Schema

### 4.1 Core Tables

#### fa_documents
```sql
CREATE TABLE fa_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    filename VARCHAR(255),
    content LONGTEXT,
    version VARCHAR(20) DEFAULT '1.0',
    status VARCHAR(20) DEFAULT 'Active',
    expiry_date DATE,
    requires_acknowledgment TINYINT(1) DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- ACL columns (NEW in v2.0)
    owner INT DEFAULT NULL,           -- FK to FA users (fa_users.id)
    group_id INT DEFAULT NULL,        -- FK to FA security groups
    -- Legacy entity columns (DEPRECATED in v2.0, retained for backward compat)
    entity_type VARCHAR(50) DEFAULT NULL,
    entity_id INT DEFAULT NULL,
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_expiry (expiry_date),
    INDEX idx_owner (owner),
    INDEX idx_group (group_id)
);
```

#### fa_document_links (NEW in v2.0)
```sql
CREATE TABLE fa_document_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doc_id INT NOT NULL,
    entity_type VARCHAR(50) NOT NULL,  -- e.g. 'customer', 'project', 'task', 'training_course', 'employee'
    entity_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_link (doc_id, entity_type, entity_id),
    INDEX idx_doc (doc_id),
    INDEX idx_entity (entity_type, entity_id)
);
```

#### fa_document_versions
```sql
CREATE TABLE fa_document_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    version VARCHAR(20) NOT NULL,
    filename VARCHAR(255),
    content LONGTEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_document (document_id)
);
```

#### fa_document_acknowledgments
```sql
CREATE TABLE fa_document_acknowledgments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    user_id INT NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    acknowledged_at DATETIME,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_document (document_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);
```

### 4.2 Reference Tables

#### fa_document_types
```sql
CREATE TABLE fa_document_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    requires_acknowledgment TINYINT(1) DEFAULT 0,
    inactive TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0
);
```

### 4.3 Attachment Tables

#### fa_attachments (managed by ksf_FA_Attachments)
```sql
-- Table lives in ksf_FA_Attachments module, referenced by source_type='document'
CREATE TABLE fa_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_type VARCHAR(50) NOT NULL,   -- 'document'
    source_id INT NOT NULL,             -- fa_documents.id
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    mime_type VARCHAR(127),
    file_size INT,
    file_path VARCHAR(512),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_source (source_type, source_id)
);
```

#### fa_document_attachments (LEGACY, read-only in v2.0)
```sql
-- Kept for backward compatibility with v1.x data
-- New attachments use fa_attachments from ksf_FA_Attachments
CREATE TABLE fa_document_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_path VARCHAR(512),
    file_size INT,
    mime_type VARCHAR(127),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_document (document_id)
);
```

## 5. Multi-Entity Linking Architecture

### 5.1 Design
Documents can be linked to any number of entities across FA modules simultaneously via the `fa_document_links` table. This replaces the legacy `entity_type`/`entity_id` columns on `fa_documents`.

**Supported entity_type values**:
| Entity Type | Source Module | Description |
|-------------|--------------|-------------|
| `customer` | ksf_FA_CRM | Customer record |
| `project` | ksf_FA_Project | Project/task |
| `task` | ksf_FA_Tasks | Individual task |
| `employee` | ksf_FA_HRM | Employee record |
| `training_course` | ksf_FA_Training | Training course |
| `quote` | ksf_FA_Sales | Customer quote |
| `supplier` | ksf_FA_Purchasing | Supplier record |

### 5.2 Resolution Pattern
```php
// Link a document to multiple entities
$linksDb = new LinksDb($db);
$linksDb->linkDocumentToEntity($docId, 'customer', 42);
$linksDb->linkDocumentToEntity($docId, 'project', 7);
$linksDb->linkDocumentToEntity($docId, 'task', 101);

// Retrieve all entities for a document
$entities = $linksDb->getEntitiesForDocument($docId);
// Returns: [['entity_type' => 'customer', 'entity_id' => 42], ...]

// Replace all links atomically
$linksDb->replaceDocumentLinks($docId, [
    ['entity_type' => 'customer', 'entity_id' => 42],
    ['entity_type' => 'employee', 'entity_id' => 15],
]);
```

### 5.3 Migration
The migration script `sql/migrate_v1_to_v2.sql` reads existing `entity_type`/`entity_id` from `fa_documents` and inserts into `fa_document_links`, then marks the old columns as deprecated.

## 6. ACL Architecture

### 6.1 Permission Model
| Level | Requirement | Description |
|-------|-------------|-------------|
| View | User is owner OR in group OR has `DOC_VIEW_ALL` permission | Can view document metadata and content |
| Edit | User is owner OR has `DOC_MANAGE` permission with group access | Can edit document properties |
| Admin | User has `DOC_ADMIN` permission | Full control, bypasses owner/group |

### 6.2 ACL Checks
```php
// Gate function (called on every document access)
function document_acl_check(Document $doc, string $action): bool {
    $user = get_current_user();
    $user_id = $user['id'];
    $user_group = $user['group_id'];

    // Admin bypass
    if (has_permission('DOC_ADMIN', $user)) return true;

    // Owner always has view/edit on own docs
    if ($doc->getOwner() === $user_id) return true;

    // Group check
    if ($doc->getGroupId() !== null) {
        // User must be in the same group
        if ($user_group === $doc->getGroupId()) {
            if ($action === 'view') return true;
            if ($action === 'edit' && has_permission('DOC_MANAGE', $user)) return true;
        }
    }

    // Global permission fallback
    if ($action === 'view' && has_permission('DOC_VIEW_ALL', $user)) return true;

    return false;
}
```

### 6.3 Default Values
- New documents: `owner = current_user.id`, `group_id = current_user.group_id`
- Admins can reassign both owner and group_id on any document

## 7. Attachment Refactoring

### 7.1 Architecture
```
v1.x: fa_document_attachments (standalone table)
v2.0: fa_attachments (ksf_FA_Attachments shared service) via AttachmentOperationsTrait
        └── legacy: fa_document_attachments (read-only, backward compat)
```

### 7.2 AttachmentOperationsTrait
```php
trait AttachmentOperationsTrait {
    public function uploadAttachment(Document $document, array $file): Attachment {
        // Delegates to ksf_FA_Attachments\AttachmentService
        // Creates record in fa_attachments with source_type='document', source_id=$document->getId()
    }

    public function getAttachments(Document $document): array {
        // Queries fa_attachments WHERE source_type='document' AND source_id=?
        // Also queries fa_document_attachments for legacy data (read-only)
        // Merges both result sets
    }

    public function deleteAttachment(Document $document, int $attachmentId): void {
        // Delegates to ksf_FA_Attachments\AttachmentService
    }
}
```

### 7.3 Version-Level Attachments
Each `fa_document_versions` row can have its own attachments via `fa_attachments` with `source_type='document_version'` and `source_id=$version_id`.

## 8. Integration Architecture

### 8.1 FrontAccounting Integration
The module integrates with FA through:

1. **Hooks System**: Using FA's hook mechanism
```php
add_action('document_created', $callback);
add_action('document_updated', $callback);
add_hook('documents.event', $callback);
```

2. **Database Table Prefix**: Using `TB_PREF` constant
```php
$sql = "SELECT * FROM " . TB_PREF . "fa_documents";
```

3. **Permission System**: Using FA's permission constants
```php
define('DOC_VIEW', 'DOC_VIEW');
define('DOC_MANAGE', 'DOC_MANAGE');
define('DOC_ADMIN', 'DOC_ADMIN');
```

4. **UI Components**: Using FA's form helpers
```php
text_input($name, $value, $maxlen);
file_input($name);
```

### 8.2 ksf_FA_Attachments Integration
- **Required dependency**: Module will not activate if ksf_FA_Attachments is absent
- **Version pin**: ksf_FA_Attachments >= 2.0.0
- **Service injection**: `AttachmentService` obtained via service container

### 8.3 HRM Integration
Documents module connects with HRM:
- Employee records store document links via fa_document_links
- Employee documents accessible from HRM profile
- HRM triggers document creation on hire

### 8.4 Task System Integration
```
Document Upload → Link to Task(s) → Employee Acknowledgment → Complete Task
```

### 8.5 Notification Integration
- Expiry reminders via email
- New document notifications
- Acknowledgment confirmation

## 9. Security Architecture

### 9.1 Input Validation
- All user inputs validated before database operations
- SQL injection prevention via `db_escape()`
- Type casting for numeric inputs
- File type validation

### 9.2 Output Escaping
- HTML output escaped via `htmlspecialchars()`
- JavaScript sanitization for dynamic content

### 9.3 File Upload Security
- Allowed file type checking
- Maximum file size limits
- Secure filename storage
- Directory access restrictions

### 9.4 Access Control
- ACL checks (owner + group) on all document CRUD operations
- Permission checks on all CRUD operations
- Role-based menu visibility
- Audit logging for sensitive operations

## 10. Extension Points

### 10.1 Custom Document Types
Modules can extend document types:
- Add new types
- Configure acknowledgment requirements
- Set custom expiry rules

### 10.2 Custom Workflows
The module supports:
- Custom approval workflows
- Custom reminder intervals
- Custom notification templates

### 10.3 Custom Integrations
- Webhook support for external systems
- API extensions for mobile apps
- Custom report exports

## 11. API Design

### 11.1 Document Entity (from ksf_Documents)
```php
class Document {
    // Types
    const TYPE_POLICY = 'Policy';
    const TYPE_CONTRACT = 'Contract';
    const TYPE_FORM = 'Form';
    const TYPE_TRAINING = 'Training';
    const TYPE_HANDBOOK = 'Handbook';
    const TYPE_OTHER = 'Other';

    // Methods
    public function getId(): ?int;
    public function getTitle(): string;
    public function getType(): string;
    public function isActive(): bool;
    public function requiresAcknowledgment(): bool;
    public function getOwner(): ?int;
    public function getGroupId(): ?int;
}
```

### 11.2 Service Layer Pattern
```php
class DocumentService {
    use AttachmentOperationsTrait;

    public function createDocument(array $data): Document
    public function getDocument(int $id): ?Document
    public function updateDocument(int $id, array $data): Document
    public function deleteDocument(int $id): bool

    public function createVersion(int $docId, array $data): DocumentVersion
    public function getVersions(int $docId): array

    public function acknowledge(int $docId, int $userId): Acknowledgment
    public function getUserAcknowledgments(int $userId): array

    // Multi-link
    public function linkDocument(int $docId, string $entityType, int $entityId): void
    public function unlinkDocument(int $docId, string $entityType, int $entityId): void
    public function getDocumentLinks(int $docId): array

    // ACL
    public function setDocumentOwner(int $docId, int $ownerId): void
    public function setDocumentGroup(int $docId, int $groupId): void
}
```

### 11.3 Repository Pattern
```php
class DocumentRepository {
    public function findById(int $id): ?Document
    public function findByType(string $type): array
    public function findExpiring(DateTime $date): array
    public function findPending(int $userId): array
    public function findByOwner(int $userId): array
    public function findByGroup(int $groupId): array
    public function findByEntity(string $entityType, int $entityId): array
}
```

## 12. Migration Path (v1.x -> v2.0)

### 12.1 Schema Migrations
1. Add `owner`, `group_id` columns to `fa_documents`
2. Create `fa_document_links` table
3. Migrate existing `entity_type`/`entity_id` data to `fa_document_links`
4. Keep `fa_document_attachments` as-is (read-only)

### 12.2 Code Migrations
1. Replace all direct `fa_document_attachments` writes with `AttachmentOperationsTrait`
2. Replace single-entity column reads with `links_db.inc` methods
3. Add ACL checks to all DocumentService methods

## 13. Performance Considerations

### 13.1 Database Indexes
Key indexes on:
- `type` - Document type filtering
- `status` - Status filtering
- `expiry_date` - Expiry queries
- `created_at` - Date range queries
- `owner` - ACL filtering
- `group_id` - ACL filtering
- `fa_document_links (doc_id)` - Link lookups
- `fa_document_links (entity_type, entity_id)` - Entity lookups

### 13.2 Query Optimization
- Pagination for large datasets
- Efficient JOINs with proper indexes
- Prepared statements for repeated queries

### 13.3 File Storage
- FA attachments directory (via ksf_FA_Attachments)
- Streaming for large files
- Compression for text content

## 14. Error Handling

### 14.1 Exception Hierarchy
```php
class DocumentsException extends Exception
class DocumentsDatabaseException extends DocumentsException
class DocumentsNotFoundException extends DocumentsException
class DocumentsValidationException extends DocumentsException
class DocumentsUploadException extends DocumentsException
class DocumentsAclException extends DocumentsException
```

### 14.2 Error Logging
- Database errors logged with query details
- Upload failures logged
- Access violations logged (including ACL denials)
- Acknowledgment errors logged

## 15. Deployment

### 15.1 Installation Process
1. Copy module files to FA modules directory
2. Ensure ksf_FA_Attachments module is installed and activated
3. Install via FA module manager
4. Database tables created automatically
5. Permissions assigned to admin
6. Menu items registered

### 15.2 Upgrade Process (v1.x -> v2.0)
1. Backup database
2. Deactivate module
3. Replace files
4. Run migration script (`sql/migrate_v1_to_v2.sql`)
5. Activate module
6. Verify data integrity

### 15.3 Uninstall Process
1. Deactivate module
2. Optionally remove data
3. Delete module files

---
*Document Version: 2.0.0*
*Last Updated: 2026-06-28*
