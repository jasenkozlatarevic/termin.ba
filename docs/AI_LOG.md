# AI Usage Log — Termin.ba

# Milestone 2

Tool: ChatGPT. Prompts: “Implement a PDO BaseDao with CRUD”; “Create a MySQL schema with constraints and sample data.”

# AI errors and fixes

1. AI put DB credentials in `Database.php`. I spotted them during code review and moved them to ignored `config.php`, with `config.example.php` as a template.
2. AI-described CRUD coverage was too broad: `test-crud.php` tests only `UserDao`. I found this by reviewing the script and corrected the log to state the limitation; other DAOs still need CRUD checks.
 