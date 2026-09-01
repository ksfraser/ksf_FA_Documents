<!-- Repo-specific appendix to the shared AGENTS.md. Generic conventions live in AGENTS_ARCH.md (hardlinked). -->

```markdown
# ksf_FA_Documents
## Architecture Overview
**FA Module** for Document Management - attach, version, and organize documents linked to transactions/contacts.
## Repository Structure
```
ksf_FA_Documents/
├── sql/
│   ├── fa_documents.sql
│   └── fa_document_links.sql
├── includes/
│   ├── documents_db.inc
│   └── links_db.inc
├── pages/
├── hooks.php
├── composer.json
└── ProjectDocs/
```
## Dependencies
- **ksf_FA_Documents_Core** (business logic)
- **ksf_FA_CRM** (link docs to contacts)
- **FrontAccounting 2.4+**
```
