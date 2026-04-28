# FA_Documents Technical Architecture

## Document Information
- **Module**: FA_Documents (Document Management)
- **Version**: 1.0.0
- **Date**: 2024-04-26
- **Status**: Implemented
- **Author**: KSFII Development Team

## 1. Architecture Overview

### 1.1 Design Principles
The FA_Documents module follows these architectural principles:

1. **Modularity**: Clean separation between UI, business logic, and data layers
2. **Extensibility**: Hooks and events for integration with other modules
3. **Compatibility**: WebERP-style functions for FA integration
4. **Type Safety**: PHP 8.0+ features with type declarations
5. **Reusability**: Core library from ksf_Documents

### 1.2 Technology Stack
- **PHP**: 8.0+ with strict typing
- **Database**: MySQL 5.7+ / MariaDB 10.0+
- **Frontend**: Bootstrap 5.x (via FA)
- **Core Library**: ksf_Documents (document entity)
- **Integration**: PSR-14 Event Dispatcher

## 2. Directory Structure

```
FA_Documents/
├── FA_Documents_Module.php      # Module registration & hooks
├── hooks.php                   # Install/activate/deactivate hooks
├── README.md                  # Module documentation
├── includes/
│   ├── doc_db.inc            # Database functions
│   └── doc_ui.inc            # UI components
├── pages/
│   ├── index.php            # Document list
│   ├── view.php             # Document viewer
│   ├── upload.php           # Document upload
│   ├── acknowledgments.php  # Acknowledgment management
│   ├── reports.php          # Document reports
│   └── settings.php          # Module settings
├── sql/
│   └── install.sql           # Database schema
├── src/                      # Additional source files
├── tests/                    # Unit and integration tests
└── ProjectDcs/               # Documentation
    ├── Functional Requirements.md
    ├── Architecture.md
    ├── Test Plan.md
    └── UAT Plan.md
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
function fa_documents_activate()          // Activates module
function fa_documents_deactivate()        // Deactivates module
function fa_documents_uninstall()        // Cleanup on uninstall
function fa_documents_get_menu_items()    // Returns navigation menu
```

### 3.2 hooks.php
Handles module lifecycle operations:
- Database installation
- Permission registration
- Menu registration
- Hook registration

### 3.3 doc_db.inc
Database abstraction layer with functions for:
- Document CRUD operations
- Version management
- Acknowledgment tracking
- Expiry management

### 3.4 doc_ui.inc
UI helper functions:
- Form input generators
- Display components
- Dashboard widgets
- Document list display
- Acknowledgment forms

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
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_expiry (expiry_date)
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

## 5. Integration Architecture

### 5.1 FrontAccounting Integration
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
```

4. **UI Components**: Using FA's form helpers
```php
text_input($name, $value, $maxlen);
file_input($name);
```

### 5.2 HRM Integration
Documents module connects with HRM:
- Employee records store document links
- Employee documents accessible from HRM profile
- HRM triggers document creation on hire

### 5.3 Task System Integration
```
Document Upload → Create Task(s) → Employee Acknowledgment → Complete Task
```

### 5.4 Notification Integration
- Expiry reminders via email
- New document notifications
- Acknowledgment confirmation

## 6. Security Architecture

### 6.1 Input Validation
- All user inputs validated before database operations
- SQL injection prevention via `db_escape()`
- Type casting for numeric inputs
- File type validation

### 6.2 Output Escaping
- HTML output escaped via `htmlspecialchars()`
- JavaScript sanitization for dynamic content

### 6.3 File Upload Security
- Allowed file type checking
- Maximum file size limits
- Secure filename storage
- Directory access restrictions

### 6.4 Access Control
- Permission checks on all CRUD operations
- Role-based menu visibility
- Audit logging for sensitive operations

## 7. Extension Points

### 7.1 Custom Document Types
Modules can extend document types:
- Add new types
- Configure acknowledgment requirements
- Set custom expiry rules

### 7.2 Custom Workflows
The module supports:
- Custom approval workflows
- Custom reminder intervals
- Custom notification templates

### 7.3 Custom Integrations
- Webhook support for external systems
- API extensions for mobile apps
- Custom report exports

## 8. Performance Considerations

### 8.1 Database Indexes
Key indexes on:
- `type` - Document type filtering
- `status` - Status filtering
- `expiry_date` - Expiry queries
- `created_at` - Date range queries

### 8.2 Query Optimization
- Pagination for large datasets
- Efficient JOINs with proper indexes
- Prepared statements for repeated queries

### 8.3 File Storage
- FA attachments directory
- Streaming for large files
- Compression for text content

## 9. API Design

### 9.1 Document Entity (from ksf_Documents)
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
}
```

### 9.2 Service Layer Pattern
```php
class DocumentService {
    public function createDocument(array $data): Document
    public function getDocument(int $id): ?Document
    public function updateDocument(int $id, array $data): Document
    public function deleteDocument(int $id): bool
    
    public function createVersion(int $docId, array $data): DocumentVersion
    public function getVersions(int $docId): array
    
    public function acknowledge(int $docId, int $userId): Acknowledgment
    public function getUserAcknowledgments(int $userId): array
}
```

### 9.3 Repository Pattern
```php
class DocumentRepository {
    public function findById(int $id): ?Document
    public function findByType(string $type): array
    public function findExpiring(DateTime $date): array
    public function findPending(int $userId): array
}
```

## 10. Error Handling

### 10.1 Exception Hierarchy
```php
class DocumentsException extends Exception
class DocumentsDatabaseException extends DocumentsException
class DocumentsNotFoundException extends DocumentsException
class DocumentsValidationException extends DocumentsException
class DocumentsUploadException extends DocumentsException
```

### 10.2 Error Logging
- Database errors logged with query details
- Upload failures logged
- Access violations logged
- Acknowledgment errors logged

## 11. Testing Strategy

### 11.1 Unit Tests
- Document entity tests
- Database function tests
- UI component tests
- Version management tests

### 11.2 Integration Tests
- FA module integration
- Database operations
- HRM integration
- Task system integration

### 11.3 Test Coverage
- Core CRUD operations
- Version workflows
- Acknowledgment tracking
- Expiry calculations

## 12. Deployment

### 12.1 Installation Process
1. Copy module files to FA modules directory
2. Install via FA module manager
3. Database tables created automatically
4. Permissions assigned to admin
5. Menu items registered

### 12.2 Upgrade Process
1. Backup database
2. Deactivate module
3. Replace files
4. Activate module
5. Run migration scripts (if any)

### 12.3 Uninstall Process
1. Deactivate module
2. Optionally remove data
3. Delete module files

---
*Document Version: 1.0.0*
*Last Updated: 2024-04-26*
