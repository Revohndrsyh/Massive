import sqlite3
import re

conn = sqlite3.connect(r'c:\Users\lathi\OneDrive\Dokumen\Skripsi Massive\database\database.sqlite')
cursor = conn.cursor()

output_lines = []
output_lines.append('-- MySQL compatible dump from SQLite')
output_lines.append('-- Generated for import into MySQL/phpMyAdmin')
output_lines.append('')
output_lines.append('SET FOREIGN_KEY_CHECKS = 0;')
output_lines.append('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";')
output_lines.append('SET AUTOCOMMIT = 0;')
output_lines.append('START TRANSACTION;')
output_lines.append('')

# Get all tables
cursor.execute("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")
tables = cursor.fetchall()

for table_name, create_sql in tables:
    if create_sql is None:
        continue
    
    # Convert CREATE TABLE syntax
    sql = create_sql
    
    # Replace double quotes with backticks
    sql = sql.replace('"', '`')
    
    # Replace 'integer primary key autoincrement not null' with MySQL syntax
    sql = re.sub(
        r'`(\w+)` integer primary key autoincrement not null',
        r'`\1` INT AUTO_INCREMENT PRIMARY KEY',
        sql, flags=re.IGNORECASE
    )
    
    # Replace 'integer' with 'INT'
    sql = re.sub(r'\binteger\b', 'INT', sql, flags=re.IGNORECASE)
    
    # Replace 'numeric' with 'DECIMAL(10,4)'
    sql = re.sub(r'\bnumeric\b', 'DECIMAL(10,4)', sql, flags=re.IGNORECASE)
    
    # Replace 'datetime' with 'DATETIME'
    sql = re.sub(r'\bdatetime\b', 'DATETIME', sql, flags=re.IGNORECASE)
    
    # Replace varchar without length to VARCHAR(255)
    sql = re.sub(r'\bvarchar\b(?!\s*\()', 'VARCHAR(255)', sql, flags=re.IGNORECASE)
    
    # Replace 'text' with 'LONGTEXT'
    sql = re.sub(r'\btext\b', 'LONGTEXT', sql, flags=re.IGNORECASE)
    
    # Add IF NOT EXISTS
    sql = sql.replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', 1)
    
    # Add ENGINE - only remove the very last closing paren
    last_paren = sql.rfind(')')
    if last_paren != -1:
        sql = sql[:last_paren] + ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    
    output_lines.append('-- --------------------------------------------------------')
    output_lines.append(f'-- Table structure for table `{table_name}`')
    output_lines.append('-- --------------------------------------------------------')
    output_lines.append(f'DROP TABLE IF EXISTS `{table_name}`;')
    output_lines.append(sql + ';')
    output_lines.append('')
    
    # Get data
    cursor.execute(f'SELECT * FROM "{table_name}"')
    rows = cursor.fetchall()
    
    if rows:
        # Get column names
        col_names = [desc[0] for desc in cursor.description]
        cols = ', '.join([f'`{c}`' for c in col_names])
        
        output_lines.append(f'-- Dumping data for table `{table_name}`')
        
        for row in rows:
            vals = []
            for v in row:
                if v is None:
                    vals.append('NULL')
                elif isinstance(v, (int, float)):
                    vals.append(str(v))
                else:
                    escaped = str(v).replace("\\", "\\\\").replace("'", "\\'")
                    vals.append(f"'{escaped}'")
            values_str = ', '.join(vals)
            output_lines.append(f'INSERT INTO `{table_name}` ({cols}) VALUES({values_str});')
        output_lines.append('')

# Get indexes
cursor.execute("SELECT sql FROM sqlite_master WHERE type='index' AND sql IS NOT NULL AND name NOT LIKE 'sqlite_%'")
indexes = cursor.fetchall()

if indexes:
    output_lines.append('-- --------------------------------------------------------')
    output_lines.append('-- Indexes')
    output_lines.append('-- --------------------------------------------------------')
    for (idx_sql,) in indexes:
        sql = idx_sql.replace('"', '`')
        sql = re.sub(r'\bon\b', 'ON', sql, count=1, flags=re.IGNORECASE)
        output_lines.append(sql + ';')
    output_lines.append('')

output_lines.append('SET FOREIGN_KEY_CHECKS = 1;')
output_lines.append('COMMIT;')

conn.close()

with open(r'c:\Users\lathi\OneDrive\Dokumen\Skripsi Massive\database\database.sql', 'w', encoding='utf-8') as f:
    f.write('\n'.join(output_lines))

print('Done! MySQL-compatible SQL generated.')
print(f'Total lines: {len(output_lines)}')
