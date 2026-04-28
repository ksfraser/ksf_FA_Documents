# FA_Documents - FrontAccounting Document Management

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-777bb6)
![FA](https://img.shields.io/badge/FrontAccounting-2.4.x-green)
![License](https://img.shields.io/badge/license-GPL--3.0-orange)

## Overview

FA_Documents is a document management module for FrontAccounting that enables storage, versioning, and tracking of employee documents including contracts, forms, policies, and training materials. Integrates with the HRM and task systems for document acknowledgment and signing workflows.

### Features

- **Document Storage** - Store employment documents (contracts, forms, policies)
- **Version Control** - Track document versions over time
- **Expiry Tracking** - Monitor document expiry dates
- **Acknowledgment Workflow** - Employee document signing/acknowledgment
- **Reminder System** - Automated expiry reminder notifications
- **Document Linking** - Link documents to employee records
- **Task Integration** - Integrate with task system for workflow

### Status

**IMPLEMENTED** - Production Ready

- Document entity management
- Version control system
- Expiry date tracking
- Acknowledgment tracking
- Integration with HRM module
- Task system integration for workflows

## Quick Start

### Installation

1. **Copy module files**:
```bash
cp -r FA_Documents /path/to/frontaccounting/modules/
```

2. **Install via FrontAccounting**:
- Go to Administrator > Modules > Install Modules
- Find FA_Documents and click Install

3. **Database tables** are created automatically on install

4. **Assign permissions** to users via Administrator > User Roles

### Using the Module

Access via the Documents menu after installation:

- **All Documents** - Browse and search documents
- **My Documents** - Documents requiring acknowledgment
- **Upload** - Upload new documents
- **Reports** - Document reports and analytics
- **Settings** - Document type configuration

## Database Tables

### Core Tables

| Table | Description |
|-------|-------------|
| `fa_documents` | Document records with metadata |
| `fa_document_versions` | Version history |
| `fa_document_acknowledgments` | Acknowledgment tracking |

### Reference Tables

| Table | Description |
|-------|-------------|
| `fa_document_types` | Document type definitions |

### Document Types

Default types:
- Policy
- Contract
- Form
- Training
- Handbook
- Other

## Permissions

| Permission | Description |
|------------|-------------|
| `DOC_VIEW` | View documents |
| `DOC_MANAGE` | Manage documents |
| `DOC_UPLOAD` | Upload documents |
| `DOC_ACKNOWLEDGE` | Acknowledge documents |
| `DOC_ADMIN` | Full administrative access |

## API Reference

### Document Entity

```php
use Ksfraaser\Documents\Entity\Document;

// Document types
Document::TYPE_POLICY    // Policy document
Document::TYPE_CONTRACT // Contract document
Document::TYPE_FORM      // Form document
Document::TYPE_TRAINING // Training document
Document::TYPE_HANDBOOK // Handbook document
Document::TYPE_OTHER    // Other document

// Check document status
$document->isActive();                           // Check if active
$document->requiresAcknowledgment();            // Check if acknowledgment required

// Get document properties
$document->getId();         // Get document ID
$document->getTitle();     // Get document title
$document->getType();      // Get document type
$document->getFilename();  // Get filename
$document->getVersion();   // Get version
$document->getStatus();    // Get status
$document->getCreatedBy(); // Get creator ID
$document->getCreatedAt(); // Get creation date
```

### Module Functions

```php
// Document management
create_document($data);
get_document($id);
update_document($id, $data);
delete_document($id);
list_documents($filters);

// Version management
create_document_version($document_id, $version_data);
get_document_versions($document_id);

// Acknowledgment tracking
acknowledge_document($document_id, $user_id);
get_user_acknowledgments($user_id);
get_pending_acknowledgments($user_id);

// Expiry tracking
get_expiring_documents($days_ahead);
send_expiry_reminders();
```

## Document Workflows

### 1. Document Upload Workflow
1. HR uploads new document with type and expiry date
2. System creates initial version
3. If acknowledgment required, assign to employees
4. Employees receive notification
5. Employees acknowledge document
6. System records acknowledgment with timestamp

### 2. Document Renewal Workflow
1. System identifies expiring document
2. HR uploads updated version
3. System creates new version
4. Reset acknowledgment status for all employees
5. Send reminder notifications

### 3. Acknowledgment Workflow
1. Employee logs in
2. Views "My Documents" section
3. Views pending documents requiring acknowledgment
4. Opens and reviews document
5. Clicks "Acknowledge" button
6. System records acknowledgment with timestamp

## Integration

### HRM Integration
- Link documents to employee records
- Retrieve employee document history
- Track employee certifications

### Task System Integration
- Create acknowledgment tasks automatically
- Track task completion
- Calculate task times

### Notification System
- Expiry reminder emails
- New document notifications
- Acknowledgment confirmation

## Development

### File Structure

```
FA_Documents/
├── FA_Documents_Module.php  # Module registration
├── hooks.php                # Installation hooks
├── includes/
│   ├── doc_db.inc         # Database functions
│   └── doc_ui.inc         # UI components
├── pages/
│   ├── index.php         # Document list
│   ├── view.php         # Document viewer
│   ├── upload.php       # Document upload
│   └── acknowledgments.php
├── sql/
│   └── install.sql       # Schema
└── tests/
    └── DocumentTest.php
```

### Testing

```bash
# Run unit tests
./vendor/bin/phpunit tests/
```

## Requirements

- FrontAccounting 2.4.0+
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.0+
- ksf_Documents library

## Version History

| Version | Changes |
|---------|---------|
| 1.0.0 | Initial release with document management |

## License

Copyright (C) 2024 KSFII Development Team

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

## Documentation

Full documentation is available in `ProjectDcs/`:

- [Functional Requirements](ProjectDcs/Functional%20Requirements.md)
- [Architecture](ProjectDcs/Architecture.md)
- [Test Plan](ProjectDcs/Test%20Plan.md)
- [UAT Plan](ProjectDcs/UAT%20Plan.md)

---
*FA_Documents Module v1.0.0*
*For FrontAccounting 2.4.x*
